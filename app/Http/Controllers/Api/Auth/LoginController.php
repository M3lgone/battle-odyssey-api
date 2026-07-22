<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * User login
     * 
     * Authenticates a user using their email and password. Upon successful 
     * authentication, it returns a Passport access token (Bearer token) 
     * that must be used in subsequent requests.
     * 
     * @group Authentication
     * @unauthenticated
     * 
     * @bodyParam email string required A valid email address registered in the system. Example: isma@gmail.com
     * @bodyParam password string required The user's password. Example: password4
     * 
     * @response 200 {
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoi... (Passport token)",
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
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     * 
     * @response 422 {
     *   "message": "The email field is required. (and 1 more error)",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ],
     *     "password": [
     *       "The password field is required."
     *     ]
     *   }
     * }
     */
    
    public function __invoke(LoginRequest $request)
    {
        $validated = $request->validated();

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
