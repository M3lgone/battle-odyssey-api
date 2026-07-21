<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminUpdateUserRequest;

/**
 * @group Admin - Users
 *
 * APIs for administrators to manage user accounts (view, update, and delete).
 */
class AdminUserController extends Controller
{
    /**
     * List all users
     * 
     * Retrieves a list of all registered users in the system. 
     * This endpoint is restricted to administrators.
     * 
     * @group Admin - Users
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "name": "Admin System",
     *     "email": "admin@example.com",
     *     "role": "admin",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "name": "Player One",
     *     "email": "player@example.com",
     *     "role": "player",
     *     "created_at": "2024-05-20T11:00:00.000000Z",
     *     "updated_at": "2024-05-20T11:00:00.000000Z"
     *   }
     * ]
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * Get user details
     * 
     * Retrieves the specific details of a single user by their ID.
     * Restricted to administrators.
     * 
     * @group Admin - Users
     * @authenticated
     * 
     * @urlParam user integer required The ID of the user. Example: 2
     * 
     * @response 200 {
     *   "id": 2,
     *   "name": "Player One",
     *   "email": "player@example.com",
     *   "role": "player",
     *   "created_at": "2024-05-20T11:00:00.000000Z",
     *   "updated_at": "2024-05-20T11:00:00.000000Z"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\User] 99999"
     * }
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update a user
     * 
     * Modifies the details of an existing user. Administrators can update
     * a user's name, email, password, and role. Only the provided fields 
     * will be updated.
     * 
     * @group Admin - Users
     * @authenticated
     * 
     * @urlParam user integer required The ID of the user to update. Example: 2
     * 
     * @bodyParam name string The new name of the user. Max 60 characters. Example: Updated Name
     * @bodyParam email string A unique email address. Example: newemail@example.com
     * @bodyParam password string The new password (min 8 characters). Must be confirmed. Example: newpassword123
     * @bodyParam password_confirmation string Required if password is provided. Example: newpassword123
     * @bodyParam role string The user's role. Must be 'player' or 'admin'. Example: admin
     * 
     * @response 200 {
     *   "id": 2,
     *   "name": "Updated Name",
     *   "email": "player@example.com",
     *   "role": "admin",
     *   "created_at": "2024-05-20T11:00:00.000000Z",
     *   "updated_at": "2024-05-20T11:30:00.000000Z"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\User] 99999"
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
    public function update(AdminUpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }

    /**
     * Delete a user
     * 
     * Permanently removes a user from the system. Administrators 
     * cannot delete their own account.
     * 
     * @group Admin - Users
     * @authenticated
     * 
     * @urlParam user integer required The ID of the user to delete. Example: 2
     * 
     * @response 200 {
     *   "message": "User deleted successfully"
     * }
     * 
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\User] 99999"
     * }
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
