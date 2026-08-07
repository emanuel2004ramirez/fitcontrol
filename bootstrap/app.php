<?php

use App\Http\Middleware\EnsureSessionPermission;
use App\Http\Middleware\EnsurePasswordIsChanged;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', EnsurePasswordIsChanged::class);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => EnsureSessionPermission::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 419) {
                return redirect()->route('login')->with(
                    'warning',
                    'Tu sesión expiró. Por favor, inicia sesión nuevamente.'
                );
            }

            return $response;
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if (! $request->expectsJson() && in_array($sqlState, ['45000', '23000'], true)) {
                $message = $sqlState === '45000'
                    ? (string) ($exception->errorInfo[2] ?? 'No fue posible completar la operación.')
                    : 'Ya existe un registro con los mismos datos únicos.';
                $message = preg_replace('/^\d+\s+/', '', $message) ?: 'No fue posible completar la operación.';

                return back()->withInput()->withErrors(['database' => $message]);
            }

            return null;
        });
    })->create();
