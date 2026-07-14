<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'skill_name',
        'description',
        'damage_skill',
        'skill_cost_magic_points',
    ];

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'character_has_skill');
    }

    public function enemies()
    {
        return $this->belongsToMany(Enemy::class, 'enemy_has_skill');
    }
}
