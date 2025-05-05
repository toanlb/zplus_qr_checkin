<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu người dùng đã đăng nhập
        if (auth()->check()) {
            // Nếu là admin, chuyển hướng đến dashboard
            if (auth()->user()->isAdmin()) {
                return redirect()->route('dashboard');
            }
            
            // Ngược lại là thành viên thường, chuyển hướng đến trang home
            return redirect()->route('member.home');
        }

        return $next($request);
    }
}