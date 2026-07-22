<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enemy;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEnemyRequest;
use App\Http\Requests\UpdateEnemyRequest;

/**
 * @group Enemies
 *
 * APIs for managing enemy entities and their stats. Restricted to administrators.
 */
class EnemyController extends Controller
{
    /**
     * List all enemies
     * 
     * Retrieves a list of all enemies in the database. 
     * This endpoint is strictly restricted to administrators.
     * 
     * @group Enemies
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "enemy_name": "Hydra",
     *     "max_health_points": 150,
     *     "max_magic_points": 100,
     *     "attack": 40,
     *     "defense": 30,
     *     "enemy_image_url": "hydra.png",
     *     "background_image_url": "bg_hydra.png",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "enemy_name": "Goblin",
     *     "max_health_points": 100,
     *     "max_magic_points": 0,
     *     "attack": 15,
     *     "defense": 5,
     *     "enemy_image_url": "goblin.png",
     *     "background_image_url": "bg-goblin.png",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
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
        $enemies = Enemy::all();

        return response()->json($enemies, 200);
    }

    /**
     * Get enemy details
     * 
     * Retrieves the details of a specific enemy, including its associated skills.
     * This endpoint is strictly restricted to administrators.
     * 
     * @group Enemies
     * @authenticated
     * 
     * @urlParam enemy integer required The ID of the enemy. Example: 1
     * 
     * @response 200 {
     *   "id": 1,
     *   "enemy_name": "Hydra",
     *   "max_health_points": 150,
     *   "max_magic_points": 100,
     *   "attack": 40,
     *   "defense": 30,
     *   "enemy_image_url": "hydra.png",
     *   "background_image_url": "bg_hydra.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T10:00:00.000000Z",
     *   "skills": [
     *     {
     *       "id": 1,
     *       "skill_name": "Venom Spit",
     *       "description": "Spits deadly venom.",
     *       "damage_skill": 25,
     *       "skill_cost_magic_points": 10
     *     }
     *   ]
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
     *   "message": "No query results for model [App\\Models\\Enemy] 99999"
     * }
     */
    public function show(Enemy $enemy)
    {
        $enemy->load('skills');
        
        return response()->json($enemy, 200);
    }

    /**
     * Create an enemy
     * 
     * Adds a new enemy type to the database.
     * Restricted to administrators.
     * 
     * @group Enemies
     * @authenticated
     * 
     * @bodyParam enemy_name string required The name of the enemy. Max 255 characters. Example: Hydra
     * @bodyParam max_health_points integer required Maximum health points. Minimum: 1. Example: 150
     * @bodyParam max_magic_points integer required Maximum magic points. Minimum: 0. Example: 100
     * @bodyParam attack integer required Base attack stat. Minimum: 0. Example: 40
     * @bodyParam defense integer required Base defense stat. Minimum: 0. Example: 30
     * @bodyParam enemy_image_url string required URL or path to the enemy's character sprite/image. Example: hydra.png
     * @bodyParam background_image_url string required URL or path to the enemy's background image for battle scenes. Example: bg_hydra.png
     * 
     * @response 201 {
     *   "id": 1,
     *   "enemy_name": "Hydra",
     *   "max_health_points": 150,
     *   "max_magic_points": 100,
     *   "attack": 40,
     *   "defense": 30,
     *   "enemy_image_url": "hydra.png",
     *   "background_image_url": "bg_hydra.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T10:00:00.000000Z"
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
     * @response 422 {
     *   "message": "The enemy name field is required. (and 6 more errors)",
     *   "errors": {
     *     "enemy_name": ["The enemy name field is required."],
     *     "max_health_points": ["The max health points field is required."]
     *   }
     * }
     */
    public function store(StoreEnemyRequest $request)
    {
        $enemy = Enemy::create($request->validated());

        return response()->json($enemy, 201);
    }

    /**
     * Update an enemy
     * 
     * Modifies the attributes of an existing enemy.
     * Restricted to administrators. Only the provided fields will be updated.
     * 
     * @group Enemies
     * @authenticated
     * 
     * @urlParam enemy integer required The ID of the enemy to update. Example: 1
     * 
     * @bodyParam enemy_name string The name of the enemy. Max 255 characters. Example: Hobgoblin
     * @bodyParam max_health_points integer Maximum health points. Minimum: 1. Example: 300
     * @bodyParam max_magic_points integer Maximum magic points. Minimum: 0. Example: 100
     * @bodyParam attack integer Base attack stat. Minimum: 0. Example: 45
     * @bodyParam defense integer Base defense stat. Minimum: 0. Example: 35
     * @bodyParam enemy_image_url string URL or path to the enemy's character sprite/image. Example: hobgoblin.png
     * @bodyParam background_image_url string URL or path to the enemy's background image. Example: bg_hobgoblin.png
     * 
     * @response 200 {
     *   "id": 1,
     *   "enemy_name": "Hobgoblin",
     *   "max_health_points": 300,
     *   "max_magic_points": 100,
     *   "attack": 40,
     *   "defense": 30,
     *   "enemy_image_url": "goblin.png",
     *   "background_image_url": "bg-goblin.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T11:00:00.000000Z"
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
     *   "message": "No query results for model [App\\Models\\Enemy] 99999"
     * }
     * 
     * @response 422 {
     *   "message": "The max health points must be at least 1.",
     *   "errors": {
     *     "max_health_points": ["The max health points must be at least 1."]
     *   }
     * }
     */
    public function update(UpdateEnemyRequest $request, Enemy $enemy)
    {
        $enemy->update($request->validated());

        return response()->json($enemy, 200);
    }

    /**
     * Delete an enemy
     * 
     * Permanently removes an enemy from the database.
     * Restricted to administrators.
     * 
     * @group Enemies
     * @authenticated
     * 
     * @urlParam enemy integer required The ID of the enemy to delete. Example: 1
     * 
     * @response 200 {
     *   "message": "Enemy deleted successfully"
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
     *   "message": "No query results for model [App\\Models\\Enemy] 99999"
     * }
     */
    public function destroy(Enemy $enemy)
    {
        $enemy->delete();

        return response()->json(['message' => 'Enemy deleted successfully'], 200);
    }
}