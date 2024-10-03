<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function fetchUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if (!$request->has('search')) {
            $users = $query->paginate(10); 
        } else {
            $users = $query->get(); 
        }

        $users->transform(function ($user) {
            $user->profile_picture = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
            return $user;
        });

        return response()->json($users);
    }
    
}
