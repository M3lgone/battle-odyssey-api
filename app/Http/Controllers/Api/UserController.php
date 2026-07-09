<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
    * @group Profile
    * 
    * Show user profile
    *
    * @authenticated
    *  
    * @urlParam user integer required The user ID. Example: 1
    * 
    * @response 200 {"id": 1, "name": "Ismael", "email": "isma@gmail.com", "role": "player"}
    * @response 401 {"message": "Unauthenticated."}
    * @response 404 {"message": "User not found"}
    */

    public function show(Request $request)
    {
        $user = auth()->user();
        return response()->json($user, 200);
    }

     /**
    * @group Profile
    * 
    * Update user profile
    *
    * @authenticated
    *  
    * @urlParam user integer required The user ID. Example: 1
    * 
    * @bodyParam name string The user's name. Example: Updated name
    * @bodyParam email string The user's email. Example: isma@gmail.com
    * @bodyParam password string Minimum 8 characters. Example: password123
    * @bodyParam password_confirmation string Must match password. Example: password123
    * 
    * @response 200 {"id": 1, "name": "Updated name", "email": "isma@gmail.com", "role": "player"}
    * @response 403 {"message": "Forbidden"}
    * @response 422 {"message": "The email has already been taken."}
    */

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:60',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }

    /**
    * @group Profile
    * 
    * Delete user profile
    *
    * @authenticated
    * 
    * @urlParam user integer required The user ID. Example: 1
    * 
    * @response 200 {"message": "User deleted successfully"}
    */

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
