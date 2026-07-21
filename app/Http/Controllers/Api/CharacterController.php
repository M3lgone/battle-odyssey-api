<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterRequest;

/**
 * @group Characters
 *
 * APIs for viewing available characters and managing character data (Admins only for creation/editing).
 */
class CharacterController extends Controller
{
    /**
     * List all characters
     * 
     * Retrieves a list of all available character classes in the game. 
     * This endpoint is accessible by both players and administrators.
     * 
     * @group Characters
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "class": "Warrior",
     *     "attack": 20,
     *     "defense": 20,
     *     "max_health_points": 200,
     *     "max_magic_points": 50,
     *     "character_image_url": "warrior.png",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "class": "Mage",
     *     "attack": 30,
     *     "defense": 15,
     *     "max_health_points": 150,
     *     "max_magic_points": 200,
     *     "character_image_url": "mage.png",
     *     "created_at": "2024-05-20T10:00:00.000000Z",
     *     "updated_at": "2024-05-20T10:00:00.000000Z"
     *   }
     * ]
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function index()
    {
        $characters = Character::all();
        return response()->json($characters, 200);
    }

    /**
     * Get character details
     * 
     * Retrieves the details of a specific character. This endpoint also 
     * loads and returns all the skills associated with the character.
     * Accessible by both players and administrators.
     * 
     * @group Characters
     * @authenticated
     * 
     * @urlParam character integer required The ID of the character. Example: 2
     * 
     * @response 200 {
     *   "id": 2,
     *   "class": "Mage",
     *   "attack": 30,
     *   "defense": 15,
     *   "max_health_points": 150,
     *   "max_magic_points": 200,
     *   "character_image_url": "mage.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T10:00:00.000000Z",
     *   "skills": [
     *     {
     *       "id": 1,
     *       "skill_name": "Fireball",
     *       "description": "Shoots a fireball at the enemy",
     *       "damage_skill": 40,
     *       "skill_cost_magic_points": 20
     *     }
     *   ]
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Character] 99999"
     * }
     */
    public function show(Character $character)
    {
        $character->load('skills');
        return response()->json($character, 200);
    }

    /**
     * Create a character
     * 
     * Adds a new character class to the game. 
     * Restricted to administrators.
     * 
     * @group Characters
     * @authenticated
     * 
     * @bodyParam class string required The class name of the character. Must be one of: Warrior, Mage, Archer. Example: Mage
     * @bodyParam attack integer required The base attack power. Minimum: 0. Example: 30
     * @bodyParam defense integer required The base defense power. Minimum: 0. Example: 15
     * @bodyParam max_health_points integer required The maximum health points. Minimum: 1. Example: 150
     * @bodyParam max_magic_points integer required The maximum magic points. Minimum: 0. Example: 200
     * @bodyParam character_image_url string required The URL or path to the character's image. Example: mage.png
     * 
     * @response 201 {
     *   "id": 3,
     *   "class": "Mage",
     *   "attack": 30,
     *   "defense": 15,
     *   "max_health_points": 150,
     *   "max_magic_points": 200,
     *   "character_image_url": "mage.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T10:00:00.000000Z"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 422 {
     *   "message": "The class field is required. (and 5 more errors)",
     *   "errors": {
     *     "class": ["The class field is required."],
     *     "attack": ["The attack field is required."]
     *   }
     * }
     */
    public function store(StoreCharacterRequest $request)
    {
       $character = Character::create($request->validated());

        return response()->json($character, 201);
    }

    /**
     * Update a character
     * 
     * Modifies the attributes of an existing character. 
     * Restricted to administrators. Only the provided fields will be updated.
     * 
     * @group Characters
     * @authenticated
     * 
     * @urlParam character integer required The ID of the character to update. Example: 2
     * 
     * @bodyParam class string The class name of the character. Must be one of: Warrior, Mage, Archer. Example: Mage
     * @bodyParam attack integer The base attack power. Minimum: 0. Example: 99
     * @bodyParam defense integer The base defense power. Minimum: 0. Example: 15
     * @bodyParam max_health_points integer The maximum health points. Minimum: 1. Example: 150
     * @bodyParam max_magic_points integer The maximum magic points. Minimum: 0. Example: 200
     * @bodyParam character_image_url string The URL or path to the character's image. Example: mage.png
     * 
     * @response 200 {
     *   "id": 2,
     *   "class": "Mage",
     *   "attack": 99,
     *   "defense": 15,
     *   "max_health_points": 150,
     *   "max_magic_points": 200,
     *   "character_image_url": "mage.png",
     *   "created_at": "2024-05-20T10:00:00.000000Z",
     *   "updated_at": "2024-05-20T11:00:00.000000Z"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Character] 99999"
     * }
     * 
     * @response 422 {
     *   "message": "The attack field must be at least 0.",
     *   "errors": {
     *     "attack": ["The attack field must be at least 0."]
     *   }
     * }
     */
    public function update(UpdateCharacterRequest $request, Character $character)
    {
        $character->update($request->validated());

        return response()->json($character, 200);
    }

    /**
     * Delete a character
     * 
     * Permanently removes a character from the database.
     * Restricted to administrators.
     * 
     * @group Characters
     * @authenticated
     * 
     * @urlParam character integer required The ID of the character to delete. Example: 2
     * 
     * @response 200 {
     *   "message": "Character deleted successfully"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Character] 99999"
     * }
     */
    public function destroy(Character $character)
    {
        $character->delete();

        return response()->json(['message' => 'Character deleted successfully'], 200);
    }
}
