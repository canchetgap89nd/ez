<?php

namespace App\Http\Middleware;

use Closure;

class PermissionApi
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
        if ($request->user()->hasRole($roles)) {
            return $next($request);
        }
        return response()->json(['message' => 'No permission'], 403);
    }

    /**
     * [getRoleInRoute get role from route]
     * @param  [type] $route 
     * @return [type]        [role]
     */
    public function getRoleInRoute($route)
    {
        $action = $route->getAction();
        return isset($action['roles']) ? $action['roles'] : null;
    }
}
