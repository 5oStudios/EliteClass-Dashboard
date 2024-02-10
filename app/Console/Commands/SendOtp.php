<?php

namespace App\Console\Commands;

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

        try {
            $pdo = new \PDO('mysql:host='.$_ENV['PROD_DB_HOST'].';dbname='.$_ENV['PROD_DB_DATABASE'],
                $_ENV['PROD_DB_USERNAME'], $_ENV['PROD_DB_PASSWORD']);
        }catch (\PDOException $e) {
            $this->error('Database connection failed');
            return 0;

        }

        // get all users that have otp available and not expired
        $stmt = $pdo->prepare(
            "SELECT email, mobile, two_factor_code 
                    FROM users 
                    WHERE two_factor_code IS NOT NULL AND two_factor_expires_at > NOW()
                    ");
        $stmt->execute();
        $otps = $stmt->fetchAll();

        foreach ($otps as $otp){
            if ($otp && $otp['mobile'] !== "") {

                $sentOpts = Cache::get('otp_sent', []);

                if (in_array($otp['two_factor_code'], $sentOpts)){
                    continue;
                }

                try {
                    $response = Http::asForm()->post('https://app.whacenter.com/api/send', [
                        'device_id' => env('WHACENTER_DEVICE_ID'),
                        'number' => $otp['mobile'],
                        'message' => 'Your Elite-Class OTP is: ' . $otp['two_factor_code'],
                    ]);

                    if ($response->successful()){
                        $sentOpts = array_merge($sentOpts, [$otp['two_factor_code']]);
                        Cache::put('otp_sent', $sentOpts, now()->addMinutes(10));
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
