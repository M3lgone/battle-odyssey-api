<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;

/**
 * @group Profile
 *
 * APIs for managing the currently authenticated user's profile.
 */
class UserController extends Controller
{
    /**
     * Show user profile
     * 
     * Retrieves the details of the currently authenticated user.
     *
     * @authenticated
     *  
     * @response 200 {
     *   "id": 1,
     *   "name": "Ismael",
     *   "email": "isma@gmail.com",
     *   "role": "player",
     *   "created_at": "2024-03-15T10:00:00.000000Z",
     *   "updated_at": "2024-03-15T10:00:00.000000Z"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        return response()->json($user, 200);
    }

    /**
     * Update user profile
     * 
     * Modifies the authenticated user's profile. You only need to send the fields 
     * you want to update. Passwords will be securely hashed.
     *
     * @authenticated
     *  
     * @bodyParam name string The user's new name. Max 60 characters. Example: Updated name
     * @bodyParam email string The user's new email. Must be unique. Example: isma@gmail.com
     * @bodyParam password string Minimum 8 characters. Example: password123
     * @bodyParam password_confirmation string Required if updating password. Must match password. Example: password123
     * 
     * @response 200 {
     *   "id": 1, 
     *   "name": "Updated name", 
     *   "email": "isma@gmail.com", 
     *   "role": "player",
     *   "created_at": "2024-03-15T10:00:00.000000Z",
     *   "updated_at": "2024-03-15T10:05:00.000000Z"
     * }
     * 
     * @response 422 {
     *   "message": "The email has already been taken.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     * 
     * @response 422 {
     *   "message": "The password confirmation does not match.",
     *   "errors": {
     *     "password": ["The password confirmation does not match."]
     *   }
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }

    /**
     * Delete user profile
     * 
     * Permanently deletes the currently authenticated user's account and all associated data.
     *
     * @authenticated
     * 
     * @response 200 {
     *   "message": "User deleted successfully"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
