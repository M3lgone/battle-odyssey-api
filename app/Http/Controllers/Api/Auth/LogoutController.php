<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
    * @group Auth
    * 
    * Logout
    * 
    * @authenticated
    * 
    * @response 200 {"message": "Logged out successfully"}
    * @response 401 {"message": "Unauthenticated."}
    */

    public function __invoke(Request $request)
    {
        $request->user('api')->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
