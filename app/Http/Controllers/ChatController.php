<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    use SendResponseTrait;

    /**
     * Create or get existing one-to-one conversation and store message
     */
  public function storeMessage(Request $request)
{
    $validator = Validator::make($request->all(), [
        'sender_id' => 'required|exists:users,id',
        'receiver_id' => 'required|exists:users,id',
        'message' => 'required', // Required, could be file or string
        'conversation_id' => 'nullable|exists:conversations,id',
        'type' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation Error',
            'errors' => $validator->messages(),
        ], 422);
    }

    try {
        Log::info('Message Payload:', $request->all());

        // Find or create conversation
        if ($request->filled('conversation_id')) {
            $conversation = Conversation::find($request->conversation_id);
            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation not found',
                ], 404);
            }
        } else {
            $userOne = min($request->sender_id, $request->receiver_id);
            $userTwo = max($request->sender_id, $request->receiver_id);
            $conversation = Conversation::firstOrCreate([
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ]);
        }

        $finalMessage = '';

        if ($request->hasFile('message')) {
            $file = $request->file('message');
            $path = $file->store('public/messages');
            $url = asset(str_replace('public/', 'storage/', $path));
            $finalMessage = $url;
        } else {
            $finalMessage = $request->input('message');
        }

        if (empty($finalMessage)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message cannot be empty.',
            ], 422);
        }

       
        $message = $conversation->messages()->create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $finalMessage,
            'type' => $request->type,

       ]);


        if($request->type == 'text'){
            $type = 'new_message';
        }else{
            $type = 'image';
        }


         $notificationData = [
            'title' => 'New Message',
            'body' => $finalMessage,
            'type' => $type,
            'notification_type' => 'message'  
        ];


       /*$y= $this->sendPushNotification(
            $notificationData['title'],
            $notificationData['body'],
            $notificationData['type'],
            $notificationData['notification_type'],
            $request->receiver_id
        );*/



        return response()->json([
            'status' => 'success',
            'message' => 'Message stored successfully',
            'data' => [
                'conversation_id' => $conversation->id,
                'message' => $message,


               /* 'jshfs'   => $y,*/
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('Store Message Error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong.',
        ], 500);
    }
}

public function uploadFile(Request $request)
{
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');
    $path = $file->store('chat_files', 'public');

    // Full URL to the uploaded file
    $fullUrl = url('storage/' . $path);

    return response()->json(['file_url' => $fullUrl]);
}


    /**
     * Get all messages in a conversation
     */
   public function getMessages($conversationId){
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return $this->apiResponse('error', 500, 'Conversation not found.');
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        $data = [
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ];

        return $this->apiResponse('success', 200, 'Messages fetched', $data);
    }

}
