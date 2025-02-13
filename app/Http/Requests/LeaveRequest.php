<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
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
            'leave_type_id' => 'required|int|exists:leave_types,id',
            'leave_start' => 'required|date|after_or_equal:today',
            'leave_end' => 'required|date|after_or_equal:leave_start',
            'notes' => 'nullable|string|max:500'
        ];

        if ($this->isMethod('patch')) {
            $rules = [
                'status' => 'required|in:approved,denied'
            ];
        }
        return $rules;
    }
}
