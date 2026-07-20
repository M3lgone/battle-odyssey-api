<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Services\GameService;
use InvalidArgumentException;

class GameController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index(Request $request)
    {
        $game = $this->gameService->getActiveGame($request->user()->id);

        if (!$game) {
            return response()->json([
                'message' => 'No active game found.'
            ], 404);
        }

        return response()->json($game);
    }
    
    public function store(Request $request)
    {
        try {
            $game = $this->gameService->createGame($request->user()->id);
            
            return response()->json($game, 201);
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}