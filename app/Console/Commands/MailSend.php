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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kantor = Shift::with('Client')->where('client_id', 1)->first(); 
        $shift = Shift::all();
        $abs = Absensi::with(['Shift', 'Kerjasama'])->get();
        $user = User::all();
        $apiKey = 'a5433eaea029fc2f3a148571a4b60e73';
        $mailtrap = new MailtrapClient(new Config($apiKey));
        
        foreach( $user as $u)
        {
             foreach ($abs as $key) {
                if ($key->user_id == $userId && $key->kerjasama->client_id != 1) {
                    foreach ($shift as $s) {
                        $carNow = Carbon::now()->format('H:m:s');
                        $pape = Carbon::createFromFormat('H:m:s', $key->shift->jam_end)->format('H:m:s');
                        
                        if($key->shift->shift_name != 'Shift Malam' && $key->shift->shift_name != 'MALAM' && $key->shift_id == $s->id && $key->absensi_type_pulang == null && $carNow->addHour()->greaterThan($pape) && $key->keterangan != 'izin') 
                        {
                            $datLast = Absensi::where('id', $key->id)->latest();
                            $email = (new Email())
                                ->from(new Address('mailtrap@absensi-sac.sac-po.com', 'Announcement'))
                                ->to(new Address($datLast->user->email));
                            
                            $email->getHeaders()
                                ->add(new TemplateUuidHeader('83842ca8-263e-4867-9918-4f9ff1eb5ed9'));
                            
                            $response = $mailtrap->sending()->emails()->send($email);
                        }
                        
                        if($key->shift->shift_name == 'Shift Malam' && $key->shift->shift_name == 'MALAM' && $key->shift_id == $s->id && $key->absensi_type_pulang == null && $carNow->addHour()->greaterThan($pape) && $key->keterangan != 'izin') 
                        {
                            $datLast = Absensi::where('id', $key->id)->latest();
                            $email = (new Email())
                                ->from(new Address('mailtrap@absensi-sac.sac-po.com', 'Announcement'))
                                ->to(new Address($datLast->user->email));
                            
                            $email->getHeaders()
                                ->add(new TemplateUuidHeader('83842ca8-263e-4867-9918-4f9ff1eb5ed9'));
                            
                            $response = $mailtrap->sending()->emails()->send($email);
                        }
                    }  
                }
            }
        }
    }
}
