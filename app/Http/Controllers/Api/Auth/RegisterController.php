<?php

namespace App\Http\Controllers\Api\Auth;

use Module;
use App\User;
use App\Wallet;
use App\Setting;
use App\Affiliate;
use App\Mail\verifyEmail;
use App\Mail\WelcomeUser;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use App\Mail\EmailVerification;
use Illuminate\Validation\Rule;
use App\Mail\EmailVerficationOTP;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Notification;
use App\Notifications\userEarnedRefralNotification;
use App\Http\Controllers\Api\VerificationController;

class RegisterController extends Controller {

    use IssueTokenTrait;

    public function register(Request $request) {
        $rules = [
            'fname' => 'required|min:3|max:20',
            'lname' => 'required|min:3|max:20',
            'institute' => 'required',
            'major' => 'required',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email',
            'password' => [
                            'required',
                            'max:50',
                            Password::min(8)
                            ->mixedCase()
                            ->numbers()
            ]
        ];
        $messages = [
            'fname.required' => __('First Name is required'),
            'fname.min' => __('First Name minimum length must be 3 characters'),
            'fname.max' => __('First Name maximum length is 20 characters'),
            'lname.required' => __('Last Name is required'),
            'lname.min' => __('Last Name minimum length must be 3 characters'),
            'lname.max' => __('Last Name maximum length is 20 characters'),
            'institute.required' => __("Institute is required"),
            'major.required' => __("Major is required"),
            'email.required' => __("Email is required"),
            'email.email' => __("Email is Invalid"),
            'email.max' => __('Email maximum length is 40'),
            'email.unique' => __('Email already taken'),
            'password.required' => __('Password is Required'),
            'password.min' => __('Password minimum length must 8 digits'),
            'password.max' => __('Password maximum length must 50 digits'),
            'password.mixedCase' => __('Password must contain at least one uppercase and one lowercase letter'),
            'password.numbers' => __('Password must contain at least one number'),
            'mobile.required' => __('Mobile Number is Required'),
            'mobile.unique' => __('Mobile Number is already taken'),
            'mobile.min' => __('Mobile Number minimum length must 6 digits'),
            'mobile.max' => __('Mobile Number maximum length must 15 digits'),
        ];

        $config = Setting::first();

        if ($config->mobile_enable == 1) {
            // $request->merge(['mobile' => Str::remove('+', trim(request('mobile')))]);
            $rules['mobile'] = ['required', 'min:10', 'max:15', Rule::unique('users', 'mobile')];
        }

        $request->validate($rules, $messages);

        if ($config->verify_enable == 0) {
            $verified = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $verified = NULL;
        }

        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')) {
            $affiliate = Affiliate::first();
        } else {
            $affiliate = NULL;
        }


        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')) {
            if (isset($affiliate) && $affiliate->status == 1) {
                $refercode = User::createReferCode();
                if (request('referral') !== null) {
                    $referred_by = request('referral');
                } else {
                    $referred_by = NULL;
                }
            } else {
                $refercode = NULL;
                $referred_by = NULL;
            }
        } else {
            $refercode = NULL;
            $referred_by = NULL;
        }

        $data = [
            'fname' => request('fname'),
            'lname' => request('lname'),
            'email' => request('email'),
            'institute' => request('institute'),
            'major' => request('major'),
            'email_verified_at' => $verified,
            'country_code' => request('country_code'),
            'timezone' => request('timezone'),
            'mobile' => $config->mobile_enable == 1 ? trim(request('mobile')) : NULL,
            'password' => bcrypt(request('password')),
        ];

        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')) {
            $data['referred_by'] = $referred_by;
            $data['affiliate_id'] = $refercode;
        }

        $user = User::create($data);

        $user->assignRole('User');
        // if ($config->w_email_enable == 1) {
        //     try {
        //         Mail::to(request('email'))->send(new WelcomeUser($user));
        //     } catch (\Exception $e) {
        //         return response()->json('Registration done. Mail cannot be sent', 201);
        //     }
        // }

        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')) {
            if (isset($affiliate) && $affiliate->status == 1) {
                if (isset($user->referred_by)) {
                    $affiliate_user = User::where('affiliate_id', $user->referred_by)->first();
                } else {
                    $affiliate_user = null;
                }

                if (isset($affiliate_user) && $affiliate_user == !NULL) {
                    $user_wallet = Wallet::where('user_id', $affiliate_user->id)->first();

                    if (isset($user_wallet)) {

                        Wallet::where('user_id', $affiliate_user->id)
                                ->update(['balance' => $user_wallet->balance + $affiliate->point_per_referral]);
                    } else {


                        Wallet::create([
                            'user_id' => $affiliate_user->id,
                            'balance' => $affiliate->point_per_referral,
                        ]);
                    }

                    $currency = \App\Currency::where('default', '=', '1')->first();
                    /** Create wallet transcation history */
                    $wallet_transaction = \App\WalletTransactions::create([
                                'wallet_id' => $affiliate_user->wallet->id,
                                'user_id' => $affiliate_user->id,
                                'transaction_id' => '',
                                'payment_method' => '',
                                'total_amount' => $affiliate->point_per_referral,
                                'currency' => $currency->code,
                                'currency_icon' => $currency->symbol,
                                'type' => 'Credit',
                                'detail' => __('Referral Credit'),
                                    ]
                    );
                    $noti = [
                        'id' => $user->id,
                    ];

                    if(env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1'){

                        if(count($affiliate_user->device_tokens) > 0 && $affiliate_user->notifications){
                            Notification::send($affiliate_user, new userEarnedRefralNotification($noti));
                        }
                    }
                }
            }
        }

        if (Cookie::get('referral') !== null) {
            Cookie::queue(Cookie::forget('referral'));
        }



        if (isset($config->activity_enable)) {
            if ($config->activity_enable == '1') {
                $project = new User();

                activity()
                        ->useLog('Register')
                        ->performedOn($project)
                        ->causedBy($user->id)
                        ->withProperties(['customProperty' => 'Register'])
                        ->log('User Register')
                        ->subject('Register');
            }
        }


        if ($config->w_email_enable == 1) {
            try {

                Mail::to($data['email'])->send(new WelcomeUser($user));
            } catch (\Swift_TransportException $e) {
                return response()->json(array("errors" => ["message" => [__('Sorry for inconvenient, We are having trouble on sending emails')]]), 400);

            }
        }

        if ($config->verify_enable == 0) {
            $res = $this->issueToken($request, 'password');

            if($request->fpjsid){ 
                $oauth = Token::where(['user_id' => $user->id, 'revoked' => 0])->orderBy('created_at', 'DESC')->first();
        
                $oauth->update(['fpjsid' => $request->fpjsid?? $oauth->fpjsid]);
        
                $token = Token::where(['revoked'=> 0,'user_id'=>$user->id])->whereDate('expires_at', '>=', now())->groupBy('user_id') ->havingRaw('count(DISTINCT fpjsid) > ?', [1])->first();
                
                if($token && $token->id){
                    $user->update(['is_locked' => 1]);
                    Token::where('user_id', $user->id)->update(['revoked' => 1]);
                    
                    return response()->json(array("errors" => ["message" => [__('user_block_multi_device_login')]]), 406);
                }
            }
            return $res;
             
        } else {
            if ($verified != NULL) {
                $res = $this->issueToken($request, 'password');

                if($request->fpjsid){ 
                    $oauth = Token::where(['user_id' => $user->id, 'revoked' => 0])->orderBy('created_at', 'DESC')->first();
            
                    $oauth->update(['fpjsid' => $request->fpjsid?? $oauth->fpjsid]);
            
                    $token = Token::where(['revoked'=> 0,'user_id'=>$user->id])->whereDate('expires_at', '>=', now())->groupBy('user_id') ->havingRaw('count(DISTINCT fpjsid) > ?', [1])->first();
                    
                    if($token && $token->id){
                        $user->update(['is_locked' => 1]);
                        Token::where('user_id', $user->id)->update(['revoked' => 1]);
                        
                        return response()->json(array("errors" => ["message" => [__('user_block_multi_device_login')]]), 406);
                    }
                }
                
                return $res;
                
            } else {

                try {

                    $token = Str::random(64);
                    $config = Setting::findOrFail(1);
                    $user->token = $token;
                    $user->save();
                    $data = ['token' => $token, 'logo' => $config->logo, 'company' => $config->project_title,'from'=>$config->wel_email];
                    Mail::to($user->email)->send(new EmailVerification($user,$data));

                    return response()->json(array("errors" => ["message" => [__('Verify your email')]]), 403);
                } catch (\Swift_TransportException $e) {
                    return response()->json(array("errors" => ["message" => [__('Sorry for inconvenient, We are having trouble on sending emails')]]), 400);
                }


                // try {
                //     // $user->sendEmailVerificationNotificationViaAPI();

                //     $code = random_int(1000, 9999);
                //     $config = Setting::findOrFail(1);
                //     $user->code = $code;
                //     $user->save();
                //     $data = ['code' => $code, 'logo' => $config->logo, 'company' => $config->project_title,'from'=>$config->wel_email];
                //     Mail::to($user->email)->send(new EmailVerficationOTP($user,$data));

                //     //  Mail::to(request('email'))->send(new WelcomeUser($user));
                //     return response()->json(array("errors" => ["message" => [__('Verify your email')]]), 403);
                // } catch (\Swift_TransportException $e) {
                //     return response()->json(array("errors" => ["message" => [__('Mail Sending Error')]]), 400);
                // }
            }
        }
    }

    public function verifyemail($token)
    {
        $user = User::where('token', $token)->first();
        $message = 'Sorry your Email cannot be identified.';

        if ($user) {
            if(!$user->email_verified_at) {
                $user->email_verified_at = now()->toDateTimeString();
                $user->status = 1;
                // $user->token = NULL;
                $user->save();
                $message = "Your E-mail has been successfully verified. You can now login to EliteClass App.";
            } else {
                $message = "Your E-mail is already verified. You can now login to EliteClass App.";
            }
        }
  
    //   return view('email.verify_modal', compact('message'));
      return $message;
    }

    public function verifyotp(Request $request) {

        $this->validate($request, [
            'code' => ['required', Rule::exists('users', 'code')->where('email', $request->email)],
            'email' => 'required|exists:users,email',
                ], [
            'code.required' => __("Verification Code is required"),
            'code.exists' => __("Verification Code is invalid"),
            'email.required' => __("Email is required"),
            'email.exists' => __("Email not exists")
        ]);
        $user = User::where(['email' => $request->email, 'code' => $request->code])->first();
        if ($user) {
            $verified = \Carbon\Carbon::now()->toDateTimeString();
            $user->email_verified_at = $verified;
            $user->status = 1;
            $user->code = NULL;
            $user->save();
            
            return $data = [
                "token_type" => "Bearer",
                "expires_in" => 863999,
                "access_token" => $token = $user->createToken('GenerateToken', ['*'])->accessToken,
                "refresh_token" => $token
            ];
        //    return $this->issueToken($request, 'password');
        } else {

            return response()->json(array("errors" => ["message" => [__("User not found")]]), 401);
        }
    }

    public function resendotp(Request $request) {
        $this->validate($request, [
            'email' => 'required|exists:users,email',
                ], [
            'email.required' => __("Email is required"),
            'email.exists' => __("Email not exists")
        ]);
        $user = User::where(['email' => $request->email])->first();
        if ($user) {
            $code = random_int(1000, 9999);
            $config = Setting::findOrFail(1);
            $user->code = $code;
            $user->save();
            $data = ['code' => $code, 'logo' => $config->logo, 'company' => $config->project_title,'from'=>$config->wel_email];
            Mail::to($user->email)->send(new EmailVerficationOTP($user,$data));
            return response()->json(__("code sent to your email"), 200);
        } else {

            return response()->json(array("errors" => ["message" => [__('User not found')]]), 401);
        }
    }

}
