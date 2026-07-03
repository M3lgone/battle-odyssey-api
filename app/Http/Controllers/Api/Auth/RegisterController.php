<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
    * @group Auth
    * 
    * Register a new user
    * 
    * @unauthenticated
    * 
    * @bodyParam name string required The user's name. Example: Ismael
    * @bodyParam email string required The user's email. Example: isma@gmail.com
    * @bodyParam password string required Minimum 8 characters. Example: password4
    * @bodyParam password_confirmation string required Must match password. Example: password4
    * 
    * @response 201 {"id": 1, "name": "Ismael", "email": "isma@gmail.com", "role": "player"}
    * @response 422 {"message": "The email has already been taken."}
    */

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
}