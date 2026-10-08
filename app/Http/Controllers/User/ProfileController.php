<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display user profile management page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ], [
            'name.required' => 'Vui lòng nhập Họ và tên.',
            'username.required' => 'Vui lòng nhập Tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng bởi tài khoản khác.',
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.unique' => 'Địa chỉ Email này đã được đăng ký bởi tài khoản khác.',
            'avatar.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'avatar.max' => 'Dung lượng ảnh đại diện tối đa là 4MB.',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $request->phone;

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Check if Email was changed
        if ($request->email !== $user->email) {
            $newEmail = $request->email;
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            session()->put('pending_email_update', [
                'new_email' => $newEmail,
                'otp_code' => $otpCode,
                'expires_at' => now()->addMinutes(10),
            ]);

            // Save non-email changes first
            $user->save();

            // Send OTP Mail to the NEW email address
            try {
                Mail::to($newEmail)->send(new OtpVerificationMail($otpCode));
            } catch (\Exception $e) {
                // If mail fails, proceed and notify user
            }

            return redirect()->route('profile.verify_email.form')->with('warning', "Thông tin đã được lưu! Mã OTP 6 chữ số vừa được gửi tới Email mới ({$newEmail}). Vui lòng nhập mã OTP để xác nhận đổi Email.");
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin tài khoản thành công!');
    }

    /**
     * Show form to enter OTP for new email verification.
     */
    public function showVerifyEmailOtpForm()
    {
        $pending = session('pending_email_update');

        if (!$pending) {
            return redirect()->route('profile.index');
        }

        return view('user.profile.verify_otp', [
            'newEmail' => $pending['new_email'],
        ]);
    }

    /**
     * Verify OTP code for new email update.
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 chữ số.',
            'otp_code.size' => 'Mã OTP phải bao gồm đúng 6 chữ số.',
        ]);

        $pending = session('pending_email_update');

        if (!$pending) {
            return redirect()->route('profile.index')->with('error', 'Phiên xác thực đã hết hạn. Vui lòng thử lại!');
        }

        if ($request->otp_code === $pending['otp_code']) {
            $user = Auth::user();
            $user->email = $pending['new_email'];
            $user->email_verified_at = now();
            $user->save();

            session()->forget('pending_email_update');

            return redirect()->route('profile.index')->with('success', "Xác thực Email mới ({$user->email}) thành công!");
        }

        return redirect()->back()->with('error', 'Mã OTP không chính xác. Vui lòng kiểm tra lại hộp thư Email mới!');
    }
}
