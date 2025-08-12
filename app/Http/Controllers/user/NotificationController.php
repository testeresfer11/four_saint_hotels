<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : getList
     * createdDate  : 22-08-2025
     * purpose      : Get the list of notification
     */
    public function getNotifications(Request $request)
    {
        try {
            auth()->user()->notifications()->update(['read_at' => date('Y-m-d H:i:s')]);
            $notifications = auth()->user()->notifications()->paginate(10);
            return $this->apiResponse('success', 200, 'All notifications fetched successfully', $notifications);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End method getList**/

    /**
     * functionName : delete
     * createdDate  : 22-08-2025
     * purpose      : delete the notifications
     */
    public function delete($id)
    {
        try {
            if ($id == "clear") {
                DB::table('notifications')->delete();
            } else {
                DB::table('notifications')->where('id', $id)->delete();
            }
            return $this->apiResponse('success', 200, 'Notification ' . config('constants.SUCCESS.DELETE_DONE'));
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End method delete**/

    /**
     * functionName : notificationRead
     * createdDate  : 22-08-2025
     * purpose      : read the notifications
     */
    public function notificationRead($id)
    {
        try {
            DB::table('notifications')->where('id', $id)->update(['read_at' => date('Y-m-d H:i:s')]);
            // auth()->user()->notifications()->update(['read_at'=> date('Y-m-d H:i:s')]);
            return $this->apiResponse('success', 200, 'Notification ' . config('constants.SUCCESS.READ_DONE'));
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End method read**/
}
