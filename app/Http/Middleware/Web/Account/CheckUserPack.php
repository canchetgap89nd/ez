<?php

namespace App\Http\Middleware\Web\Account;

use Closure;

class CheckUserPack
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
        $packs = $this->getPackInRoute($request->route());
        if ($this->userHasPack($request->user(), $packs)) {
            return $next($request);
        }
        return redirect()->route('notify.user.expire');
    }

    public function getPackInRoute($route)
    {
        $action = $route->getAction();
        return isset($action['packs']) ? $action['packs'] : null;
    }

    public function userHasPack($user, $packs)
    {
        if ($user->hasPack($packs)) {
            return true;
        }
        return false;
    }
}
