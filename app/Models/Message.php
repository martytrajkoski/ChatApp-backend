<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message';
    
    protected $fillable = ['chat_id', 'user_id', 'content', 'is_deleted'];

    public function chat(){
        return $this->belongsTo(Chats::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'message_user', 'message_id', 'user_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }
}
