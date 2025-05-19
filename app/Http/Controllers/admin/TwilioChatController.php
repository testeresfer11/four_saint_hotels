<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TwilioConversationController extends Controller
{
    protected $twilio;

    /**
     * functionName : __construct
     * createdDate  : 2025-05-19
     * purpose      : Initialize Twilio client using environment variables
     */
    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    /**
     * functionName : createConversation
     * createdDate  : 2025-05-19
     * purpose      : Create a unique conversation based on admin_id and user_id
     */
    public function createConversation(Request $request)
    {
        $admin = User::where('role_id', 1)->first();
        $userId = Auth::id();
        $adminId = $admin->id;

        $friendlyName = "chat_admin_{$adminId}_user_{$userId}";

        // Check if conversation already exists
        $existing = $this->twilio->conversations->v1->conversations->read([
            'friendlyName' => $friendlyName
        ]);

        if (!empty($existing)) {
            return response()->json([
                'message' => 'Conversation already exists',
                'conversation_sid' => $existing[0]->sid
            ], 200);
        }

        // Create new conversation
        $conversation = $this->twilio->conversations->v1->conversations->create([
            'friendlyName' => $friendlyName
        ]);

        return response()->json(['conversation_sid' => $conversation->sid]);
    }

    /**
     * functionName : addParticipant
     * createdDate  : 2025-05-19
     * purpose      : Add a participant to a conversation using their identity
     */
    public function addParticipant(Request $request)
    {
        $request->validate([
            'conversation_sid' => 'required',
            'identity' => 'required',
        ]);

        $participant = $this->twilio->conversations->v1
            ->conversations($request->conversation_sid)
            ->participants
            ->create([
                'identity' => $request->identity
            ]);

        return response()->json(['participant_sid' => $participant->sid]);
    }

    /**
     * functionName : sendMessage
     * createdDate  : 2025-05-19
     * purpose      : Send a message in a given Twilio conversation
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_sid' => 'required',
            'author' => 'required',
            'body' => 'required',
        ]);

        $message = $this->twilio->conversations->v1
            ->conversations($request->conversation_sid)
            ->messages
            ->create([
                'author' => $request->author,
                'body' => $request->body
            ]);

        return response()->json(['message_sid' => $message->sid]);
    }

    /**
     * functionName : fetchMessages
     * createdDate  : 2025-05-19
     * purpose      : Retrieve all messages from a Twilio conversation
     */
    public function fetchMessages($conversationSid)
    {
        $messages = $this->twilio->conversations->v1
            ->conversations($conversationSid)
            ->messages
            ->read();

        $result = collect($messages)->map(function ($msg) {
            return [
                'sid' => $msg->sid,
                'author' => $msg->author,
                'body' => $msg->body,
                'date_created' => $msg->dateCreated->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json(['messages' => $result]);
    }
}
