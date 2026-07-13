<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enemy;
use Illuminate\Http\Request;

class EnemyController extends Controller
{
    public function index()
    {
        $enemies = Enemy::all();

        return response()->json($enemies, 200);
    }

    public function show(Enemy $enemy)
    {
        $enemy->load('skills');
        
        return response()->json($enemy, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enemy_name' => 'required|string|max:255', 
            'max_health_points' => 'required|integer|min:1',
            'max_magic_points' => 'required|integer|min:0',
            'attack' => 'required|integer|min:0',
            'defense' => 'required|integer|min:0',
        ]);
        
        $enemy = Enemy::create($validated);

        return response()->json($enemy, 201);
    }
}