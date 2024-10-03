<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Chats;
use Auth;
use Illuminate\Http\Request;
class ChatsController extends Controller
{
    public function fetchChats()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $chats = Chats::where(function($query) use ($user_id) {
            $query->where('sender_id', $user_id)->where('sender_deleted', false)
                  ->orWhere('receiver_id', $user_id)->where('receiver_deleted', false);
        })
        ->with(['messages' => function($query) {
            $query->with('users') 
                  ->orderBy('created_at', 'desc') 
                  ->limit(1); 
        }])->get();
        
        $response = $chats->map(function ($chat) use ($user) {
            return [
                'chat_id' => $chat->id,
                'receiver' => [
                    'id' => $chat->receiver->id !== $user->id ?  $chat->receiver->id : $chat->sender->id,
                    'name' => $chat->receiver->name !== $user->name ?  $chat->receiver->name : $chat->sender->name,
                    'profile_picture' => $chat->receiver->profile_picture !== $user->profile_picture ?  asset('storage/' . $chat->receiver->profile_picture) : asset('storage/' . $chat->sender->profile_picture),
                ],
                'messages' => $chat->messages,
            ];
        });

        return response()->json($response);
    }

    public function startChat(Request $request)
    {
        $user1 = Auth::id();
        $user2 = $request->input('user_id'); 
        $chat = Chats::where(function ($query) use ($user1, $user2) {
            $query->where('sender_id', $user1)->where('receiver_id', $user2);
        })->orWhere(function ($query) use ($user1, $user2) {
            $query->where('sender_id', $user2)->where('receiver_id', $user1);
        })->first();
        if ($chat) {
            $chat->sender_deleted = false;
            $chat->receiver_deleted = false;
            $chat->save();
        }else{
            $chat = Chats::create(['sender_id' => $user1, 'receiver_id' => $user2]);
        }

        return response()->json($chat);
    }

    public function getMessages($chat_id)
    {
        $user = Auth::id();

        $chats = Chats::with(['messages' => function($query) use ($user){
            $query->where('message.user_id', $user)->with(['users'])->orderBy('created_at', 'desc')->limit(1);
        }])->find($chat_id);
    
        if (!$chats) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $message = $chats->messages->first();
    
        $response = [
            'chat_id' => $chats->id,
            'receiver' => [
                'id' => $message->users->first()->id,
                'name' => $message->users->first()->name,
                'profile_picture' => $message->users->first()->profile_picture,
            ],
            'messages' => $message->content,
        ];
    
        return response()->json($response);
    }

    public function deleteChat($chat_id)
    {
        $user = Auth::id();
        $chat = Chats::findOrFail($chat_id);

        if ($chat->sender_id == $user) {
            $chat->sender_deleted = true;
        } elseif ($chat->receiver_id == $user) {
            $chat->receiver_deleted = true;
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($chat->sender_deleted && $chat->receiver_deleted) {
            $chat->delete(); 
        } else {
            $chat->save();
        }

        return response()->json(['success' => 'Chat deleted for user']);
    }

    
}
