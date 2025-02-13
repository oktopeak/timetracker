<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9\-\+]{9,15}$/',
            'email' => 'required|email|unique:users,email',
            'position_id' => 'required|exists:positions,id',
            'birthday' => 'nullable|date|date_format:Y-m-d',
            'password' => 'required|string|min:8',
            'joined_team' => 'required|date|date_format:Y-m-d',
            'number_of_days' => 'required|integer|min:1',
        ];

        if ($this->isMethod('patch')) {
            $rules = [
                'name' => 'nullable|string|max:255',
                'surname' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|regex:/^[0-9\-\+]{9,15}$/',
                'position_id' => 'nullable|exists:positions,id',
                'birthday' => 'nullable|date',
                'password' => 'nullable|string|min:8',
                'joined_team' => 'nullable|date',
            ];
        }
        return $rules;
    }
}
