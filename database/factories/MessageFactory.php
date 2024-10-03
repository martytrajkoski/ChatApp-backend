<?php

namespace Database\Factories;

use App\Models\Chats;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'chat_id' => Chats::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->text,
            'read_at' => null, 
        ];
    }
}
