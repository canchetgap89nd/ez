<?php

namespace App\Http\Middleware\Web\Account;

use Closure;
use App\Traits\UserTrait;

class CheckPageLimit
{
    use UserTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inPageLimit($request->user())) {
            return $next($request);
        }
        return redirect()->route('set.info')->with([
            'status' => 'danger',
            'message' => 'Tài khoản của bạn chỉ có thể kích hoạt tối đa ' . $this->getPageLimit($request->user()) . ' Fanpage. Vui lòng nâng cấp tài khoản để có thể kích hoạt không giới hạn Fanpage.'
        ]);
    }
}
