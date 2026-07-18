<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;

class BattleController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'game_id' => 'required|exists:games,id',
            'character_id' => 'required|exists:characters,id',
        ]);

        $battlesFought = Battle::where('game_id', $request->game_id)->count();

        $enemy = Enemy::orderBy('id')->skip($battlesFought)->first();

        if (!$enemy) {
            return response()->json([
                'message' => 'Victory',
                'game_won' => true
            ], 200);
        }

        $character = Character::findOrFail($request->character_id);

        $battle = Battle::create([
            'game_id' => $request->game_id,
            'character_id' => $character->id,
            'result' => 'ongoing',
            'character_current_hp' => $character->max_health_points,
            'character_current_mp' => $character->max_magic_points,
            'enemy_current_hp' => $enemy->max_health_points,
            'enemy_current_mp' => $enemy->max_magic_points,
            'total_damage_dealt' => 0,
            'total_damage_received' => 0,
        ]);

        $battle->enemies()->attach($enemy->id);

        $battle->load(['character', 'enemies']);

        return response()->json([
            'message' => 'Battle started successfully!',
            'battle' => $battle
        ], 201);
    }
}
