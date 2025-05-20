<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\{User,Conversation};
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
        $conversations = Conversation::all();
        return view('admin.chat.index', compact('conversations'));
    }

    public function getMessages($sid)
    {
        $messages = $this->twilio->conversations->v1
            ->conversations($sid)
            ->messages
            ->read();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $sid)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $this->twilio->conversations->v1
            ->conversations($sid)
            ->messages
            ->create([
                'author' => 'admin',
                'body' => $request->body,
            ]);

        return back()->with('success', 'Message sent!');
    }

    public function listConversations(){
        
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
