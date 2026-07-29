<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Game;
use App\Http\Requests\StoreBattleRequest;
use App\Http\Requests\UpdateBattleRequest;
use App\Services\BattleService;


/**
 * @group Battles
 *
 * APIs for managing the combat system.
 * A battle always belongs to an 'active' game session.
 * The combat itself is resolved on the client; these endpoints create battles,
 * persist their outcome and apply the resulting game state transitions.
 */
class BattleController extends Controller
{
    /**
     * Start a new battle
     * 
     * Initiates a new battle for a specific active game. The character is the one 
     * chosen when the game was created and starts every battle fully healed.
     * The system automatically selects the next enemy based on the number of battles 
     * already won (e.g., Goblin -> Troll -> Orc). Enemy current HP/MP are stored 
     * per enemy in the battle pivot, ready for future multi-enemy battles.
     * 
     * @authenticated
     * 
     * @bodyParam game_id integer required The ID of the active game. Must belong to the authenticated user. Example: 1
     * 
     * @response 201 {
     *   "message": "Battle started successfully!",
     *   "battle": {
     *     "id": 12,
     *     "game_id": 1,
     *     "character_id": 1,
     *     "result": "ongoing",
     *     "character_current_hp": 120,
     *     "character_current_mp": 100,
     *     "total_damage_dealt": 0,
     *     "total_damage_received": 0,
     *     "character": {
     *       "id": 1,
     *       "class": "Warrior",
     *       "skills": [
     *         {
     *           "id": 1,
     *           "skill_name": "Slash",
     *           "description": "A powerful sword slash.",
     *           "damage_skill": 25,
     *           "skill_cost_magic_points": 10
     *         }
     *       ]
     *     },
     *     "enemies": [
     *       {
     *         "id": 2,
     *         "enemy_name": "Troll",
     *         "skills": [
     *           {
     *             "id": 4,
     *             "skill_name": "Smash",
     *             "description": "Crushes everything with brutal strength",
     *             "damage_skill": 30,
     *             "skill_cost_magic_points": 15
     *           }
     *         ],
     *         "pivot": {
     *           "current_hp": 150,
     *           "current_mp": 50
     *         }
     *       }
     *     ]
     *   }
     * }
     * 
     * @response 200 {
     *   "message": "Victory",
     *   "game_won": true
     * }
     * 
     * @response 400 {
     *   "message": "This game is already finished."
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
            $request->validated('game_id')
        );
        return response()->json($result['payload'], $result['status']);
    }

    /**
     * Update a battle
     * 
     * Persists the outcome of a battle once the combat has been resolved on the 
     * client: final result, character and enemy HP/MP, and damage totals.
     * Only a battle in 'ongoing' state can be updated, and only by the owner 
     * of the game. State transitions are applied automatically: the game is 
     * finished when the battle is lost, fled, or when the final enemy is defeated.
     * 
     * @authenticated
     * 
     * @urlParam battle integer required The ID of the battle to update. Example: 12
     * 
     * @bodyParam result string required The battle outcome. One of: win, loss, flee. Example: win
     * @bodyParam character_current_hp integer required Final HP of the character. Minimum: 0. Example: 45
     * @bodyParam character_current_mp integer required Final MP of the character. Minimum: 0. Example: 30
     * @bodyParam total_damage_dealt integer required Total damage dealt to enemies. Minimum: 0. Example: 150
     * @bodyParam total_damage_received integer required Total damage received by the character. Minimum: 0. Example: 75
     * @bodyParam enemies array required Final state of every enemy involved in the battle.
     * @bodyParam enemies[].id integer required The ID of the enemy. Must belong to the battle. Example: 2
     * @bodyParam enemies[].current_hp integer required Final HP of the enemy. Minimum: 0. Example: 0
     * @bodyParam enemies[].current_mp integer required Final MP of the enemy. Minimum: 0. Example: 20
     * 
     * @response 200 {
     *   "message": "Battle updated successfully.",
     *   "game_status": "active",
     *   "battle": {
     *     "id": 12,
     *     "game_id": 1,
     *     "result": "win",
     *     "character_current_hp": 45,
     *     "character_current_mp": 30,
     *     "total_damage_dealt": 150,
     *     "total_damage_received": 75,
     *     "character": {
     *       "id": 1,
     *       "class": "Warrior",
     *       "skills": [
     *         {
     *           "id": 1,
     *           "skill_name": "Slash",
     *           "damage_skill": 25,
     *           "skill_cost_magic_points": 10
     *         }
     *       ]
     *     },
     *     "enemies": [
     *       {
     *         "id": 2,
     *         "enemy_name": "Troll",
     *         "skills": [
     *           {
     *             "id": 4,
     *             "skill_name": "Smash",
     *             "damage_skill": 30,
     *             "skill_cost_magic_points": 15
     *           }
     *         ],
     *         "pivot": {
     *           "current_hp": 0,
     *           "current_mp": 20
     *         }
     *       }
     *     ]
     *   }
     * }
     * 
     * @response 400 {
     *   "message": "This battle has already been resolved."
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Battle] 99999"
     * }
     * 
     * @response 422 {
     *   "message": "The result field is required.",
     *   "errors": {
     *     "result": [
     *       "The result field is required."
     *     ]
     *   }
     * }
     */
    public function update(UpdateBattleRequest $request, Battle $battle, BattleService $battleService)
    {
        $result = $battleService->finishBattle($battle, $request->validated());

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
     *   "character": {
     *     "id": 1,
     *     "class": "Warrior",
     *     "skills": [
     *       {
     *         "id": 1,
     *         "skill_name": "Slash",
     *         "description": "A powerful sword slash.",
     *         "damage_skill": 25,
     *         "skill_cost_magic_points": 10
     *       }
     *     ]
     *   },
     *   "enemies": [
     *     {
     *       "id": 1,
     *       "enemy_name": "Goblin",
     *       "skills": [
     *         {
     *           "id": 3,
     *           "skill_name": "Hack",
     *           "description": "Swings a crude weapon with reckless forc.",
     *           "damage_skill": 25,
     *           "skill_cost_magic_points": 10
     *         }
     *       ],
     *       "pivot": {
     *         "current_hp": 80,
     *         "current_mp": 30
     *       }
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
        $battle->load(['character.skills', 'enemies.skills']);

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
     *         "class": "Warrior",
     *         "skills": [
     *           {
     *             "id": 1,
     *             "skill_name": "Slash",
     *             "description": "A powerful sword slash.",
     *             "damage_skill": 25,
     *             "skill_cost_magic_points": 10
     *           }
     *         ]
     *       },
     *       "enemies": [
     *         {
     *           "enemy_name": "Goblin",
     *           "skills": [
     *             {
     *               "id": 3,
     *               "skill_name": "Hack",
     *               "description": "Swings a crude weapon with reckless forc.",
     *               "damage_skill": 25,
     *               "skill_cost_magic_points": 10
     *             }
     *           ],
     *           "pivot": {
     *             "current_hp": 0,
     *             "current_mp": 30
     *           }
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
            ->with(['character.skills', 'enemies.skills'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Battle history retrieved successfully.',
            'game_id' => $game->id,
            'battles' => $battles
        ], 200);
    }
}
