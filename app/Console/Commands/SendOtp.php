<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SendOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (!env('ENABLE_OTP_CRON')) {
            $this->info('OTP Fetch is disabled!');
            return 0;
        }

        // select all users that have otp available and not expired
        $users = User::whereNotNull('two_factor_code')
            ->where('two_factor_expires_at', '>', now())
            ->get()
        ;

        foreach ($users as $user){
            if ($user && $user->mobile !== "") {

                $sentOpts = Cache::get('otp_sent', []);

                if (in_array($user->two_factor_code, $sentOpts)){
                    continue;
                }

                try {
                    $response = Http::asForm()->post('https://app.whacenter.com/api/send', [
                        'device_id' => env('WHACENTER_DEVICE_ID'),
                        'number' => $user->mobile,
                        'message' => 'Your Elite-Class OTP is: ' . $user->two_factor_code,
                    ]);

                    if ($response->successful()){
                        $sentOpts = array_merge($sentOpts, [$user->two_factor_code]);
                        Cache::put('otp_sent', $sentOpts, now()->addDay());
                    }

                } catch (\Exception $e) {
                     $this->error('Error: ' . $e->getMessage());
                }
            }
        }
        $this->info('OTP Fetch is done!');
        return 0;
    }
}
