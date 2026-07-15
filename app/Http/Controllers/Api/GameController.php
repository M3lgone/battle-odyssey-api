<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $game = Game::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if (!$game) {
            return response()->json([
                'message' => 'No active game found.'
            ], 404);
        }

        return response()->json($game);
    }
    
    public function store(Request $request)
    {
        $activeGameExists = Game::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'in_progress'])
            ->exists();

        if ($activeGameExists) {
            return response()->json([
                'error' => 'You already have a game in progress, you must finish or delte it to start another one.'
            ], 400);
        }

        $game = Game::create([
            'user_id' => auth()->id(),
            'status' => 'active',
        ]);

        return response()->json($game, 201);
    }
}