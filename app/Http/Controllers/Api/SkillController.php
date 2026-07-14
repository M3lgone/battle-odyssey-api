<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::all();

        return response()->json($skills, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'description' => 'required|string',
            'damage_skill' => 'required|integer|min:0',
            'skill_cost_magic_points' => 'required|integer|min:0',
        ]);

        $skill = Skill::create($validated);

        return response()->json($skill, 201);
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'skill_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'damage_skill' => 'sometimes|integer|min:0',
            'skill_cost_magic_points' => 'sometimes|integer|min:0',
        ]);

        $skill->update($validated);

        return response()->json($skill, 200);
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return response()->json(['message' => 'Skill deleted successfully'], 200);
    }
}
