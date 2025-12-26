@component('mail::message')
# Kode Verifikasi

Hai, Sobat!

Untuk melanjutkan proses verifikasi, silakan masukkan kode OTP berikut:

Kode: **{{ $otp }}** {{-- This is the Markdown syntax for bold --}}

Kode ini akan kedaluwarsa pada pukul: **{{ $expiresAt }}**.

Jangan berikan kode ini kepada siapapun untuk menjaga keamanan akunmu.

Jika kamu tidak melakukan permintaan ini, abaikan saja email ini.

Salam hangat,<br>
Tim Sac-Po
@endcomponent
