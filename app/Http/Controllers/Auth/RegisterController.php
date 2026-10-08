<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('home');
        }
        return view('auth.register');
    }

    /**
     * Handle a registration request: Generate OTP and send Email.
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Địa chỉ Email không đúng định dạng.',
            'email.unique' => 'Địa chỉ Email này đã được đăng ký.',
            'phone.required' => 'Vui lòng nhập Số điện thoại.',
            'username.required' => 'Vui lòng nhập Tên đăng nhập.',
            'username.alpha_dash' => 'Tên đăng nhập chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập Mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $name = $request->filled('name') ? trim($request->name) : trim($request->username);
        $email = strtolower(trim($request->email));
        $otpCode = sprintf('%06d', mt_rand(100000, 999999));

        // Save pending user data & OTP in session
        session([
            'pending_registration' => [
                'name' => $name,
                'email' => $email,
                'phone' => trim($request->phone),
                'username' => trim($request->username),
                'password' => $request->password,
            ],
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        // Attempt sending email
        $mailSent = false;
        try {
            Mail::to($email)->send(new OtpVerificationMail($otpCode, $name));
            $mailSent = true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP Email to {$email}: " . $e->getMessage());
        }

        Log::info("=== OTP CODE FOR {$email} ===: {$otpCode}");

        if ($mailSent) {
            return redirect()->route('otp.verify.form')
                ->with('success', "Mã OTP 6 chữ số đã được gửi thành công tới hộp thư email: {$email}. Vui lòng kiểm tra hộp thư chính hoặc thư rác/spam.");
        }

        return redirect()->route('otp.verify.form')
            ->with('warning', "Đã tạo yêu cầu đăng ký cho {$email}, nhưng có lỗi xảy ra khi kết nối máy chủ gửi Mail. Vui lòng bấm 'Gửi lại mã OTP' hoặc kiểm tra kết nối.");
    }

    /**
     * Show the OTP verification form.
     */
    public function showOtpForm()
    {
        if (!session()->has('pending_registration') || !session()->has('otp_code')) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký không tồn tại hoặc đã được xác thực trước đó. Vui lòng đăng ký mới.');
        }

        return view('auth.verify_otp');
    }

    /**
     * Verify the entered OTP code and complete user registration.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 chữ số.',
            'otp_code.size' => 'Mã OTP phải chứa đúng 6 chữ số.',
        ]);

        if (!session()->has('pending_registration') || !session()->has('otp_code')) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký không tồn tại hoặc mã OTP đã được sử dụng. Vui lòng đăng ký lại.');
        }

        $sessionOtp = session('otp_code');
        $expiresAt = session('otp_expires_at', 0);

        if (now()->timestamp > $expiresAt) {
            // Hết hạn -> Xóa OTP cũ để không bị dùng lại
            session()->forget(['otp_code', 'otp_expires_at']);
            return back()->with('error', 'Mã OTP đã hết hạn (quá 10 phút). Vui lòng bấm "Gửi lại mã OTP".');
        }

        if (trim($request->otp_code) !== (string)$sessionOtp) {
            return back()->with('error', 'Mã OTP không chính xác. Vui lòng kiểm tra lại.');
        }

        // OTP Validated! Create User in DB
        $pendingData = session('pending_registration');

        $user = User::create([
            'name' => $pendingData['name'],
            'email' => $pendingData['email'],
            'phone' => $pendingData['phone'],
            'username' => $pendingData['username'],
            'password' => Hash::make($pendingData['password']),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Hủy bỏ ngay lập tức dữ liệu OTP trong session (Mã OTP chỉ dùng 1 lần duy nhất)
        session()->forget(['pending_registration', 'otp_code', 'otp_expires_at']);

        // Log the user in
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Xác thực Email thành công! Chào mừng ' . $user->name . ' đến với LuxeLips!');
    }

    /**
     * Resend a new OTP code to user's email.
     */
    public function resendOtp()
    {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.');
        }

        $pendingData = session('pending_registration');
        $email = $pendingData['email'];
        $name = $pendingData['name'];
        $newOtpCode = sprintf('%06d', mt_rand(100000, 999999));

        session([
            'otp_code' => $newOtpCode,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        $mailSent = false;
        try {
            Mail::to($email)->send(new OtpVerificationMail($newOtpCode, $name));
            $mailSent = true;
        } catch (\Exception $e) {
            Log::error("Failed to resend OTP Email to {$email}: " . $e->getMessage());
        }

        Log::info("=== RESENT OTP CODE FOR {$email} ===: {$newOtpCode}");

        if ($mailSent) {
            return back()->with('success', "Mã OTP mới đã được gửi tới hộp thư email: {$email}. Vui lòng kiểm tra hòm thư.");
        }

        return back()->with('error', "Không thể gửi lại email OTP đến {$email}. Vui lòng kiểm tra lại mạng hoặc thông tin email.");
    }
}
