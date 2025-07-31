<?php
// use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic as Image;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Notifications\OtpNotif;
use App\Otp\BasicOtp;

function UploadImage($request, $NameFile)
{
    Image::configure(['driver' => 'imagick']);
    $file = $request->file($NameFile);
    if ($file != null && $file->isValid()) {
        
    $img = Image::make($file);
    $imageSize = $img->filesize();
    
            $image = Image::make($file);
            $extensions = $file->getClientOriginalExtension();
            $randomNumber = mt_rand(1, 999999);
            $rename = 'data' . $randomNumber . '.' . $extensions;
            
            $path = public_path('storage/images/' . $rename);
            $img = Image::make($file->getRealPath());
            $img->resize(450, 450);
            $img->save($path, 13);
        
            return $rename;
      
    }
}

function UploadImageV2($request, $NameFile)
{

    $file = $request->file($NameFile);
    if ($file != null && $file->isValid()) {
        
    $img = Image::make($file);
    $imageSize = $img->filesize();
    
            $image = Image::make($file);
            $extensions = $file->getClientOriginalExtension();
            $randomNumber = mt_rand(1, 999999);
            $rename = 'data' . $randomNumber . '.' . $extensions;
            
            $path = public_path('storage/images/' . $rename);
            $img = Image::make($file->getRealPath());
            $img->save($path, 85);
        
            return $rename;
      
    }
}

function UploadImageUser($request, $NameFile)
{

    $file = $request->file($NameFile);
    if ($file != null && $file->isValid()) {
        
    $img = Image::make($file);
    $imageSize = $img->filesize();
    
            $image = Image::make($file);
            $extensions = $file->getClientOriginalExtension();
            $randomNumber = mt_rand(1, 999999);
            $rename = 'data' . $randomNumber . '.' . $extensions;
            
            $path = public_path('storage/user/' . $rename);
            $img = Image::make($file->getRealPath());
            $img->save($path, 85);
        
            return $rename;
      
    }
}

function UploadFile($request, $NameFile)
{
    $file = $request->file($NameFile);
    if($file != null && $file->isValid()) {

        $extensions = $file->getClientOriginalExtension();
        $randomName = mt_rand(1, 9999999);
        $rename = 'pdf' . $randomName . '.' . $extensions;
        $path = public_path('storage/file/' . $rename);
        $file->storeAs('pdf', $rename, 'public');

        return $rename;
    }
}

function validateEmail($email)
{
    $validator = Validator::make(['email' => $email], [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        abort(422, 'Email tidak valid.');
    }
}

function sendOtpReg($email)
{
    $otp = rand(100000, 999999);
    $expiresAt = now()->addMinutes(30); // bisa atur di config/otp.php

    // Simpan OTP ke cache
    Cache::put("otp_{$email}", $otp, $expiresAt);

    // Kirim OTP lewat email
    Notification::route('mail', $email)
        ->notify(new OtpNotif($otp, $expiresAt->format('H:i')));

    return [
        'status' => 'otp.sent',
        'otp' => app()->environment('local') ? $otp : null
    ];
}

function verifOtpReg($email, $code)
{
    $cachedOtp = Cache::get("otp_{$email}");

    if (!$cachedOtp) {
        return ['status' => 'otp.expired', 'valid' => false];
    }

    if ($cachedOtp != $code) {
        return ['status' => 'otp.invalid', 'valid' => false];
    }

    // OTP cocok, hapus dari cache
    // Cache::forget("otp_{$email}");

    return ['status' => 'otp.validated', 'valid' => true, 'email' => $email];
}

function toRupiah($angka)
{
    if (strpos($angka, '.')){
        return "Rp. ". $angka;
    } else if ($angka == '-') {
        return "Rp. ". $angka;
    } else{
        return "Rp. ". number_format($angka, 0, '.','.');
    }
}

function normalizePhone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone); // remove non-digit characters
    
    if ($phone === '') {
        return null;
    }

    if (strpos($phone, '62') === 0) {
        return $phone;
    } elseif (strpos($phone, '0') === 0) {
        return '62' . substr($phone, 1);
    }

    return $phone;
}

