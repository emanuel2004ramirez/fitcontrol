<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->debe_cambiar_password && ! $request->routeIs('password.change.*', 'logout')) {
            return redirect()->route('password.change.edit')
                ->with('warning', 'Debes crear una nueva contraseña antes de continuar.');
        }

        return $next($request);
    }
}
