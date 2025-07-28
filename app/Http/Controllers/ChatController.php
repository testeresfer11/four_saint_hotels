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
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->messages());
        }

        try {
            $userOne = min($request->sender_id, $request->receiver_id);
            $userTwo = max($request->sender_id, $request->receiver_id);

            $conversation = Conversation::firstOrCreate([
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ]);

            $message = $conversation->messages()->create([
                'sender_id' => $request->sender_id,
                'message' => $request->message
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message stored successfully',
                'data' => [
                    'conversation_id' => $conversation->id,
                    'message' => $message
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->apiResponse('error', 500, 'Something went wrong.');
        }
    }

    /**
     * Get all messages in a conversation
     */
    public function getMessages($conversationId)
    {
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return $this->sendError('Conversation not found', [], 404);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return $this->apiResponse('success', 200, 'Messages fetched', $messages);
    }
}
