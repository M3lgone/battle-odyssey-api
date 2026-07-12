<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enemy extends Model
{
    /** @use HasFactory<\Database\Factories\EnemyFactory> */
    use HasFactory;

    protected $fillable = [
        'enemy_name',
        'max_health_points',
        'max_magic_points',
        'attack',
        'defense',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'enemy_has_skill');
    }
}
