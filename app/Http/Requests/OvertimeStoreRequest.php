<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OvertimeStoreRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'date_overtime' => 'required|date',
            'desc' => 'required|string',
            'type_overtime' => ['required', Rule::in(['shift', 'jam', 'lainnya'])],
            'type_overtime_manual' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn() => in_array($this->type_overtime, ['jam', 'lainnya'], true))],
        ];
    }
}
