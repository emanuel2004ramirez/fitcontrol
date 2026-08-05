<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $permissions = $request->session()->get('permissions', []);
        abort_unless(in_array('*', $permissions, true) || in_array($permission, $permissions, true), 403, 'No tiene permiso para realizar esta acción.');

        return $next($request);
    }
}
