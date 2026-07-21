<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Services\GameService;
use InvalidArgumentException;


/**
 * @group Games
 *
 * APIs for managing the player's game sessions.
 * A game session consists of a sequence of 3 battles.
 * The game has only two states:
 * - **active**: The player is currently playing (they can leave and continue later).
 * - **finished**: The player won the 3 battles, lost a battle, or fled.
 */
class GameController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Get active game
     * 
     * Retrieves the currently active game for the authenticated user.
     * Returns a 404 error if the user does not have a game with an 'active' status.
     * 
     * @group Games
     * @authenticated
     * 
     * @response 200 {
     *   "id": 1,
     *   "user_id": 5,
     *   "status": "active",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T10:00:00.000000Z"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 404 {
     *   "message": "No active game found."
     * }
     */
    public function index(Request $request)
    {
        $game = $this->gameService->getActiveGame($request->user()->id);

        if (!$game) {
            return response()->json([
                'message' => 'No active game found.'
            ], 404);
        }

        return response()->json($game);
    }
    
    /**
     * Start a new game
     * 
     * Creates a new game instance for the authenticated user and sets its status to 'active'.
     * A user can only have one game in 'active' status at a time.
     * 
     * @group Games
     * @authenticated
     * 
     * @response 201 {
     *   "id": 2,
     *   "user_id": 5,
     *   "status": "active",
     *   "created_at": "2024-05-20T10:05:00.000000Z",
     *   "updated_at": "2024-05-20T10:05:00.000000Z"
     * }
     * 
     * @response 400 {
     *   "error": "You already have an active game, you must finish it to start another one."
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function store(Request $request)
    {
        try {
            $game = $this->gameService->createGame($request->user()->id);
            
            return response()->json($game, 201);
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}