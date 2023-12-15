<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware {

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($request, Closure $next, ...$guards) {
        $this->authenticate($request, $guards);
        return $next($request);
    }

    protected function authenticate($request, array $guards) {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                // if ($this->auth->guard($guard)->user()->status) {
                if ($this->auth->guard($guard)->user()->status && $this->auth->guard($guard)->user()->is_locked == 0) {
                    return $this->auth->shouldUse($guard);
                } else {
                    if ($request->expectsJson()) {
                        $token = $this->auth->guard($guard)->user()->token();
                        $token->revoke();
                    } else {
                        Auth::logout();
                    }
                }
            }
        }

        $this->unauthenticated($request, $guards);
    }

    protected function notActive($request, $guard) {
        
    }

    protected function redirectTo($request) {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

}
