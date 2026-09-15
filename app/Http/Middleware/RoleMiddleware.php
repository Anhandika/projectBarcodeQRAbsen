<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ✅ CHECK 1: Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user?->role?->value;

        // ✅ CHECK 2: Ensure user has a role assigned
        if (!$userRole) {
            \Log::warning('User role not set', [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'path' => $request->path(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Peran pengguna tidak ditemukan. Silakan login kembali.');
        }

        // ✅ CHECK 3: Ensure user's role is in allowed roles
        if (!in_array($userRole, $roles, true)) {
            \Log::warning('Unauthorized role access attempt', [
                'user_id' => auth()->id(),
                'user_role' => $userRole,
                'required_roles' => $roles,
                'ip_address' => $request->ip(),
                'path' => $request->path(),
            ]);

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
