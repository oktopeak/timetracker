<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'number_of_days' => 'required|integer|min:1',
            'year' => 'required|string|size:4',
            'days_left' => 'nullable|integer|min:0',
        ];
    
        if ($this->isMethod('patch')) { 
            $rules = [
                'number_of_days' => 'required|integer|min:1'
            ];
        }
    
        return $rules;
    }
}
