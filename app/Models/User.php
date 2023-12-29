<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Creagia\LaravelSignPad\Concerns\RequiresSignature;
use Creagia\LaravelSignPad\Contracts\CanBeSigned;
use Creagia\LaravelSignPad\Contracts\ShouldGenerateSignatureDocument;
use Creagia\LaravelSignPad\Templates\BladeDocumentTemplate;
use Creagia\LaravelSignPad\Templates\PdfDocumentTemplate;
use Creagia\LaravelSignPad\SignatureDocumentTemplate;
use Creagia\LaravelSignPad\SignaturePosition;

class User extends Authenticatable implements CanBeSigned
{
    use HasApiTokens, HasFactory, Notifiable, RequiresSignature;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nama_lengkap',
        'kerjasama_id',
        'email',
        'password',
        'image',
        'devisi_id',
        'status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function getSignatureDocumentTemplate(): SignatureDocumentTemplate
    {
        return new SignatureDocumentTemplate(
            outputPdfPrefix: 'document', // optional
            // template: new BladeDocumentTemplate('pdf/my-pdf-blade-template'), // Uncomment for Blade template
            template: new PdfDocumentTemplate(storage_path('pdf/template.pdf')), // Uncomment for PDF template
            signaturePositions: [
                 new SignaturePosition(
                     signaturePage: 1,
                     signatureX: 20,
                     signatureY: 25,
                 ),
                 new SignaturePosition(
                     signaturePage: 2,
                     signatureX: 25,
                     signatureY: 50,
                 ),
            ]               
        );
    }
    
     public function hasReceivedNotificationToday()
    {
        return $this->last_notification_date === Carbon::today()->toDateString();
    }

    public function Role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function Kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'kerjasama_id', 'id');
    }

    public function Absensi()
    {
        return $this->hasMany(Absensi::class);
    }
    public function Divisi()
    {
        return $this->belongsTo(Divisi::class, 'devisi_id', 'id');
    }
    public function JadwalUser(): HasMany
    {
        return $this->hasMany(JadwalUser::class);
    }

    public function CheckPoint()
    {
        return $this->hasMany(User::class);
    }
    public function Rating()
    {
        return $this->hasMany(Rating::class);
    }
    public function Status()
    {
        return $this->belongsTo(Status::class);
    }
    
}
