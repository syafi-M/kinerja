<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Mailtrap\EmailHeader\Template\TemplateUuidHeader;
use Mailtrap\EmailHeader\Template\TemplateVariableHeader;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbsensiNotification;

class MailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mail-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kantor = Shift::with('Client')->where('client_id', 1)->first(); 
        $shift = Shift::all();
        $abs = Absensi::with(['Shift', 'Kerjasama', 'user'])->whereNot('user_id', 2)->where('kerjasama_id', 1)->get();
        $user = User::whereNot('id', 2)->where('kerjasama_id', 1)->get();
        $apiKey = 'a5433eaea029fc2f3a148571a4b60e73';
        // $mailtrap = new MailtrapClient(new Config($apiKey));
        
        foreach ($abs as $a) {
            try {
                if ($a->user_id == 11) {
                    Mail::to($a->user->email)->send(new AbsensiNotification());
                    $this->info('Daily report email sent successfully to ' . $a->user->email);
                }
            } catch (\Exception $e) {
                $this->error('Failed to send email to ' . $a->user->email . '. Error: ' . $e->getMessage());
            }
        }
        
        // foreach( $user as $u)
        // {
        //      foreach ($abs as $key) {
        //         if ($key->user_id == $userId && $key->kerjasama->client_id != 1) {
        //             foreach ($shift as $s) {
        //                 $carNow = Carbon::now()->format('H:m:s');
        //                 $pape = Carbon::createFromFormat('H:m:s', $key->shift->jam_end)->format('H:m:s');
                        
        //                 if($key->shift->shift_name != 'Shift Malam' && $key->shift->shift_name != 'MALAM' && $key->shift_id == $s->id && $key->absensi_type_pulang == null && $carNow->addHour()->greaterThan($pape) && $key->keterangan != 'izin') 
        //                 {
        //                     $datLast = Absensi::where('id', $key->id)->latest();
        //                     $email = (new Email())
        //                         ->from(new Address('mailtrap@absensi-sac.sac-po.com', 'Announcement'))
        //                         ->to(new Address($datLast->user->email));
                            
        //                     $email->getHeaders()
        //                         ->add(new TemplateUuidHeader('83842ca8-263e-4867-9918-4f9ff1eb5ed9'));
                            
        //                     $response = $mailtrap->sending()->emails()->send($email);
        //                 }
                        
        //                 if($key->shift->shift_name == 'Shift Malam' && $key->shift->shift_name == 'MALAM' && $key->shift_id == $s->id && $key->absensi_type_pulang == null && $carNow->addHour()->greaterThan($pape) && $key->keterangan != 'izin') 
        //                 {
        //                     $datLast = Absensi::where('id', $key->id)->latest();
        //                     $email = (new Email())
        //                         ->from(new Address('mailtrap@absensi-sac.sac-po.com', 'Announcement'))
        //                         ->to(new Address($datLast->user->email));
                            
        //                     $email->getHeaders()
        //                         ->add(new TemplateUuidHeader('83842ca8-263e-4867-9918-4f9ff1eb5ed9'));
                            
        //                     $response = $mailtrap->sending()->emails()->send($email);
        //                 }
        //             }  
        //         }
        //     }
        // }
    }
}
