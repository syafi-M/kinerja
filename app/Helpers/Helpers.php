<?php
use Intervention\Image\Facades\Image;


function UploadImage($request, $NameFile)
{

    $file = $request->file($NameFile);
    if ($file != null && $file->isValid()) {
        
    $img = Image::make($file);
    $imageSize = $img->filesize();
    
            $image = Image::make($file);
            $extensions = $file->getClientOriginalExtension();
            $randomNumber = mt_rand(1, 999999999999);
            $rename = 'data' . $randomNumber . '.' . $extensions;
            
            $path = public_path('storage/images/' . $rename);
            $img = Image::make($file->getRealPath());
            $img->save($path, 80);
        
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
            $randomNumber = mt_rand(1, 999999999999);
            $rename = 'data' . $randomNumber . '.' . $extensions;
            
            $path = public_path('storage/images/' . $rename);
            $img = Image::make($file->getRealPath());
            $img->save($path, 80);
        
            return $rename;
      
    }
}

function toRupiah($angka)
{
    if(strpos($angka, '.')){
        return "Rp.". $angka;
    }else{
        return "Rp.". number_format($angka, 0, '.','.');
    }
}
