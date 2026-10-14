<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->with('error', 'Không thể kết nối với tài khoản Google hoặc bạn đã hủy đăng nhập.');
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            return redirect()->route('login')->with('error', 'Không lấy được thông tin email từ tài khoản Google của bạn.');
        }

        try {
            // 1. Check if user already exists with this google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Update avatar if not present
                if (!$user->avatar && $googleUser->getAvatar()) {
                    $user->update(['avatar' => $googleUser->getAvatar()]);
                }

                Auth::login($user, true);
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập bằng tài khoản Google thành công!');
            }

            // 2. Check if user exists with the same email
            $existingUserByEmail = User::where('email', $googleUser->getEmail())->first();

            if ($existingUserByEmail) {
                $existingUserByEmail->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $existingUserByEmail->avatar ?: $googleUser->getAvatar(),
                    'email_verified_at' => $existingUserByEmail->email_verified_at ?? now(),
                ]);

                Auth::login($existingUserByEmail, true);
                return redirect()->intended(route('home'))->with('success', 'Đã liên kết và đăng nhập bằng tài khoản Google thành công!');
            }

            // 3. Create a brand new user
            $newUser = User::create([
                'name' => $googleUser->getName() ?: 'Khách hàng LuxeLips',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);

            Auth::login($newUser, true);
            return redirect()->intended(route('home'))->with('success', 'Chào mừng bạn! Tài khoản đã được tạo tự động qua Google.');

        } catch (\Throwable $e) {
            Log::error('Error saving Google user: ' . $e->getMessage(), [
                'email' => $googleUser->getEmail(),
                'exception' => $e,
            ]);

            return redirect()->route('login')->with('error', 'Đã xảy ra lỗi khi xử lý thông tin tài khoản: ' . $e->getMessage());
        }
    }
}
