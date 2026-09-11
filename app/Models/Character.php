<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'class',
        'attack',
        'defense',
        'max_health_points',
        'max_magic_points',
        'character_image_url'
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'character_has_skill');
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function battles()
    {
        return $this->hasMany(Battle::class);
    }
}
