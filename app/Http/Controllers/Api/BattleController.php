<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Game;
use App\Http\Requests\StoreBattleRequest;
use App\Services\BattleService;


/**
 * @group Battles
 *
 * APIs for managing the combat system.
 * A battle always belongs to an 'active' game session.
 */
class BattleController extends Controller
{
    /**
     * Start a new battle
     * 
     * Initiates a new battle for a specific active game and character. The system 
     * automatically selects the next enemy based on the player's progression 
     * (e.g., Goblin -> Troll -> Orc). If the player has already defeated 
     * the final boss, the game status becomes 'finished' and it returns a victory message.
     * 
     * @authenticated
     * 
     * @bodyParam game_id integer required The ID of the active game. Must belong to the authenticated user. Example: 1
     * @bodyParam character_id integer required The ID of the character fighting the battle. Example: 1
     * 
     * @response 201 {
     *   "id": 12,
     *   "game_id": 1,
     *   "character_id": 1,
     *   "result": "ongoing",
     *   "character_current_hp": 120,
     *   "character_current_mp": 100,
     *   "enemy_current_hp": 150,
     *   "enemy_current_mp": 50,
     *   "enemy_name": "Troll"
     * }
     * 
     * @response 200 {
     *   "message": "Victory! The game has finished."
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 422 {
     *   "message": "The selected game id is invalid.",
     *   "errors": {
     *     "game_id": [
     *       "The selected game id is invalid."
     *     ]
     *   }
     * }
     */
    public function store(StoreBattleRequest $request, BattleService $battleService)
    {
        $result = $battleService->startBattle(
            $request->validated('game_id'), 
            $request->validated('character_id')
        );
        return response()->json($result['payload'], $result['status']);
    }

    /**
     * View battle details
     * 
     * Retrieves the current state and specific details of a single battle, 
     * including the character stats and the enemies involved. The battle 
     * must belong to a game owned by the authenticated user.
     * 
     * @authenticated
     * 
     * @urlParam battle integer required The ID of the battle to view. Example: 12
     * 
     * @response 200 {
     *   "id": 12,
     *   "game_id": 1,
     *   "character_id": 1,
     *   "result": "ongoing",
     *   "character_current_hp": 120,
     *   "enemy_current_hp": 80,
     *   "character": {
     *     "id": 1,
     *     "class": "Warrior"
     *   },
     *   "enemies": [
     *     {
     *       "id": 1,
     *       "enemy_name": "Goblin"
     *     }
     *   ]
     * }
     * 
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Battle] 99999"
     * }
     */
    public function show(Battle $battle)
    {
        if ($battle->game->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $battle->load(['character', 'enemies']);

        return response()->json($battle, 200);
    }

    /**
     * List game battles (History)
     * 
     * Retrieves the complete battle history for a specific game, ordered 
     * by the most recent battles first. Only the owner of the game can 
     * access this history. This is useful to see wins, losses, and flees for a run.
     * 
     * @authenticated
     * 
     * @urlParam game integer required The ID of the game. Example: 1
     * 
     * @response 200 {
     *   "message": "Battle history retrieved successfully.",
     *   "game_id": 1,
     *   "battles": [
     *     {
     *       "id": 12,
     *       "result": "ongoing",
     *       "character": {
     *         "class": "Warrior"
     *       },
     *       "enemies": [
     *         {
     *           "enemy_name": "Goblin"
     *         }
     *       ]
     *     },
     *     {
     *       "id": 11,
     *       "result": "win"
     *     }
     *   ]
     * }
     * 
     * @response 403 {
     *   "error": "Unauthorized. This game does not belong to you."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Game] 99999"
     * }
     */
    public function index(Game $game)
    {

        if ($game->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized. This game does not belong to you.'], 403);
        }

        $battles = Battle::where('game_id', $game->id)
            ->with(['character', 'enemies'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Battle history retrieved successfully.',
            'game_id' => $game->id,
            'battles' => $battles
        ], 200);
    }
}
