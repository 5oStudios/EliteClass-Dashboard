<?php

use Carbon\Carbon;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

/**
 * Save Fpjsid into oauth_access_token table
 */

if (! function_exists('saveFpjsid')) {
    function saveFpjsid($authUser, $fpjsid)
    {
        $oauth = Token::where(['user_id' => $authUser->id, 'revoked' => 0])->orderBy('created_at', 'DESC')->first();
            
        $oauth->update(['fpjsid' => $fpjsid]);

        $token = Token::where(['revoked'=> 0,'user_id'=>$authUser->id])->whereDate('expires_at', '>=', now())->groupBy('user_id') ->havingRaw('count(DISTINCT fpjsid) > ?', [1])->first();
        
        if($token && $token->id){
            $authUser->update(['is_locked' => 1]);
            Token::where('user_id', $authUser->id)->update(['revoked' => 1]);
            
            return response()->json(array("errors" => ["message" => [__('user_block_multi_device_login')]]), 406);
        }
    }
}

if (! function_exists('getUserTimeZoneDateTime')) {
    function getUserTimeZoneDateTime($datetime)
    {
        $timezone = Auth::guard('web')->check() ? auth()->user()->timezone : 'Asia/Kuwait';

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $datetime, 'UTC');
        return $date->setTimezone($timezone);
    }
}


if (! function_exists('encryptData')) {
    function encryptData($data, $key='secretKey456$')
    {
        $encrypted = Crypt::encryptString($data, $key);
        return $encrypted;
    }
}


if (! function_exists('decryptData')) {
    function decryptData($data, $key='secretKey456$')
    {
        $decrypted = Crypt::decryptString($data, $key);
        return $decrypted;
    }
}


