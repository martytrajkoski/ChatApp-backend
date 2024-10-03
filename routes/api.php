<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Chats\ChatsController;
use App\Http\Controllers\Auth\AuthController;

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'createUser']);
    Route::post('login', [AuthController::class, 'credentialsLogin']);
    Route::get('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('reset-password', [AuthController::class, 'updatePassword']);
    // Authentication routes that require previously issued access token
    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::patch('update-user', [AuthController::class, 'updateUser']);
        Route::patch('change-password', [AuthController::class, 'changePassword']);
        Route::post('change-profile-picture', [AuthController::class, 'changeProfilePicture']);
    });
});

Route::get('/user', function (Request $request) {
    $user = $request->user();
    
    if ($user->profile_picture) {
        $user->profile_picture = asset('storage/' . $user->profile_picture);
    }
    
    return response()->json($user);
})->middleware('auth:api');

Route::group(['middleware' => 'auth:api'], function(){
    Route::prefix('messages')->group(function(){
        Route::post('/chats/{chat}/messages', [MessageController::class, 'sendMessage']);
        Route::delete('{message}', [MessageController::class, 'deleteMessage']); 
        Route::patch('{message}/read', [MessageController::class, 'markAsRead']);
        Route::get('/chats/{chat}/messages', [MessageController::class, 'getMessagesFromChat']);
    });
    Route::prefix('chats')->group(function(){
        Route::get('fetch', [ChatsController::class, 'fetchChats']);
        Route::post('create', [ChatsController::class, 'startChat']);
        Route::get('{chat_id}', [ChatsController::class, 'getMessages']); 
        Route::post('{chat_id}', [ChatsController::class, 'deleteChat']);
    });
    Route::get('/fetchUsers', [UserController::class, 'fetchUsers']);
});