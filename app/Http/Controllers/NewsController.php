<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::paginate(50);
        return view('admin.news.index', compact('news'));
    }
    
    public function create()
    {
        return view('admin.news.create');
    }
    
    public function store(NewsRequest $request)
    {
        $news = new News();
        $news = [
            'image' => $request->image,
            'tanggal_lihat' => $request->tanggal_lihat,
            'tanggal_tutup' => $request->tanggal_tutup
        ];
        
         if ($request->hasFile('image')) {
            $news['image'] = UploadImage($request, 'image');
        }else{
            toastr()->error('Image harus ditambahkan', 'error');
        }
        
         try {
            News::create($news);
        } catch(\Illuminate\Database\QueryException $e){
           toastr()->error('Data Sudah Ada', 'error');
           return redirect()->back();
        }
            toastr()->success('News Berhasil Ditambahkan', 'success');
            return redirect()->to(route('news.index'));
    }
    
    public function edit($id)
    {
        $newsId = News::findOrFail($id);
        if ($newsId != null) {
            return view('admin.news.edit', compact('newsId'));
        }
        toastr()->error('Data Tidak Ditemukan', 'error');
        return redirect()->back();
    }
    
    public function update(Request $request, $id)
    {
        $news = [
            'image' => $request->image,
            'tanggal_lihat' => $request->tanggal_lihat,
            'tanggal_tutup' => $request->tanggal_tutup
        ];
        
        if($request->hasFile('image'))
        {
            if($request->oldimage)
            {
                Storage::disk('public')->delete('images/' . $request->oldimage);
            }

            $news['image'] = UploadImage($request, 'image');
        }else{
            $news['image'] = $request->oldimage;
        }
         try {
            News::findOrFail($id)->update($news);
        } catch(\Illuminate\Database\QueryException $e){
           toastr()->error('Data Tidak Tersimpan', 'error');
           return redirect()->back();
        }
        toastr()->success('Data berhasil diedit', 'success');
        return redirect()->to(route('news.index'));
    }
    
    public function destroy($id)
    {
        $news = News::find($id);
        if ($news != null) {
            if ($news->image == null) {
                toastr()->error('Logo Tidak Ditemukan', 'error');
            }
                if ($news->image) {
                    Storage::disk('public')->delete('images/'.$news->image);
                }
        }
        $news->delete();
        toastr()->error('Data Tidak Ditemukan', 'error');
        return redirect()->back();
    }
    
    public function NewsBefore()
    {
        $newsId = News::all();
        return view('tes.index', compact('newsId'));
    }
    
      
    public function NewsDownload($id)
    {
        $newsId = News::findOrFail($id);
        
        $filepath = public_path('storage/images/').$newsId->image;
        return Response::download($filepath);
        
    }

}
