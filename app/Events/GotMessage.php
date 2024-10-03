<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class GotMessage implements ShouldBroadcast
{
    use Dispatchable, SerializesModels, InteractsWithSockets;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('message.' . $this->message->chat_id)]; 
    }
}
