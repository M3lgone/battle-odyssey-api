<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->enum('result', ['win', 'loss', 'flee', 'ongoing']);
            $table->foreignId('character_id')->constrained('characters');
            $table->foreignId('game_id')->constrained('games');
            $table->integer('character_current_hp');
            $table->integer('character_current_mp');
            $table->integer('enemy_current_hp');
            $table->integer('enemy_current_mp');
            $table->integer('total_damage_dealt');
            $table->integer('total_damage_received'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
