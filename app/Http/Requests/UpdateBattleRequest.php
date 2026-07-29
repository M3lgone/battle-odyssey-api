<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBattleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $battle = $this->route('battle');

        return $battle && $battle->game->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $battle = $this->route('battle');

        return [
            'result' => 'required|in:win,loss,flee',
            'character_current_hp' => 'required|integer|min:0',
            'character_current_mp' => 'required|integer|min:0',
            'total_damage_dealt' => 'required|integer|min:0',
            'total_damage_received' => 'required|integer|min:0',
            'enemies' => 'required|array|min:1',
            'enemies.*.id' => [
                'required',
                'integer',
                Rule::exists('battle_has_enemy', 'enemy_id')->where('battle_id', $battle->id),
            ],
            'enemies.*.current_hp' => 'required|integer|min:0',
            'enemies.*.current_mp' => 'required|integer|min:0',
        ];
    }
}
