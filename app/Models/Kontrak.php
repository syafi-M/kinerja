<?php

namespace App\Models;

use Carbon\Carbon;
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
        'tgl_mulai_kontrak',
        'tgl_selesai_kontrak',
    ];

    public function isPending()
    {
        return is_null($this->tgl_selesai_kontrak) && $this->send_to_operator == '0' && $this->created_at->addDays(30)->isFuture();
    }

    public function isProses()
    {
        return $this->tgl_selesai_kontrak && is_null($this->ttd_atasan) && Carbon::parse($this->tgl_selesai_kontrak)->isFuture();
    }

    public function isActive()
    {
        return $this->tgl_selesai_kontrak && $this->send_to_operator == '1' && $this->send_to_atasan == '0' && Carbon::parse($this->tgl_selesai_kontrak)->isFuture();
    }
}
