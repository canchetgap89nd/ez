<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Auth;

class CheckHasBlocked
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
        $user = User::find(Auth::guard('web')->id());
        if ($this->hasBlocked($user)) {
            return redirect()->route('client.account.blocked');
        }
        return $next($request);
    }

    /**
     * check if user is blocked
     * @param  User    $user [model User]
     * @return boolean       [description]
     */
    public function hasBlocked(User $user)
    {
        if ($user->blocked) {
            if ($user->time_expire_blocked) {
                // check time if has time blocked
                $hasTimeExpire = strtotime($user->time_expire_blocked) >= time();
                if ($hasTimeExpire) {
                    return true;
                }
                // clear blocked if timeout
                $user->update([
                    'blocked' => false,
                    'time_expire_blocked' => null
                ]);
                return false;
            }
            return true;
        }
        return false;
    }
}
