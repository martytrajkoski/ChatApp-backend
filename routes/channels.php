<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chats;

Broadcast::routes(['middleware' => ['auth:api']]);


Broadcast::channel('message.{id}', function ($user, $id) {
    return Chats::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id);
                })
                ->exists();
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    return Chats::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id);
                })
                ->exists();
});