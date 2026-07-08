<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::all();
        return response()->json($characters, 200);
    }

    public function show(Character $character)
    {
        $character->load('skills');
        return response()->json($character, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class' => 'required|in:Warrior,Mage,Archer',
            'attack' => 'required|integer|min:0',
            'defense' => 'required|integer|min:0',
            'max_health_points' => 'required|integer|min:1',
            'max_magic_points' => 'required|integer|min:0',
        ]);
        
        $character = Character::create($validated);

        return response()->json($character, 201);
    }
}
