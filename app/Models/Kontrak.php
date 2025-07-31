<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Kontrak extends Model
{
    use HasApiTokens;
    protected $connection = 'mysql2';
    protected $table = 'p_g_j__kontraks';
    
    protected $fillable = [
        'no_srt',
        'ttd',
        'send_to_operator',
        'send_to_atasan',
        'nama_pk_kda',
        'tempat_lahir_pk_kda',
        'tgl_lahir_pk_kda',
        'nik_pk_kda',
        'alamat_pk_kda',
        'jabatan_pk_kda',
        'unit_pk_kda',
        'status_pk_kda',
    ];
}
