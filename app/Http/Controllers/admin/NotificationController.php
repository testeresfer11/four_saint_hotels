<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Validator};
use App\Models\{PushNotification,User};

class NotificationController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : getList
     * createdDate  : 28-08-2024
     * purpose      : Get the list of notification
    */
    public function getList(Request $request){
        try{
            auth()->user()->notifications()->update(['read_at'=> date('Y-m-d H:i:s')]);
            $notifications = auth()->user()->notifications()->paginate(10);
            return view("admin.notification.list",compact('notifications'));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

    /**
     * functionName : delete
     * createdDate  : 28-08-2024
     * purpose      : delete the notifications
    */
    public function delete( $id){
        try{
            if($id == "clear"){
                DB::table('notifications')->delete();
            }else{
                DB::table('notifications')->where('id', $id)->delete();
            }
            return $this->apiResponse('success',200,'Notification '.config('constants.SUCCESS.DELETE_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /**End method delete**/

    /**
     * functionName : notificationRead
     * createdDate  : 28-08-2024
     * purpose      : read the notifications
    */
    public function notificationRead($id){
        try{
            DB::table('notifications')->where('id', $id)->update(['read_at'=> date('Y-m-d H:i:s')]);
            // auth()->user()->notifications()->update(['read_at'=> date('Y-m-d H:i:s')]);
            return $this->apiResponse('success',200,'Notification '.config('constants.SUCCESS.READ_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /**End method read**/

  public function getPushNotificationList()
    {
        $notifications = PushNotification::with('receiver')->latest()->paginate(10);
        return view('admin.pushnotification.list', compact('notifications'));
    }

public function add(Request $request)
{
    try {
        if ($request->isMethod('get')) {
            $users = User::where('status', 1)->get(['id', 'full_name', 'email']);
            return view('admin.pushnotification.add', compact('users'));
        }

        if ($request->isMethod('post')) {
            // Validate
            $validator = Validator::make($request->all(), [
                'title'             => 'required|string|max:255',
                'body'              => 'required|string|max:1000',
                'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type'              => 'nullable|string|max:50',
                'notification_type' => 'nullable|string|max:50',
                'send_to'           => 'required|in:single,multiple,all',
                'receiver_id'       => 'required_if:send_to,single|exists:users,id',
                'receiver_ids'      => 'required_if:send_to,multiple|array',
                'receiver_ids.*'    => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Upload image if provided
           // Upload image if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('push_notifications', 'public');
                $imagePath = asset('storage/' . $path); // Store full URL
            }


            // Determine recipients
            if ($request->send_to === 'single') {
                $receiverIds = [$request->receiver_id];
            } elseif ($request->send_to === 'multiple') {
                $receiverIds = $request->receiver_ids;
            } elseif ($request->send_to === 'all') {
                $receiverIds = User::where('status', 1)->pluck('id')->toArray();
            } else {
                $receiverIds = [];
            }

            // Store one record with all receiver IDs in array format
            $pushNotification = PushNotification::create([
                'receiver_id'       => json_encode($receiverIds), // store as JSON array
                'title'             => $request->title,
                'body'              => $request->body,
                'image'             => $imagePath,
                'type'              => $request->type,
                'notification_type' => $request->notification_type,
            ]);

            // Send push notifications to each user in the array
            foreach ($receiverIds as $userId) {


                 PushNotification::create([
                    'receiver_id' => $userId,
                    'title' => $request->title,
                    'body' => $request->body,
                    'type' => $request->type,
                    'notification_type' => $request->notification_type,
                    'image' => $imagePath,
                ]);

                $this->sendPushNotification(
                    $request->title,
                    $request->body,
                    $request->type,
                    $request->notification_type,
                    $userId
                );
            }

            return redirect()
                ->route('admin.pushnotification.list')
                ->with('success', 'Push notification sent successfully.');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}



public function edit(Request $request, $id)
{
    try {
        $pushNotification = PushNotification::findOrFail($id);

        if ($request->isMethod('get')) {
            $users = User::where('status', 1)->get(['id', 'full_name', 'email']);

            // Determine selected receivers for form prefill
            if ($pushNotification->receiver_ids) {
                $selectedReceivers = json_decode($pushNotification->receiver_ids, true);
            } elseif ($pushNotification->receiver_id) {
                $selectedReceivers = [$pushNotification->receiver_id];
            } else {
                $selectedReceivers = [];
            }

            return view('admin.pushnotification.edit', compact('pushNotification', 'users', 'selectedReceivers'));
        }

      if ($request->isMethod('post')) {
    $validator = Validator::make($request->all(), [
        'title'             => 'required|string|max:255',
        'body'              => 'required|string|max:1000',
        'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'send_to'           => 'required|in:single,multiple,all',
        'receiver_id'       => 'required_if:send_to,single|exists:users,id',
        'receiver_ids'      => 'required_if:send_to,multiple|array',
        'receiver_ids.*'    => 'exists:users,id',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Image
    $imagePath = $pushNotification->image;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('push_notifications', 'public');
        $imagePath = asset('storage/' . $path);
    }

    if ($request->send_to === 'single') {
        // Update current notification for one user
        $pushNotification->update([
            'receiver_id'       => $request->receiver_id,
            'title'             => $request->title,
            'body'              => $request->body,
            'image'             => $imagePath,
            'type'              => $request->type ?? null,
            'notification_type' => $request->notification_type ?? null,
        ]);

    } elseif ($request->send_to === 'multiple') {
        // Delete the current one and create new for each user
        $pushNotification->delete();

        foreach ($request->receiver_ids as $userId) {
            PushNotification::create([
                'receiver_id'       => $userId,
                'title'             => $request->title,
                'body'              => $request->body,
                'image'             => $imagePath,
                'type'              => $request->type ?? null,
                'notification_type' => $request->notification_type ?? null,
            ]);
        }

    } elseif ($request->send_to === 'all') {
        $pushNotification->delete();

        $allUsers = User::where('status', 1)->pluck('id');
        foreach ($allUsers as $userId) {
            PushNotification::create([
                'receiver_id'       => $userId,
                'title'             => $request->title,
                'body'              => $request->body,
                'image'             => $imagePath,
                'type'              => $request->type ?? null,
                'notification_type' => $request->notification_type ?? null,
            ]);
        }
    }

    return redirect()
        ->route('admin.pushnotification.list')
        ->with('success', 'Push notification updated successfully.');
}

    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}


/**
 * Helper to check JSON string
 */
private function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}


 public function deletePushNotification($id)
    {
        try{
        $notification = PushNotification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Push Notification deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    
    }

}
}
