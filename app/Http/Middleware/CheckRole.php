<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Xử lý trường hợp role field chưa tồn tại (migration chưa chạy)
        if (!isset($user->role)) {
            // Nếu chưa có role field, coi như user thường
            if ($role === 'admin') {
                return redirect()->route('pos.index');
            }
            return $next($request);
        }

        if ($user->role !== $role) {
            // Nếu user không có quyền, chuyển hướng về trang phù hợp với role của họ
            if ($user->isAdmin()) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('pos.index');
            }
        }

        return $next($request);
    }
}
