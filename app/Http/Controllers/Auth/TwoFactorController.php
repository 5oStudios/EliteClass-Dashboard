<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Notifications\SendTwoFactorCode;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        if (!$user->two_factor_code && !$user->two_factor_expires_at) {
            return redirect()->to('/');
        }
        
        return view('auth.twofactor');
    }

    public function store(Request $request): ValidationException|RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['integer', 'required'],
        ]);

        $user = auth()->user();

        if ($request->input('two_factor_code') !== $user->two_factor_code) {
            throw ValidationException::withMessages([
                'two_factor_code' => __('The two factor code you have entered does not match'),
            ]);
        }

        $user->resetTwoFactorCode();

        return redirect()->to('/');
    }

    public function resend(): RedirectResponse
    {
        $user = auth()->user();

        $expire = new Carbon($user->two_factor_expires_at);
        if ($expire->diffInSeconds(now()) > 600) {
            $user->generateTwoFactorCode();
        }

        $user->notify(new SendTwoFactorCode());

        return redirect()->back()->withStatus(__('The two factor code has been sent again'));
    }
}
