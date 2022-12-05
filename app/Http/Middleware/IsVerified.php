<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use NextApps\VerificationCode\VerificationCode;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $next($request);
        }

        // verify via link
        // $request->user()->sendEmailVerificationNotification();
        
        // verify via code
        VerificationCode::send($request->user()->email);

        return response()->json('Your account is inactive. Verification email sent on your email id please verify your account first.');
    }
}
