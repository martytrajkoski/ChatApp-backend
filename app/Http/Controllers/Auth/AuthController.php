<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\changePasswordAuthRequest;
use App\Http\Requests\AuthRequest\loginAuthRequest;
use App\Http\Requests\AuthRequest\updateAuthRequest;
use App\Http\Requests\AuthRequest\registerAuthRequest;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\TokenRepository;
use App\Models\User;

class AuthController extends Controller
{
    public function createUser(registerAuthRequest $request)
    {
        $request->validated();

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $profilePicturePath,
        ]);
        $token = $user->createToken('Laravel')->accessToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
            ],
            'token' => $token
        ], 201);
    }

    public function credentialsLogin(loginAuthRequest $request)
    {
        $request->validated();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Laravel')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function updateUser(updateAuthRequest $request)
    {
        $request->validated();

        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $profilePicturePath;
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], 201);
    }
    public function changePassword(changePasswordAuthRequest $request)
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['error' => 'Current password does not match.'], 400);
        }

        try {
            $user->password = Hash::make($validatedData['new_password']);
            $user->save();
        } catch (\Exception $e) {
            \Log::error('Error changing password: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to change password.'], 500);
        }

        return response()->json(['message' => 'Password changed successfully.', 'user' => $user]);
    }
    public function changeProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = Auth::user();
    
        if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
            Storage::delete('public/' . $user->profile_picture);
        }
    
        $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');    
        
        $user->profile_picture = $profilePicturePath;
        $user->save();

        return response()->json([
            'message' => 'Profile picture updated successfully!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => Storage::url($user->profile_picture), 
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
