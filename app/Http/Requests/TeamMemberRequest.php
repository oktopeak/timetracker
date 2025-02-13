<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
            'position_id' => 'required|exists:positions,id',
            'role' => 'nullable|string|in:manager,member'
        ];

        if ($this->isMethod('patch')) { 
            $rules = [
                'position_id' => 'nullable|exists:positions,id',
                'role' => 'nullable|string|in:manager,member'
            ];
        }
        return $rules;
    }
}
