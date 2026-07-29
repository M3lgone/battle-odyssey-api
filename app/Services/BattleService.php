<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class BattleService
{
    public function startBattle(int $gameId): array
    {
        $game = Game::with('character')->findOrFail($gameId);

        if ($game->status !== 'active') {
            return [
                'payload' => ['message' => 'This game is already finished.'],
                'status'  => 400
            ];
        }

        $ongoingBattle = Battle::where('game_id', $gameId)
                               ->where('result', 'ongoing')
                               ->exists();

        if ($ongoingBattle) {
            return [
                'payload' => ['error' => 'You already have an ongoing battle.'],
                'status'  => 400
            ];
        }

        $battlesWon = Battle::where('game_id', $gameId)
                            ->where('result', 'win')
                            ->count();

        $enemy = Enemy::orderBy('id')->skip($battlesWon)->first();

        if (!$enemy) {
            $game->update(['status' => 'finished']);

            return [
                'payload' => [
                    'message' => 'Victory',
                    'game_won' => true
                ],
                'status'  => 200
            ];
        }

        $character = $game->character;

        $battle = DB::transaction(function () use ($game, $character, $enemy) {
            $battle = Battle::create([
                'game_id' => $game->id,
                'character_id' => $character->id,
                'result' => 'ongoing',
                'character_current_hp' => $character->max_health_points,
                'character_current_mp' => $character->max_magic_points,
                'total_damage_dealt' => 0,
                'total_damage_received' => 0,
            ]);

            $battle->enemies()->attach($enemy->id, [
                'current_hp' => $enemy->max_health_points,
                'current_mp' => $enemy->max_magic_points,
            ]);

            return $battle;
        });

        $battle->load(['character.skills', 'enemies.skills']);

        return [
            'payload' => [
                'message' => 'Battle started successfully!',
                'battle' => $battle
            ],
            'status'  => 201
        ];
    }
}
