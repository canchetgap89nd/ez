<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckHasPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->hasPageActive()) {
            return $next($request);
        }
        return redirect()->route('set.info');
    }

    public function hasPageActive()
    {
        $user = Auth::guard('web')->user();
        $count = $user->pages()->count();
        $checkIsWare = $user->isWare();
        if ($count > 0 || $checkIsWare) {
            return true;
        }
        return false;
    }
}
