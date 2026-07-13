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
}