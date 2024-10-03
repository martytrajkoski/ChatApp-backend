<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'sender_deleted', 'receiver_deleted'];

    public function sender(){
        return $this->belongsTo(User::class,  'sender_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class,  'receiver_id');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'chat_id');
    }
}
