<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AbsenUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:absen-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'how to run = php artisan scheduler:work';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // Handle to auto send absensi_type_pulang when time has passed
        $kantor = Client::where('id', 1)->first(); 
        $shift = Shift::all();
        // $abs = Absensi::with(['Shift', 'Kerjasama', 'User'])->get();
        $user = User::all();
        $currentTime = Carbon::now()->format('H:m:s');

        // Handle the case where if not present pulang
        // if != client Kantor
        // CONTROL OF AUTO UPDATE DATA 
    //     // JANGAN DI UBAH UBAH, SELAIN YANG BERTANGGUNG JAWAB DAN MENGERTI KODING DIBAWAH INI, ADA EROR BUKAN TANGGUNG JAWAB KAMI
        if(!$kantor)
        {
            foreach ($user as $u) {
                $userId = $u->id;
                foreach ($abs as $key) {
                    if ($key->shift_id != null && $key->user_id == $userId && $key->kerjasama->client_id != 1) {
                        foreach ($shift as $s) {
                            if($key->shift->shift_name != 'Malam' && $key->shift->shift_name != 'Shift Malam' && $key->shift->shift_name != 'MALAM' && $key->shift_id == $s->id && $currentTime > $key->shift->jam_end && $key->keterangan != 'izin') 
                            {
                                $abs->where('id', $key->id)->whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
                            }
                            if($key->shift->shift_name == 'Malam' && $key->shift->shift_name == 'Shift Malam' && $key->shift->shift_name == 'MALAM' && $key->shift_id == $s->id && $currentTime == $key->shift->jam_end && $key->keterangan != 'izin') 
                            {
                                $abs->where('id', $key->id)->whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
                            }
                        }  
                    }
                }
            }
        }
    //      // JANGAN DI UBAH UBAH, SELAIN YANG BERTANGGUNG JAWAB DAN MENGERTI KODING DIBAWAH INI, ADA EROR BUKAN TANGGUNG JAWAB KAMI
    // //Handle Shift Kantor
    //      // JANGAN DI UBAH UBAH, SELAIN YANG BERTANGGUNG JAWAB DAN MENGERTI KODING DIBAWAH INI, ADA EROR BUKAN TANGGUNG JAWAB KAMI
    
            if ($kantor && $currentTime > '17:00:00') {
                foreach ($abs as $key) {
                    if ($key->shift_id != null && $currentTime > '14:30:00' && $currentTime == $key->shift->jam_end && $key->shift->client_id == 1 && $key->keterangan != 'izin') {
                        $abs->where('id', $key->id)->whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
                    }
                }
            }
            
            if($kantor && $currentTime > '23:59:59')
            {
                foreach ($user as $arr) {
                    foreach ($abs as $key)
                    {
                        if ($arr->id == 289 && $currentTime > '23:59:59' && $key->keterangan != 'izin') {
                          $abs->where('user_id', 289)->whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
                        }
                    }
                }
            }
        // $abs = Absensi::with(['Shift', 'Kerjasama', 'User'])
        //     ->join('shifts', 'absensis.shift_id', '=', 'shifts.id')
        //     ->join('users', 'absensis.user_id', '=', 'users.id')
        //     ->select('absensis.*', 'shifts.jam_end', 'users.id as user_id')
        //     ->get();

        // $absByUser = $abs->groupBy('user_id');
        
        // if(!$kantor)
        // {
        //     foreach ($absByUser as $userId => $absList) {
        //         foreach ($absList as $key) {
        //             if ($key->kerjasama->client_id != 1 && $key->absensi_type_pulang == null && Carbon::now()->gt(Carbon::parse($key->shift->jam_end)) && $key->keterangan != 'izin') {
        //                 Absensi::where('id', $key->id)->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
        //             }
        //         }
        //     }
        // }
        
        // if ($kantor && Carbon::now()->gt(Carbon::parse('14:30:00'))) {
        //     foreach ($absByUser[$kantor->id] ?? [] as $key) {
        //         if ($key->absensi_type_pulang == null && $key->keterangan != 'izin' && Carbon::now()->eq($key->shift->jam_end)) {
        //             Absensi::where('id', $key->id)->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
        //         }
        //     }
        // }
        
        // if($kantor && Carbon::now()->gt(Carbon::parse('23:59:59')))
        // {
        //     Absensi::where('user_id', 289)
        //         ->where('absensi_type_pulang', null)
        //         ->where('keterangan', '!=', 'izin')
        //         ->update(['absensi_type_pulang' => 'Tidak Absen Pulang']);
        // }
    }
}
 // JANGAN DI UBAH UBAH, SELAIN YANG BERTANGGUNG JAWAB DAN MENGERTI KODING DIBAWAH INI, ADA EROR BUKAN TANGGUNG JAWAB KAMI
