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
        Schema::create('battle_has_enemy', function (Blueprint $table) {
            $table->foreignId('battle_id')->constrained('battles')->cascadeOnDelete();
            $table->foreignId('enemy_id')->constrained('enemies')->cascadeOnDelete();
            $table->integer('current_hp');
            $table->integer('current_mp');
            $table->primary(['battle_id', 'enemy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battle_has_enemy');
    }
};
