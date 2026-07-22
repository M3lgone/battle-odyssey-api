<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * User logout
     * 
     * Revokes the authenticated user's current Passport access token, 
     * effectively logging them out of the application and preventing 
     * further use of that specific token.
     * 
     * @group Authentication
     * @authenticated
     * 
     * @response 200 {
     *   "message": "Logged out successfully"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */

    public function __invoke(Request $request)
    {
        $request->user('api')->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
