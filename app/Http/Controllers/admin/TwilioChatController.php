<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\{User, Conversation,Message};
use Illuminate\Support\Facades\Auth;

class TwilioChatController extends Controller
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function index()
    {
        $conversations = Conversation::with('userTwo','userTwo.userDetail')->get();
        return view('admin.chat.index', compact('conversations'));
    }

  

    public function getMessages($conversationId)
{
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return response()->json([
            'status' => 'error',
            'message' => 'Conversation not found',
        ], 404);
    }

    $messages = Message::where('conversation_id', $conversationId)
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $messages
    ]);
}

    public function listConversations()
    {

        $conversations = $this->twilio->conversations->v1->conversations->read();

        $result = [];
        foreach ($conversations as $conversation) {
            $result[] = [
                'sid' => $conversation->sid,
                'friendly_name' => $conversation->friendlyName,
            ];
        }

        return response()->json(['conversations' => $result]);
    }
}
