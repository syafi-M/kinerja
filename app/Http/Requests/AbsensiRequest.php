<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiRequest extends FormRequest
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
            'user_id'       => 'required',
            'kerjasama_id'  => 'required',
            'shift_id'      => 'nullable',
            'perlengkapan'  => 'required',
            'keterangan'    => 'required',
            'absensi_type_masuk'  => 'required',
            'absensi_type_pulang'  => 'nullable',
            'image'       => 'nullable',
            'deskripsi' => 'nullable',
            'point_id' => 'nullable',
            'subuh' => 'nullable',
            'dzuhur' => 'nullable',
            'asar' => 'nullable',
            'magrib' => 'nullable',
            'isya' => 'nullable',
            'msk_lat' => 'nullable',
            'msk_long' => 'nullable',
            'sig_lat' => 'nullable',
            'sig_long' => 'nullable',
            'plg_lat' => 'nullable',
            'plg_long' => 'nullable',
            'masuk' => 'nullable',
            'tukar' => 'nullable',
            'lembur' => 'nullable',
            'terus' => 'nullable',
            'tukar_id' => 'nullable'

        ];
    }
}
