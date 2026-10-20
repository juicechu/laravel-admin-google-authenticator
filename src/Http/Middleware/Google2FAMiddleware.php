<?php

namespace TechDjoin\LaravelAdminGoogleAuthenticator\Http\Middleware;

use Closure;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('google2fa.enabled')) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        // Logout must always stay reachable: a user who does not want to
        // continue the 2FA steps must be able to sign out instead.
        if ($request->routeIs('admin.logout')) {
            return $next($request);
        }

        // When mandatory, an admin without a secret must enroll before using
        // the panel. Only the 2FA routes themselves stay reachable (setting /
        // enable / disable / verify) so the enrollment flow cannot loop back
        // into this redirect.
        if (config('google2fa.mandatory', false) && !$user->google2faIsEnabled()) {
            if (!$request->routeIs('admin.2fa.*')) {
                return redirect()->route('admin.2fa.settings');
            }

            return $next($request);
        }

        // Without a secret there is nothing to challenge: pass through.
        if (!$user->google2faIsEnabled()) {
            return $next($request);
        }

        $authenticator = app(Authenticator::class)->boot($request);
        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}