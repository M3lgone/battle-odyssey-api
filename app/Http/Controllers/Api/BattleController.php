<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Game;

class BattleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'character_id' => 'required|exists:characters,id',
        ]);

        $game = Game::find($request->game_id);
        
        if ($game->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ongoingBattle = Battle::where('game_id', $game->id)
                               ->where('result', 'ongoing')
                               ->exists();

        if ($ongoingBattle) {
            return response()->json(['error' => 'You already have an ongoing battle.'], 400);
        }

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

    public function show(Battle $battle)
    {
        if ($battle->game->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $battle->load(['character', 'enemies']);

        return response()->json($battle, 200);
    }

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
