<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id'); 
            $table->unsignedBigInteger('receiver_id');
            $table->timestamps();
            $table->boolean('sender_deleted')->default(false);
            $table->boolean('receiver_deleted')->default(false);

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['sender_id', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('chats');
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['sender_deleted', 'receiver_deleted']);
        });
    }
};
