<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Chats;
use App\Models\Message;
use Auth;
use Illuminate\Http\Request;
class MessageController extends Controller
{
    public function sendMessage(Request $request, $chatId)
    {
        $user_id = Auth::id();
        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $chat = Chats::findOrFail($chatId);

        $message = Message::create([
            'chat_id' => $chatId,
            'user_id' => $user_id,
            'content' => $request->input('content'),
        ]);

        $message->users()->attach([
            $chat->sender_id == $user_id ? $chat->receiver_id : $chat->sender_id,
        ]);
        
        if($chat->sender_deleted == true || $chat->receiver_deleted == true){
            $chat->sender_deleted = false;
            $chat->receiver_deleted = false;
        }

        SendMessage::dispatch($message); 

        return response()->json(['success' => 'Message sent', 'message' => $message]);
    }


    public function getMessagesFromChat($chatId)
    {
        $user = Auth::user();
        $user_id = $user->id;
    
        $chat = Chats::where(function($query) use ($user_id) {
            $query->where('sender_id', $user_id)
                  ->orWhere('receiver_id', $user_id);
        })->find($chatId);
    
        $messages = Message::where('chat_id', $chat->id)
                           ->with('users')
                           ->orderBy('created_at', 'desc')
                           ->paginate(30);
    
        $receiver = $chat->receiver->id !== $user->id ? $chat->receiver : $chat->sender;
    
        return [
            'chat_id' => $chat->id,
            'receiver' => [
                'id' => $receiver->id,
                'name' => $receiver->name,
                'profile_picture' => asset('storage/' . $receiver->profile_picture),
            ],
            'messages' => $messages->items(), 
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'next_page_url' => $messages->nextPageUrl(),
                'prev_page_url' => $messages->previousPageUrl(),
                'total' => $messages->total(),
            ]
        ];
    }
    

    public function markAsRead($messageId)
    {
        $user = Auth::id();
        $message = Message::findOrFail($messageId);

        $message->users()->updateExistingPivot($user, ['read_at' => now()]);
        // SendMessage::dispatch($message); 

        return response()->json(['success' => 'Message marked as read']);
    }

    public function deleteMessage($messageId)
    {
        $user = Auth::id();
        $message = Message::findOrFail($messageId);

        $message->users()->updateExistingPivot($user, ['is_deleted' => true]);

        return response()->json(['success' => 'Message deleted for user']);
    }
}
