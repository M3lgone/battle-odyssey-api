<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterRequest;

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

    public function store(StoreCharacterRequest $request)
    {
       $character = Character::create($request->validated());

        return response()->json($character, 201);
    }

    public function update(UpdateCharacterRequest $request, Character $character)
    {
        $character->update($request->validated());

        return response()->json($character, 200);
    }

    public function destroy(Character $character)
    {
        $character->delete();

        return response()->json(['message' => 'Character deleted successfully'], 200);
    }
}
