<?php

namespace App\Http\Middleware\Api;

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
        $user = Auth::guard('api')->user();
        if ($this->hasBlocked($user)) {
            return response()->json([
                'message' => 'account is blocked'
            ], 402);
        }
        return $next($request);
    }

    /**
     * check if user is blocked
     * @param  User    $user [model User]
     * @return boolean       [description]
     */
    public function hasBlocked($user)
    {
        if ($user->blocked) {
            if ($user->time_expire_blocked) {
                // check time if has time blocked
                $hasTimeExpire = strtotime($user->time_expire_blocked) >= time();
                if ($hasTimeExpire) {
                    return true;
                }
                // clear blocked if timeout
                User::find($user->id)->update([
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
