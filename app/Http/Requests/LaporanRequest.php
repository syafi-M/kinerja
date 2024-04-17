<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'client_id' => 'required',
            'image1' => 'nullable|mimes:png,jpg,jpeg|max:6144',
            'image2' => 'nullable|mimes:png,jpg,jpeg|max:6144',
            'image3' => 'nullable|mimes:png,jpg,jpeg|max:6144',
            'image4' => 'nullable|mimes:png,jpg,jpeg|max:6144',
            'image5' => 'nullable|mimes:png,jpg,jpeg|max:6144',
            'keterangan' => 'nullable',
            'pekerjaan' => 'required',
            'nilai' => 'nullable'
        ];
    }
}
