<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Vui lòng nhập Email hoặc Tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $loginInput = trim($request->input('login'));
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Đăng nhập thành công! Chào mừng Admin ' . Auth::user()->name);
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Đăng nhập thành công! Chào mừng ' . Auth::user()->name);
        }

        return back()->withErrors([
            'login' => 'Tên đăng nhập / Email hoặc Mật khẩu không chính xác.',
        ])->withInput($request->only('login'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất thành công!');
    }
}
