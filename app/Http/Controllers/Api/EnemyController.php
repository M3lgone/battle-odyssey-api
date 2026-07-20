<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enemy;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEnemyRequest;
use App\Http\Requests\UpdateEnemyRequest;

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

    public function store(StoreEnemyRequest $request)
    {
        $enemy = Enemy::create($request->validated());

        return response()->json($enemy, 201);
    }

    public function update(UpdateEnemyRequest $request, Enemy $enemy)
    {
        $enemy->update($request->validated());

        return response()->json($enemy, 200);
    }

    public function destroy(Enemy $enemy)
    {
        $enemy->delete();

        return response()->json(['message' => 'Enemy deleted successfully'], 200);
    }
}