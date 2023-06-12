<?php

namespace App\Http\Middleware;

use App\Traits\ResponseApiTrait;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailsVerifiedAPI
{
    use ResponseApiTrait;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return $this->sendError('Your email address is not verified. Please check inbox/spam email.', [], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
