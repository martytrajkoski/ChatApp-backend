<?php

namespace App\Jobs;

use App\Events\GotChat;
use App\Events\GotReadMessage;
use App\Models\Message;
use App\Events\GotMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        GotMessage::dispatch($this->message);
        GotChat::dispatch($this->message);
    }
}
