<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'class',
        'attack',
        'defense',
        'max_health_points',
        'max_magic_points',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'character_has_skill');
    }
}
