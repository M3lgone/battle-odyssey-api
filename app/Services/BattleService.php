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
                'payload' => ['message' => 'You already have an ongoing battle.'],
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

    public function finishBattle(Battle $battle, array $data): array
    {
        if ($battle->result !== 'ongoing') {
            return [
                'payload' => ['message' => 'This battle has already been resolved.'],
                'status'  => 400
            ];
        }

        $game = $battle->game;

        DB::transaction(function () use ($battle, $data, $game) {
            $battle->update([
                'result' => $data['result'],
                'character_current_hp' => $data['character_current_hp'],
                'character_current_mp' => $data['character_current_mp'],
                'total_damage_dealt' => $data['total_damage_dealt'],
                'total_damage_received' => $data['total_damage_received'],
            ]);

            foreach ($data['enemies'] as $enemyData) {
                $battle->enemies()->updateExistingPivot($enemyData['id'], [
                    'current_hp' => $enemyData['current_hp'],
                    'current_mp' => $enemyData['current_mp'],
                ]);
            }

            if ($data['result'] === 'win') {
                $battlesWon = Battle::where('game_id', $game->id)
                                    ->where('result', 'win')
                                    ->count();

                if ($battlesWon >= Enemy::count()) {
                    $game->update(['status' => 'finished']);
                }
            } else {
                $game->update(['status' => 'finished']);
            }
        });

        $battle->load(['character.skills', 'enemies.skills']);

        return [
            'payload' => [
                'message' => 'Battle updated successfully.',
                'battle' => $battle,
                'game_status' => $game->status
            ],
            'status'  => 200
        ];
    }
}
