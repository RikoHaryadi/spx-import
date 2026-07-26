<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (!auth()->user()->is_active) {
            auth()->logout();

            return redirect('/login')
                ->withErrors([
                    'email' => 'Akun Anda sudah dinonaktifkan.'
                ]);
        }

        return $next($request);
    }
}