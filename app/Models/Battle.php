<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'result',
        'character_id',
        'game_id',
        'character_current_hp',
        'character_current_mp',
        'total_damage_dealt',
        'total_damage_received',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function enemies()
    {
        return $this->belongsToMany(Enemy::class, 'battle_has_enemy')
                    ->withPivot(['current_hp', 'current_mp']);
    }
}
