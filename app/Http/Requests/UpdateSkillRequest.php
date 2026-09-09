<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
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
            'skill_name' => 'sometimes|required|string|max:60',
            'description' => 'sometimes|required|string|max:200',
            'damage_skill' => 'sometimes|required|integer|min:0',
            'skill_cost_magic_points' => 'sometimes|required|integer|min:0',
        ];
    }
}
