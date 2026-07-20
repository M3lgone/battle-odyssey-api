<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;

class BattleService
{
    public function startBattle(int $gameId, int $characterId): array
    {
        $ongoingBattle = Battle::where('game_id', $gameId)
                               ->where('result', 'ongoing')
                               ->exists();

        if ($ongoingBattle) {
            return [
                'payload' => ['error' => 'You already have an ongoing battle.'],
                'status'  => 400
            ];
        }

        $battlesFought = Battle::where('game_id', $gameId)->count();
        $enemy = Enemy::orderBy('id')->skip($battlesFought)->first();

        if (!$enemy) {
            return [
                'payload' => [
                    'message' => 'Victory',
                    'game_won' => true
                ],
                'status'  => 200
            ];
        }

        $character = Character::findOrFail($characterId);

        $battle = Battle::create([
            'game_id' => $gameId,
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

        return [
            'payload' => [
                'message' => 'Battle started successfully!',
                'battle' => $battle
            ],
            'status'  => 201
        ];
    }
}