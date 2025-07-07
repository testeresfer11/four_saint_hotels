<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\API\TwilioService;


class TwilioConversationController extends Controller
{
    protected $twilio;


    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }


    /**
     * functionName : __construct
     * createdDate  : 2025-05-19
     * purpose      : Initialize Twilio client using credentials from .env
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
     * purpose      : Create a Twilio conversation with a unique friendly name
     */
    public function createConversation(Request $request)
    {
        $admin = User::where('role_id', 1)->first();
        $userId = Auth::id();
        $adminId = $admin->id;

        $friendlyName = "chat_admin_{$adminId}_user_{$userId}";

        $conversation = $this->twilio->conversations->v1->conversations->create([
            'friendlyName' => $friendlyName
        ]);

        return response()->json(['conversation_sid' => $conversation->sid]);
    }

    /**
     * functionName : addParticipant
     * createdDate  : 2025-05-19
     * purpose      : Add a participant (by identity) to a Twilio conversation
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
     * purpose      : Send a message from a user into a Twilio conversation
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
     * purpose      : Retrieve all messages for a given Twilio conversation
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

    /**
     * functionName : makeCall
     * createdDate  : 2025-05-19
     * purpose      : make call from twillio
     */
    public function makeCall(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'twiml_url' => 'required|url',
        ]);

        try {
            $call = $this->twilio->makeCall($request->to, $request->twiml_url);
            return response()->json([
                'message' => 'Call initiated successfully.',
                'sid' => $call->sid,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to make the call.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
