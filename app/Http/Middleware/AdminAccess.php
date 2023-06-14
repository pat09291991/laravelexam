<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccess
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
        $user = $request->user();
        $adminRole = $user->roles->map(function ($userRole) {
            if ($userRole->role->name == "Administrator") {
                return true;
            }
        });

        if ($adminRole->contains(true)) {
            return $next($request);
        }

        return response()->json(['error' => "Permission denied."], 401);
    }
}
