<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCharacterRequest extends FormRequest
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
            'class' => 'required|in:Warrior,Mage,Archer',
            'attack' => 'required|integer|min:0',
            'defense' => 'required|integer|min:0',
            'max_health_points' => 'required|integer|min:1',
            'max_magic_points' => 'required|integer|min:0',
            'character_image_url' => 'required|string'
        ];
    }
}
