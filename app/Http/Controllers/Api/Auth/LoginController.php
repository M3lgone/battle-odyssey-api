<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
    * @group Auth
    * 
    * Login
    * 
    * @unauthenticated
    * 
    * @bodyParam email string required The user's email. Example: isma@gmail.com
    * @bodyParam password string required The user's password. Example: password4
    * 
    * @response 200 {"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...", "user": {"id": 1, "name": "Ismael", "email": "isma@gmail.com"}}
    * @response 401 {"message": "Invalid credentials"}
    * @response 422 {"message": "The email field is required.", "errors": {"email": ["The email field is required."], "password": ["The password field is required."]}}
    */
    
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api-token')->accessToken;
   
        return response()->json([
            'token' => $token,
            'user' => $user,
            ], 200);
    }
}
