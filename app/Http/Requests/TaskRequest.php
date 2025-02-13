<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
        $rules =  [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'nullable|string',
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
            'created_at' => 'nullable|date',
        ];

        if ($this->isMethod('patch')) {
            $rules = [
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'time' => 'nullable|string',
                'created_at' => 'nullable|date',
            ];
        }

        if($this->isMethod('get')){
            $rules = [
                'team_id' => 'nullable|exists:teams,id',
                'user_id' => 'nullable|exists:users,id',
                'year' => 'nullable|integer|min:2000|max:'.date('Y'),
                'month' => 'nullable|integer|min:1|max:12',
            ];
        }

        return $rules;
    }
}
