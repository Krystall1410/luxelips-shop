<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập tài khoản Quản trị để tiếp tục.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Cảnh báo: Tài khoản của bạn không có quyền truy cập khu vực Admin!');
        }

        return $next($request);
    }
}
