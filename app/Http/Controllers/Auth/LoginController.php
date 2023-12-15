<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SendTwoFactorCode;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function authenticated(Request $request)
    {
        // $gsetting = Setting::first();

        // if (Auth::user()->role == "instructor" || Auth::user()->role == "user") {
        //     if (isset($gsetting->activity_enable)) {
        //         if ($gsetting->activity_enable == '1') {
        //             $project = new user();

        //             activity()
        //                ->useLog('Login')
        //                ->performedOn($project)
        //                ->causedBy(auth()->user())
        //                ->withProperties(['customProperty' => 'Login'])
        //                ->log('Logged In')
        //                ->subject('Login');
        //         }
        //     }
        // }

        if (!auth()->user()->hasRole('user-unblock-admin')) {
            $request->user()->generateTwoFactorCode();
            $request->user()->notify(new SendTwoFactorCode());
        }

        if (Auth::user()->status == 1) {
            if (Auth::user()->role == "admin") {
                return redirect()->route('admin.index');
            } elseif (Auth::user()->role == "instructor") {
                return redirect()->route('instructor.index');
            } elseif (auth()->user()->role != "user") {
                return redirect()->to('/');
            } else {
                // return redirect('/home');
                Auth::logout();
                return redirect()->route('login')->with('delete', 'User cannot login !');
            }
        } else {
            Auth::logout();
            return redirect()->route('login')->with('delete', 'You are deactivated !');
        }
    }

    public function socialLogin($social)
    {
        return Socialite::driver($social)->redirect();
    }

    public function handleProviderCallback($social)
    {
        $userSocial = Socialite::driver($social)->user();
        $user = User::where(['email' => $userSocial->getEmail()])->first();

        // set the remember me cookie if the user check the box
        $remember = request()->has('remember') ? true : false;

        // attempt to do the login
        if (
            Auth::attempt(['email' => request()->get('email') , 'password' => request()->get('password') ,
            'status' => 1], $remember)
        ) {
                return redirect()->intended('/home');
        } else {
            $errors = new MessageBag(['email' => ['Email or password is invalid.']]);
            return Redirect::back()->withErrors($errors)->withInput(request()->except('password'));
        }

        if ($user) {
            Auth::login($user);
            return redirect()-> action('HomeController@index');
        } else {
            return view('auth.register', ['name' => $userSocial->getName(),
                                            'email' => $userSocial->getEmail()]);
        }
    }
}
