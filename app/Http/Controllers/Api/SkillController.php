<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;


/**
 * @group Skills
 *
 * APIs for managing character and enemy skills.  Restricted to administrators.
 *  
 */
class SkillController extends Controller
{
    /**
     * List all skills
     * 
     * Retrieves a complete list of all available skills in the game.
     * 
     * @authenticated
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "skill_name": "Fireball",
     *     "description": "Shoots a blazing fireball.",
     *     "damage_skill": 50,
     *     "skill_cost_magic_points": 20,
     *     "created_at": "2024-03-15T10:00:00.000000Z",
     *     "updated_at": "2024-03-15T10:00:00.000000Z"
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
        $skills = Skill::all();

        return response()->json($skills, 200);
    }

    /**
     * Get skill details
     *
     * Retrieves the details of a specific skill.
     *
     * @authenticated
     *
     * @urlParam skill integer required The ID of the skill. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "skill_name": "Fireball",
     *   "description": "Shoots a blazing fireball.",
     *   "damage_skill": 50,
     *   "skill_cost_magic_points": 20,
     *   "created_at": "2024-03-15T10:00:00.000000Z",
     *   "updated_at": "2024-03-15T10:00:00.000000Z"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Skill] 99999"
     * }
     */
    public function show(Skill $skill)
    {
        return response()->json($skill, 200);
    }

    /**
     * Create a new skill
     * 
     * Adds a new skill to the game database.
     * 
     * @authenticated
     * 
     * @bodyParam skill_name string required The name of the skill. Max 255 characters. Example: Meteor Strike
     * @bodyParam description string required A detailed description of what the skill does. Example: A devastating fiery boulder friom the sky.
     * @bodyParam damage_skill integer required The base damage the skll inflicts. Must be 0 or greater. Example: 100
     * @bodyParam skill_cost_magic_points integer required The MP cost to use the skill. Must be 0 or greater. Example: 50
     * 
     * @response 201 {
     *   "id": 2,
     *   "skill_name": "Meteor Strike",
     *   "description": "A devastating fiery boulder from the sky.",
     *   "damage_skill": 100,
     *   "skill_cost_magic_points": 50,
     *   "created_at": "2024-03-15T10:05:00.000000Z",
     *   "updated_at": "2024-03-15T10:05:00.000000Z"
     * }
     * 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "damage_skill": [
     *       "The damage skill must be at least 0."
     *     ]
     *   }
     * }
     */
    public function store(StoreSkillRequest $request)
    {
        $skill = Skill::create($request->validated());

        return response()->json($skill, 201);
    }

    /**
     * Update an existing skill
     * 
     * Modifies the attributes of a specific skill. You only need to send 
     * the fields you want to update.
     * 
     * @authenticated
     * 
     * @urlParam skill integer required The ID of the skill to update. Example: 1
     * 
     * @bodyParam skill_name string The new name of the skill. Max 255 characters. Example: Super Fireball
     * @bodyParam description string The new description. Example: Shoots an even bigger fireball.
     * @bodyParam damage_skill integer The new damage value. Must be 0 or greater. Example: 150
     * @bodyParam skill_cost_magic_points integer The new MP cost. Must be 0 or greater. Example: 40
     * 
     * @response 200 {
     *   "id": 1,
     *   "skill_name": "Super Fireball",
     *   "description": "Shoots an even bigger fireball.",
     *   "damage_skill": 150,
     *   "skill_cost_magic_points": 40,
     *   "created_at": "2024-03-15T10:00:00.000000Z",
     *   "updated_at": "2024-03-15T10:10:00.000000Z"
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Skill] 99999"
     * }
     */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $skill->update($request->validated());

        return response()->json($skill, 200);
    }

    /**
     * Delete a skill
     * 
     * Permanently removes a skill from the database. 
     * 
     * @authenticated
     * 
     * @urlParam skill integer required The ID of the skill to delete. Example: 1
     * 
     * @response 200 {
     *   "message": "Skill deleted successfully"
     * }
     * 
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\Skill] 99999"
     * }
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        return response()->json(['message' => 'Skill deleted successfully'], 200);
    }
}
