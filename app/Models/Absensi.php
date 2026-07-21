<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kerjasama_id',
        'shift_id',
        'perlengkapan',
        'keterangan',
        'absensi_type_masuk',
        'tanggal_absen',
        'tipe_id',
        'absensi_type_pulang',
        'image',
        'deskripsi',
        'point_id',
        'subuh',
        'fotoSubuh',
        'subuh_lat',
        'subuh_long',
        'dzuhur',
        'fotoDzuhur',
        'dzuhur_lat',
        'dzuhur_long',
        'asar',
        'fotoAsar',
        'asar_lat',
        'asar_long',
        'maghrib',
        'fotoMaghrib',
        'maghrib_lat',
        'maghrib_long',
        'isya',
        'fotoIsya',
        'isya_lat',
        'isya_long',
        'msk_lat',
        'msk_long',
        'sig_lat',
        'sig_long',
        'plg_lat',
        'plg_long',
        'masuk',
        'tukar',
        'lembur',
        'terus',
        'tukar_id'
    ];
    
    protected $guarded = ['user_id', 'kerjasama_id', 'shift_id', 'perlengkapan', 'keterangan', 'absensi_type_masuk', 'tanggal_absen', 'tipe_id', 'absensi_type_pulang', 'image', 'deskripsi', 'point_id', 'subuh', 'fotoSubuh', 'subuh_lat', 'subuh_long', 'dzuhur', 'fotoDzuhur', 'dzuhur_lat', 'dzuhur_long', 'asar', 'fotoAsar', 'asar_lat', 'asar_long', 'maghrib', 'fotoMaghrib', 'maghrib_lat', 'maghrib_long', 'isya', 'fotoIsya', 'isya_lat', 'isya_long', 'msk_lat', 'msk_long', 'sig_lat', 'sig_long', 'plg_lat', 'plg_long', 'masuk', 'tukar', 'lembur', 'terus', 'tukar_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'kerjasama_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function tipeAbsensi()
    {
        return $this->belongsTo(TipeAbsensi::class, 'tipe_id', 'id');
    }

    
    public function perlengkapan()
    {
        return $this->belongsToMany(Perlengkapan::class, 'absensi_perlengkapan');
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function getStatusSholatAttribute()
    {
        return (object) [
            'Subuh' => $this->subuh,
            'Zuhur' => $this->dzuhur,
            'Ashar' => $this->asar,
            'Maghrib' => $this->maghrib,
            'Isya' => $this->isya,
        ];
    }

}
