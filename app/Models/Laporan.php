<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'ruangan_id',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'keterangan',
        'pekerjaan',
        'nilai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
