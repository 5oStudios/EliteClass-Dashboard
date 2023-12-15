<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use App\Order;
use App\Setting;
use App\CourseClass;
use App\CourseProgress;
use App\CoursesInBundle;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class UserApiController extends Controller {

    public function updateUserCategories(Request $request) {
        $this->validate($request, [
            'main_category' => 'required|exists:categories,id',
            'scnd_category_id' => 'required|exists:secondary_categories,id',
            'sub_category' => 'required|exists:sub_categories,id',
            'ch_sub_category' => 'required|exists:child_categories,id'
                ], [
            'main_category.required' => __("country not selected"),
            'main_category.exists' => __("country not exists"),
            'scnd_category_id.required' => __("type of institute not selected"),
            'scnd_category_id.exists' => __("type of institute not exists"),
            'sub_category.required' => __("Institute not selected"),
            'sub_category.exists' => __("Institute not exist"),
            'ch_sub_category.required' => __("major not selected"),
            'ch_sub_category.required' => __("major not exist"),
        ]);

        $user = Auth::user();
        User::where('id', $user->id)
                ->update([
                    'main_category' => $request->main_category,
                    'scnd_category_id' => $request->scnd_category_id,
                    'sub_category' => $request->sub_category,
                    'ch_sub_category' => $request->ch_sub_category
        ]);

        return response()->json(__("Categories saved Successfull"), 200);
    }

    public function ChangePass(Request $request) {

        $this->validate($request, [
            'old_password' => 'required|password',
            'password' => [
                'required',
                'max:50',
                        Password::min(8)
                        ->mixedCase()
                        ->numbers(),
                'different:old_password'
            ],
                ], [
            'old_password.required' => __("Old Password is required"),
            'old_password.password' => __('old password is not correct'),
            'password.required' => __('Password is Required'),
            'password.min' => __('Password minimum length must 8 digits'),
            'password.max' => __('Password maximum length must 50 digits'),
            'password.mixedCase' => __('Password must contain at least one uppercase and one lowercase letter'),
            'password.numbers' => __('Password must contain at least one number'),
        ]);

        $user = Auth::user();

        if ($user) {

            $user->update(['password' => bcrypt($request->password)]);

            $user->save();
            $token = $user->token();
            $token->revoke();

            return response()->json('Password changed', 200);
        } else {
            return response()->json(array("errors" => ["message" => [__("user not found")]]), 401);
        }
    }


    public function getUserSelectedCategories(Request $request) {
        $user = Auth::user();
        $responseData = [
            'initial_country' => [
                'name' => $user->country->title??null,
                'id' => $user->main_category
            ],
            'initial_type' => [
                'name' => $user->type->title??null,
                'id' => $user->scnd_category_id
            ],
            'initial_stage' => [
                'name' => $user->stage->title??null,
                'id' => $user->sub_category
            ],
            'initial_major' => [
                'name' => $user->majorr->title??null,
                'id' => $user->ch_sub_category
            ]

        ];

        return response()->json($responseData, 200);
    }

    public function userprofile(Request $request) {

        $user = Auth::guard('api')->user();

        $setting = Setting::first();
        
        $hours = 0;
        $courses = CourseProgress::where('user_id', '=', $user->id)->activeProgress()->get();
        foreach ($courses as $c) {
            $hours += CourseClass::whereIn('id', $c->mark_chapter_id)->sum('duration');
        }
        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings') && (!$user->affiliate_id)) {
            $refercode = User::createReferCode();
            $user->update(['affiliate_id' => $refercode]);
        }
        $data = [
            'id' => $user->id,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'image' => $user->user_img ? url('images/user_img/' . $user->user_img) : null,
            'email' => $user->email,
            'institute' => $user->institute,
            'major' => $user->major,
            'main_category' => $user->main_category,
            'scnd_category_id' => $user->scnd_category_id,
            'sub_category' => $user->sub_category,
            'ch_sub_category' => $user->ch_sub_category,
            'referral_link' => '?referral=' . $user->affiliate_id,
            'notifications' => auth()->User()->unreadNotifications()->count(),
            'notifications_on' => $user->notifications ? true : false,
            'mobile' => $user->mobile,
            'dob' => $user->dob,
            'courses' => $courses->count()??0,
            'hours' => round($hours / 60, 1),
            'code' => $user->token()->id,
            'customer_support' => $setting->default_phone,
            'is_testUser' => $user->test_user == 1 ? true : false
        ];

        return response()->json($data, 200);
    }

    public function updateprofile(Request $request)
    {
        $auth = Auth::guard('api')->user();

        $this->validate($request, [
            'fname' => 'nullable|min:3|max:20',
            'lname' => 'nullable|min:3|max:20',
            'institute' => 'nullable|exists:sub_categories,slug',
            'major' => 'nullable|exists:child_categories,slug',
            'email' => 'nullable|email:rfc,dns|max:40|unique:users,email,' . $auth->id,
            'mobile' => 'nullable|min:10|max:15|unique:users,mobile,' . $auth->id,
            'user_img' => 'nullable|mimes:jpg,jpeg,png|max:20480',
        ], [
            // 'fname.required' => __('First Name is required'),
            'fname.min' => __('First Name minimum length must be 3 characters'),
            'fname.max' => __('First Name maximum length is 20 characters'),
            // 'lname.required' => __('Last Name is required'),
            'lname.min' => __('Last Name minimum length must be 3 characters'),
            'lname.max' => __('Last Name maximum length is 20 characters'),
            // 'institute.required' => __("Institute is required"),
            'institute.exists' => __("The selected majot does not exists"),
            // 'major.required' => __("Major is required"),
            'major.exists' => __("The selected majot does not exists"),
            // 'email.required' => __("Email is required"),
            'email.email' => __("Email is Invalid"),
            'email.max' => __('Email maximum length is 40'),
            'email.unique' => __('Email already taken'),
            // 'mobile.required' => __('Mobile Number is Required'),
            'mobile.unique' => __('Mobile Number is already taken'),
            'mobile.min' => __('Mobile Number minimum length must 6 digits'),
            'mobile.max' => __('Mobile Number maximum length must 15 digits'),
            'user_img.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'user_img.max' => __('Image size should not be greater 20 MB'),
        ]);

        if ($file = $request->file('user_img')) {
            if ($auth->user_img != null) {
                $image_file = @file_get_contents(public_path() . '/images/user_img/' . $auth->user_img);
                if ($image_file) {
                    unlink(public_path() . '/images/user_img/' . $auth->user_img);
                }
            }
            $name = time() . $file->getClientOriginalName();
            $file->move('images/user_img', $name);
        }

        $data = [
            'fname' => $request->fname ?? $auth->fname,
            'lname' => $request->lname ?? $auth->lname,
            'email' => $request->email ?? $auth->email,
            'country_code' => $request->country_code ?? $auth->country_code,
            'mobile' => $request->mobile ?? $auth->mobile,
            'institute' => $request->institute ?? $auth->institute,
            'major' => $request->major ?? $auth->major,
            'dob' => $request->dob ?? $auth->dob,
            'user_img' => $request->file('user_img') ? $name : $auth->user_img,
            'address' => $request->address ?? $auth->address,
            'detail' => $request->detail ?? $auth->detail,
        ];

        $config = \App\Setting::first();
        if ($request->email && ($request->email != $auth->email) && $config->verify_enable == 0) {
            $verified = \Carbon\Carbon::now()->toDateTimeString();
            $data['email_verified_at'] = $verified;
        } elseif ($request->email && ($request->email != $auth->email)) {
            $verified = NULL;
            $data['email_verified_at'] = $verified;
        }

        $auth->update($data);

        if ($file = $request->file('user_img')) {
            return response()->json(__('Profile Photo Added Successfully'), 200);
        } else {
            if ($request->email && $auth->email_verified_at == null && $config->verify_enable) {
                $token = Auth::user()->token();
                $token->revoke();
                return response()->json(array("errors" => ["msg" => [__('Verify your email')]]), 402);
            }
            return response()->json(__("Profile Updated Added Successfully"), 200);
        }
    }
    

    public function mycalendar(Request $request) {

        $request->validate([
            'from' => 'required|date|date_format:Y-m-d',
            'to' => 'required|date|date_format:Y-m-d|after:from'
        ]);

        $data = [];

        $orders = SessionEnrollment::where('user_id', Auth::id())
                        ->where( function($query) use($request) {
                            $query->whereHas('meeting', function($query) use($request) {
                                $query->whereBetween('start_time', [$request->from, $request->to])
                                    ->where('expire_date', '>=', date('Y-m-d'));
                            })
                            ->orWhereHas('offlinesession', function($query) use($request) {
                                $query->whereBetween('start_time', [$request->from, $request->to])
                                        ->where('expire_date', '>=', date('Y-m-d'));
                            });
                        })
                        ->active()
                        ->with('meeting')
                        ->with('offlinesession')
                        // ->orderBy('enroll_start', 'ASC')
                        ->get();

        foreach ($orders as $o) {
            $data[] = [

                'type' => $o->meeting_id ? 'live-streaming' : ($o->offline_session_id ? 'in-person-session' : ''),
                'id' => $o->meeting_id ?? $o->offline_session_id,
                'title' => $o->meeting_id ? $o->meeting->_title() : ($o->offline_session_id ? $o->offlinesession->_title() : ''),
                'session_time' => $o->meeting_id ? $o->meeting->_enrollstart() : ($o->offline_session_id ? $o->offlinesession->_enrollstart() : ''),
                'date' => $o->meeting_id ? date('Y-m-d', strtotime($o->meeting->_enrollstart())) : ($o->offline_session_id ? date('Y-m-d', strtotime($o->offlinesession->_enrollstart())) : ''),
            ];
        }

        return collect($data)->groupBy('date');
    }


    public function storePlayerDeviceId(Request $request){

        if($request->player_device_id){
            $oauth = Token::where(['id' => auth()->user()->token()->id, 'revoked' => 0])->first();
    
            $oauth->update([
                'player_device_id' => $request->player_device_id,
                'updated_at' => now(),
            ]);
    
            return response()->json(__("Player Device ID Added Successfully"), 200);
        }
    }

    public function DeleteUser(Request $r){
        $user = Auth::user();

        if ($user) {

            $user->update(['deleted_by'=>$user->id,'status'=>0,'email'=>'delete.'.time().'.'.$user->email,'password' => bcrypt('user@delete_by_user')]);

            $user->save();
            $token = $user->token();
            $token->revoke();

            return response()->json(__('your account has been deleted'), 200);
        } else {
            return response()->json(array("errors" => ["message" => [__("user not found")]]), 401);
        }
    }
    
}
