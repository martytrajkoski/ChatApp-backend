<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chats;
use App\Models\Message;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create two users
        $user1 = User::factory()->create(['name' => 'User One', 'email' => 'userone@example.com', 'password'=> bcrypt('password123')]);
        $user2 = User::factory()->create(['name' => 'User Two', 'email' => 'usertwo@example.com', 'password'=> bcrypt('password123')]);
        $user3 = User::factory()->create(['name' => 'User Three', 'email' => 'userthree@example.com', 'password'=> bcrypt('password123')]);
        
        // Create a chat between user1 and user2
        $chat = Chats::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
        ]);

        // Create an initial message from user1 without 'is_deleted'
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id,
            'content' => 'Hello!',
        ]);

        // Attach both users to the message in the pivot table with 'is_deleted'
        $message->users()->attach($user1->id, ['read_at' => now(), 'is_deleted' => false]);
        $message->users()->attach($user2->id, ['read_at' => null, 'is_deleted' => false]);

        // Create additional messages from both users and attach pivot data
        Message::factory()->count(5)->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id,
        ])->each(function($msg) use ($user1, $user2) {
            $msg->users()->attach($user1->id, ['read_at' => now(), 'is_deleted' => false]);
            $msg->users()->attach($user2->id, ['read_at' => null, 'is_deleted' => false]);
        });

        Message::factory()->count(5)->create([
            'chat_id' => $chat->id,
            'user_id' => $user2->id,
        ])->each(function($msg) use ($user1, $user2) {
            $msg->users()->attach($user1->id, ['read_at' => null, 'is_deleted' => false]);
            $msg->users()->attach($user2->id, ['read_at' => now(), 'is_deleted' => false]);
        });
    }
}
