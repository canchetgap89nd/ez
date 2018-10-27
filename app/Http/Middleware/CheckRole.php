<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
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
        $roles = $this->getRoleInRoute($request->route());
        if (!$roles || $request->user()->hasRole($roles)) {
            return $next($request);
        }
        return redirect()->route('no_permission');
    }

    /**
     * [getRoleInRoute get role from route]
     * @param  [type] $route 
     * @return [type]        [role]
     */
    public function getRoleInRoute($route)
    {
        $action = $route->getAction();
        return isset($action['packs']) ? $action['packs'] : null;
    }
}
