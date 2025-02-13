<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalHolidayRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
        ];
    
        if ($this->isMethod('patch')) { 
            $rules = [
                'name' => 'nullable|string|max:255',
                'date' => 'nullable|date'
            ];
        }
    
        return $rules;  
    }
}
