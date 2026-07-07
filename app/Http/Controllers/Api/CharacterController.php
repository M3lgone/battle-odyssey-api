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
}
