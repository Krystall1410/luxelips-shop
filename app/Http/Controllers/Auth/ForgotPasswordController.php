<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Show form to request password reset OTP.
     */
    public function showLinkRequestForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }
        return view('Auth.forgot_password');
    }

    /**
     * Send password reset OTP email.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Địa chỉ Email không đúng định dạng.',
            'email.exists' => 'Không tìm thấy tài khoản nào gắn liền với địa chỉ Email này.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        $otpCode = sprintf('%06d', mt_rand(100000, 999999));

        session([
            'reset_password_email' => $email,
            'reset_otp_code' => $otpCode,
            'reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        // Attempt sending email
        $mailSent = false;
        try {
            Mail::to($email)->send(new ResetPasswordOtpMail($otpCode, $user->name));
            $mailSent = true;
        } catch (\Exception $e) {
            Log::error("Failed to send Password Reset OTP Email to {$email}: " . $e->getMessage());
        }

        Log::info("=== RESET PASSWORD OTP CODE FOR {$email} ===: {$otpCode}");

        if ($mailSent) {
            return redirect()->route('password.reset.form')
                ->with('success', "Mã OTP 6 chữ số đã được gửi thành công tới email {$email}. Vui lòng kiểm tra hộp thư để tiếp tục đặt lại mật khẩu.");
        }

        return redirect()->route('password.reset.form')
            ->with('warning', "Đã tạo mã OTP khôi phục cho email {$email}, nhưng có sự cố máy chủ gửi Email. Bạn có thể bấm 'Gửi lại mã OTP' hoặc kiểm tra nhật ký log.");
    }

    /**
     * Show form to enter OTP and new password.
     */
    public function showResetForm()
    {
        if (!session()->has('reset_password_email') || !session()->has('reset_otp_code')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Phiên khôi phục mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng gửi lại yêu cầu mới.');
        }

        return view('Auth.reset_password');
    }

    /**
     * Verify OTP and update password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'otp.required' => 'Vui lòng nhập Mã OTP 6 chữ số.',
            'otp.size' => 'Mã OTP phải bao gồm đúng 6 chữ số.',
            'password.required' => 'Vui lòng nhập Mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $sessionEmail = session('reset_password_email');
        $sessionOtp = session('reset_otp_code');
        $expiresAt = session('reset_otp_expires_at');

        if (!$sessionEmail || !$sessionOtp) {
            return redirect()->route('password.forgot')->with('error', 'Phiên làm việc đã hết hạn. Vui lòng thực hiện lại.');
        }

        if (now()->timestamp > $expiresAt) {
            return redirect()->back()->with('error', 'Mã OTP đã hết hạn (sau 10 phút). Vui lòng bấm gửi lại mã OTP mới.');
        }

        if (trim($request->otp) !== (string) $sessionOtp) {
            return redirect()->back()->with('error', 'Mã OTP bạn nhập không chính xác. Vui lòng kiểm tra kỹ lại.');
        }

        $user = User::where('email', $sessionEmail)->first();
        if (!$user) {
            return redirect()->route('password.forgot')->with('error', 'Không tìm thấy thông tin tài khoản người dùng.');
        }

        // Update User Password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Forget Reset Sessions
        session()->forget(['reset_password_email', 'reset_otp_code', 'reset_otp_expires_at']);

        return redirect()->route('login')
            ->with('success', '🎉 Đặt lại mật khẩu thành công! Vui lòng sử dụng mật khẩu mới để đăng nhập.');
    }

    /**
     * Resend Reset OTP code.
     */
    public function resendOtp(Request $request)
    {
        $email = session('reset_password_email');
        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Không tìm thấy yêu cầu khôi phục mật khẩu.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.forgot')->with('error', 'Tài khoản người dùng không tồn tại.');
        }

        $otpCode = sprintf('%06d', mt_rand(100000, 999999));

        session([
            'reset_otp_code' => $otpCode,
            'reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        try {
            Mail::to($email)->send(new ResetPasswordOtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            Log::error("Failed to resend Reset OTP Email to {$email}: " . $e->getMessage());
        }

        Log::info("=== RESENT RESET OTP CODE FOR {$email} ===: {$otpCode}");

        return redirect()->back()->with('success', "Đã gửi lại mã OTP mới tới email {$email}. Vui lòng kiểm tra lại hộp thư.");
    }
}
