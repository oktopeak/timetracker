<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
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
            'pin' => 'required|string|size:4',
            'action' => 'required|in:check-in,check-out'
        ];

        if($this->isMethod('get')){
            $rules = [
                'date' => 'nullable|date|date_format:Y-m-d',
            ];
        }
        return $rules;
    }
}
