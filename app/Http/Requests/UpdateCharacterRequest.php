<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCharacterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class' => 'sometimes|required|in:Warrior,Mage,Archer',
            'attack' => 'sometimes|required|integer|min:0',
            'defense' => 'sometimes|required|integer|min:0',
            'max_health_points' => 'sometimes|required|integer|min:1',
            'max_magic_points' => 'sometimes|required|integer|min:0',
            'character_image_url' => 'sometimes|required|string|max:45'
        ];
    }
}
