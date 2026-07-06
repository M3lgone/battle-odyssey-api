<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
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
}
