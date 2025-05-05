<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Kiểm tra nếu người dùng là admin (có quyền truy cập tất cả)
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Kiểm tra nếu không yêu cầu vai trò cụ thể
        if (empty($roles)) {
            return $next($request);
        }

        // Kiểm tra nếu người dùng có một trong các vai trò được yêu cầu
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Nếu không có quyền truy cập, chuyển hướng về trang chủ với thông báo lỗi
        return redirect()->route('dashboard')
            ->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
