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

class BattleController extends Controller
{
    public function store(StoreBattleRequest $request, BattleService $battleService)
    {
        $result = $battleService->startBattle(
            $request->validated('game_id'), 
            $request->validated('character_id')
        );
        return response()->json($result['payload'], $result['status']);
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
