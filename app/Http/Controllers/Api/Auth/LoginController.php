<?php

namespace App\Http\Controllers\Api\Auth;

use Hash;
use App\User;
use Validator;
use App\Setting;
use App\Mail\WelcomeUser;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Mail\EmailForgotPassOTP;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller {

    use IssueTokenTrait;

    public function login(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ],[
            'email.required' => __("Email is required"),
            'password.required' => __("Password is required"),
        ]);

        
        // $authUser = User::whereRaw("((`email` = '$request->email' and `role` = 'user') or ( `mobile` = '" . trim($request->email) . "' and `role` = 'user'))")->first();
        $authUser = User::where('email',trim($request->email))->where('role', 'user')->orWhere( function($query) use($request) {
            $query->where('mobile', trim($request->email))->where('role', 'user');
        })->first();
        // $authUser = User::whereRaw("((`email` = '$request->email' and `role` = 'user') or ( `mobile` = '" . Str::remove('+', trim($request->email)) . "' and `role` = 'user'))")->first();
        
        if (isset($authUser) && $authUser->status == 0) {
            return response()->json(array("errors" => ["message" => [__('Blocked User')]]), 406);
        } else {

            $res = null;
            $result = null;
            $setting = Setting::first();

            if (isset($authUser)) {
                $request->email = $authUser->email;
                
                if ($setting->verify_enable == 0) {
                    if (isset($request->role)) {
                        if ($authUser->role == 'instructor') {
                            $res = $this->issueToken($request, 'password');
                        } else {
                            return response()->json(array("errors" => ["message" => [__('Invalid Login')]]), 406);
                        }
                    } else {
                        $res = $this->issueToken($request, 'password');
                    }
                
                } 
                
                else {
                    if ($authUser->email_verified_at != NULL) {
                        if (isset($request->role)) {
                            if ($authUser->role == 'instructor') {
                                $res = $this->issueToken($request, 'password');
                            } else {
                                return response()->json(array("errors" => ["message" => [__('Invalid Login')]]), 406);
                            }
                        } else {
                            $res = $this->issueToken($request, 'password');
                        }
                    } else {
                        return response()->json(array("errors" => ["message" => [__("Verify your email")]]), 403);
                    }
                }

            } else {

                return response()->json(array("errors" => ["message" => [__("user not found")]]), 406);
            }

            if ($res->status() == 400) {
                return response()->json(array("errors" => ["message" => [__("Invalid Login")]]), 400);
            } else {

                if(isset($authUser) && $authUser->is_locked == 1){
                    return response()->json(array("errors" => ["message" => [__('user_block_multi_device_login')]]), 406);
                }
                
                if($request->fpjsid && $authUser->test_user == '0' && $authUser->is_allow_multiple_device == '0') { 
                    $oauth = Token::where(['user_id' => $authUser->id, 'revoked' => 0])->orderBy('created_at', 'DESC')->first();
                    
                    $oauth->update(['fpjsid' => $request->fpjsid?? $oauth->fpjsid]);
                    
                    $token = Token::where(['revoked'=> 0,'user_id'=>$authUser->id])->whereDate('expires_at', '>=', now())->groupBy('user_id') ->havingRaw('count(DISTINCT fpjsid) > ?', [1])->first();
                    
                    if($token && $token->id){

                        $authUser->update(['is_locked' => 1]);
                        $authUser->increment('blocked_count');

                        Token::where('user_id', $authUser->id)->update(['revoked' => 1]);
                        
                        return response()->json(array("errors" => ["message" => [__('user_block_multi_device_login')]]), 406);
                    }
                }
                
                if($request->timezone){
                    $authUser->update(['timezone' => $request->timezone]);
                }
                
                return $res;
            }
        }
    }

    public function fblogin(Request $request) {

        $this->validate($request, [
            'email' => 'required',
            'name' => 'required',
            'code' => 'required',
            'password' => ''
        ]);
        $authUser = User::where('email', $request->email)->first();
        if ($authUser) {
            $authUser->facebook_id = $request->code;
            $authUser->fname = $request->name;
            $authUser->save();
            if (isset($authUser) && $authUser->status == '0') {
                return response()->json(array("message" => 'Blocked User'), 401);
            } else {
                if (Hash::check('password', $authUser->password)) {

                    return $response = $this->issueToken($request, 'password');
                } else {
                    $response = ["message" => "Password mismatch"];
                    return response($response, 422);
                }
            }
        } else {

            $verified = \Carbon\Carbon::now()->toDateTimeString();

            $user = User::create([
                        'fname' => request('name'),
                        'email' => request('email'),
                        'password' => Hash::make($request->password != '' ? $request->password : 'password'),
                        'facebook_id' => request('code'),
                        'status' => '1',
                        'email_verified_at' => $verified
            ]);

            return $this->issueToken($request, 'password');
        }
    }

    public function googlelogin(Request $request) {


        $this->validate($request, [
            'email' => 'required',
            'name' => 'required',
            'uid' => 'required',
            'password' => ''
        ]);

        $authUser = User::where('email', $request->email)->first();

        if ($authUser) {

            $authUser->google_id = $request->uid;
            $authUser->fname = $request->name;
            $authUser->save();

            if (isset($authUser) && $authUser->status == '0') {
                return response()->json(array("message" => 'Blocked User'), 401);
            } else {

                if (Hash::check('password', $authUser->password)) {

                    return $response = $this->issueToken($request, 'password');
                } else {
                    $response = ["message" => "Password mismatch"];
                    return response($response, 422);
                }
            }
        } else {


            $verified = \Carbon\Carbon::now()->toDateTimeString();

            $user = User::create([
                        'fname' => request('name'),
                        'email' => request('email'),
                        'password' => Hash::make($request->password != '' ? $request->password : 'password'),
                        'google_id' => request('uid'),
                        'status' => '1',
                        'email_verified_at' => $verified
            ]);

            return $response = $this->issueToken($request, 'password');
        }
    }

    public function refresh(Request $request) {
        $this->validate($request, [
            'refresh_token' => 'required'
                ], [
            'refresh_token.required' => __('Refresh Token Is Required')
        ]);

        return $this->issueToken($request, 'refresh_token');
    }

    public function forgotApi(Request $request) {
        $this->validate($request, [
            'email' => 'required|exists:users,email'
                ], [
            'email.required' => __("Email is required"),
            'email.exists' => __("Email not exists")
        ]);
        $user = User::whereEmail($request->email)->first();
        if ($user) {

            $code = random_int(1000, 9999);
            // $uni_col = User::pluck('code');
            // do {
            // } while (in_array($code, $uni_col));
            try {
                $config = Setting::findOrFail(1);
                $user->code = $code;
                $user->save();
                $Maildata = ['code' => $code, 'logo' => $config->logo, 'company' => $config->project_title,'from'=>$config->wel_email];
                Mail::to($user->email)->send(new EmailForgotPassOTP($user,$Maildata));
                $data = [
                    'user_id' => $user->id,
                    'type' => 'password',
                    'code' => $code,
                    'expire_time' => config('app.otp_expire')
                ];

                return response()->json(array('data' => $data), 200);
            } catch (\Swift_TransportException $e) {
                info('FORGOT API EXCEPTION');
                info($e);
                return response()->json(array("errors" => ["message" => [__('Sorry for inconvenient, We are having trouble on sending emails')]]), 400);
            }
        } else {
            return response()->json(array("errors" => ["message" => [__('user not found')]]), 401);
        }
    }

    public function verifyApi(Request $request) {
        $this->validate($request, [
            'code' => ['required', Rule::exists('users', 'code')->where('email', $request->email)]
                ], [
            'code.required' => __("Verification Code is required"),
            'code.exists' => __("Verification Code is invalid")
        ]);

        $user = User::whereEmail($request->email)->whereCode($request->code)->first();
        $startTime = Carbon::parse($user->updated_at);
        $finishTime = Carbon::parse(now());
        $totalDuration = $finishTime->diffInSeconds($startTime);
        if ($totalDuration > config('app.otp_expire')) {
            return response()->json(array("errors" => ["message" => [__('OTP Expired')]]), 422);
        } else {
            // $user->code = null;
            // $user->save();
            return response()->json(__('OTP verified'), 200);
        }
    }

    public function resetApi(Request $request) {

        $this->validate($request, [
            'code' => ['required', Rule::exists('users', 'code')->where('email', $request->email)],
            'password' => [
                            'required',
                            'max:50',
                            Password::min(8)
                            ->mixedCase()
                            ->numbers()
            ],
            'email' => 'required|exists:users,email',
                ], [
            'code.required' => __("Verification Code is required"),
            'code.exists' => __("Verification Code is invalid"),
            'password.required' => __('Password is Required'),
            'password.min' => __('Password minimum length must 8 digits'),
            'password.max' => __('Password maximum length must 50 digits'),
            'password.mixedCase' => __('Password must contain at least one uppercase and one lowercase letter'),
            'password.numbers' => __('Password must contain at least one number'),
            'email.required' => __("Email is required"),
            'email.exists' => __("Email not exists")
        ]);

        $user = User::whereEmail($request->email)->first();
        $startTime = Carbon::parse($user->updated_at);
        $finishTime = Carbon::parse(now());
        $totalDuration = $finishTime->diffInSeconds($startTime);
        if ($totalDuration > config('app.otp_expire')) {
            return response()->json(array("errors" => ["message" => [__('OTP Expired')]]), 422);
        }

        $user->code = null;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(__('Password reset successfully'), 200);
    }

    public function logoutApi() {

        $token = Auth::user()->token();
        $token->revoke();
        $response = __('You have been successfully logged out');
        return response($response, 200);
    }

    public function redirectToblizzard_sociallogin($provider) {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function blizzard_sociallogin(Request $request, $provider) {

        if (!$request->has('code') || $request->has('denied')) {
            return response()->json('Code not found !', 401);
        }


        try {

            return Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {

            return response()->json($e->getMessage(), 401);
        }

        $authUser = $this->findOrCreateUser($user, $provider);

        //check status and block condition and return response
        // return msg your a/c is not active.

        if (isset($authUser) && $authUser->status == '0') {
            return response()->json(array("errors" => ["msg" => ["Blocked User"]]), 401);
        } else {

            $token = $authUser
                            ->createToken(config('app.name') . ' Password Grant Client')
                    ->accessToken;

            return response()->json(['accessToken' => $token], 200);
        }

        // return $token
    }

    public function findOrCreateUser($user, $provider) {
        if ($user->email == Null) {
            $user->email = $user->id . '@facebook.com';
        }

        $authUser = User::where('email', $user->email)->first();
        $providerField = "{$provider}_id";

        if ($authUser) {
            if ($authUser->{$providerField} == $user->id) {
                $authUser->email_verified_at = \Carbon\Carbon::now()->toDateTimeString();
                $authUser->save();
                return $authUser;
            } else {
                $authUser->{$providerField} = $user->id;
                $authUser->email_verified_at = \Carbon\Carbon::now()->toDateTimeString();
                $authUser->save();
                return $authUser;
            }
        }

        if ($user->avatar != NULL && $user->avatar != "") {
            $fileContents = @file_get_contents($user->getAvatar());
            $user_profile = File::put(public_path() . '/images/user_img/' . $user->getId() . ".jpg", $fileContents);
            $name = $user->getId() . ".jpg";
        } else {
            $name = NULL;
        }

        $verified = \Carbon\Carbon::now()->toDateTimeString();

        $setting = Setting::first();

        $auth_user = User::create([
                    'fname' => $user->name,
                    'email' => $user->email,
                    'user_img' => $name,
                    'email_verified_at' => $verified,
                    'password' => Hash::make('password'),
                    $providerField => $user->id,
        ]);

        if ($setting->w_email_enable == 1) {
            try {

                Mail::to($auth_user['email'])->send(new WelcomeUser($auth_user));
            } catch (\Swift_TransportException $e) {
                return response()->json(array("errors" => ["message" => [__('Sorry for inconvenient, We are having trouble on sending emails')]]), 400);
            }
        }

        return $auth_user;
    }

}
