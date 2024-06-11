<?php
// use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic as Image;


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
            $img->save($path, 13);
        
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

function toRupiah($angka)
{
    if(strpos($angka, '.')){
        return "Rp. ". $angka;
    }else{
        return "Rp. ". number_format($angka, 0, '.','.');
    }
}
