<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Register a new user
     * 
     * Creates a new user account in the system. By default, 
     * all new users are assigned the 'player' role.
     * 
     * @group Authentication
     * @unauthenticated
     * 
     * @bodyParam name string required The user's name. Maximum 60 characters. Example: Ismael
     * @bodyParam email string required A valid and unique email address. Example: isma@gmail.com
     * @bodyParam password string required The access password. Minimum 8 characters. Example: password123
     * @bodyParam password_confirmation string required Must match the password exactly. Example: password123
     * 
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "Ismael",
     *     "email": "isma@gmail.com",
     *     "role": "player",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
     *   }
     * }
     * 
     * @response 422 {
     *   "message": "The email has already been taken.",
     *   "errors": {
     *     "email": [
     *       "The email has already been taken."
     *     ]
     *   }
     * }
     */

    public function __invoke(RegisterRequest $request)
    {
        $validated = $request->validated();

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