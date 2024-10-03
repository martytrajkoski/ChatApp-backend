<?php

namespace Database\Factories;

use App\Models\Chats;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatsFactory extends Factory
{
    protected $model = Chats::class;

    public function definition()
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
        ];
    }
}
