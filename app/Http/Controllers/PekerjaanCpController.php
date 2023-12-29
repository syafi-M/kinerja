<?php

namespace App\Http\Controllers;

use App\Http\Requests\PekerjaanCpRequest;
use App\Models\PekerjaanCp;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Kerjasama;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PekerjaanImport;
use Maatwebsite\Excel\Validators\ValidationException;

use Illuminate\Http\Request;

class PekerjaanCpController extends Controller
{
   public function index()
   {
       $pcp = PekerjaanCp::paginate(50);
       return view('admin.pekerjaanCp.index', compact('pcp'));
   }
   
   public function create()
   {
       $user = User::all();
       $divisi = Divisi::all();
       $kerjasama = Kerjasama::all();
       return view('admin.pekerjaanCp.create', compact('kerjasama', 'user', 'divisi'));
   }
   
   public function store(PekerjaanCpRequest $request)
   {
    //   dd($request->all());
       $pcp = new PekerjaanCp;
       
       $pcp = [
            'user_id' => $request->user_id,
            'divisi_id' => $request->devisi_id,
            'kerjasama_id' => $request->kerjasama_id,
            'name' => $request->name,
            'type_check' => $request->type_check
            
       ];
       PekerjaanCp::create($pcp);
       toastr()->success('Data Berhasil Di Tambahkan', 'success');
       return to_route('pekerjaanCp.index');
   }
   
   public function edit($id)
   {
       $user = User::all();
       $kerjasama = Kerjasama::all();
       $pcpId = PekerjaanCp::findOrFail($id);
       $divisi = Divisi::all();
       return view('admin.pekerjaanCp.edit', compact('pcpId', 'kerjasama', 'user', 'divisi'));;
   }
   
   public function update(Request $request, $id)
   {
        $pcp = [
            'user_id' => $request->user_id,
            'divisi_id' => $request->devisi_id,
            'kerjasama_id' => $request->kerjasama_id,
            'name' => $request->name,
            'type_check' => $request->type_check
            
       ];
        $Datapcp = PekerjaanCp::findOrFail($id);
        $Datapcp->update($pcp);
        toastr()->success('Data Berhasil Di Edit', 'success');
        return to_route('pekerjaanCp.index');
   }
   
   public function destroy($id)
   {
    $pcpId = PekerjaanCp::findOrFail($id);
    $pcpId->delete();
    toastr()->warning('Data Berhasil Di Deleted', 'warning');
    return redirect()->back();
   }
   
    public function import(Request $request)
    {
        try {
            Excel::import(new PekerjaanImport,  $request->file);
            toastr()->success('Data Berhasil Di Upload', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle other exceptions
             if ($e->errorInfo[1] === 1062) { // 1062 is the MySQL error code for a unique constraint violation
                $errorMessage = $e->getMessage();

                // Check if the error message contains information about a duplicate entry
                if (strpos($errorMessage, 'Duplicate entry') !== false) {
                    // Extract the relevant part of the error message
                    preg_match("/Duplicate entry '(.+)' for key/", $errorMessage, $matches);
        
                    if (isset($matches[1])) {
                        $duplicateValue = $matches[1];
        
                        // Handle the duplicate value as needed
                        toastr()->error('Data Gagal Di Upload', 'Error');
                        return redirect()->back()->with('failures', $duplicateValue);
                    }
                }
             }
        }
        
        
    }
   
}
