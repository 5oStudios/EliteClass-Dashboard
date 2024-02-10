<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SendOtpController extends Controller
{
    public function sendOtp(Request $request)
    {

        // create PDO connection and try it to avoid throwing exception
        try {
            $pdo = new \PDO('mysql:host='.$_ENV['PROD_DB_HOST'].';dbname='.$_ENV['PROD_DB_DATABASE'], $_ENV['PROD_DB_USERNAME'], $_ENV['PROD_DB_PASSWORD']);
        }catch (\PDOException $e) {
            return response()->json([
                'message' => 'Database connection failed'
            ], 500);
        }
        // get all users that have otp available and not expired
        $stmt = $pdo->prepare("SELECT email, mobile, two_factor_code FROM users WHERE email = :email AND two_factor_code IS NOT NULL AND two_factor_expires_at > NOW()");
        $stmt->execute(['email' => $request->email]);
        $otp = $stmt->fetch(\PDO::FETCH_OBJ);

        if ($otp && $otp->email == $request->email) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.whacenter.com/api/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'device_id' => $_ENV['WHACENTER_DEVICE_ID'],
                    'number' => $otp->mobile,
                    'message' => 'Your Elite-Class OTP is: ' . $otp->two_factor_code
                ]
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        }

        return response()->json([
            'message' => 'You shouldn\'t be here',
        ]);
    }
}
