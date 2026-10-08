<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mã OTP Khôi Phục Mật Khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #ec4899 0%, #e11d48 100%); padding: 24px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 1px;">LUXELIPS PRO</h2>
            <p style="margin: 4px 0 0 0; font-size: 13px; opacity: 0.9;">Yêu cầu khôi phục mật khẩu tài khoản</p>
        </div>
        <div style="padding: 30px; color: #334155; font-size: 15px; line-height: 1.6;">
            <p>Xin chào <strong>{{ $recipientName }}</strong>,</p>
            <p>Hệ thống LuxeLips PRO nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với email này.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <span style="font-size: 13px; text-transform: uppercase; color: #64748b; letter-spacing: 1px; display: block; margin-bottom: 8px;">Mã OTP Khôi Phục Mật Khẩu Của Bạn</span>
                <span style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #ec4899; background-color: #fdf2f8; padding: 12px 24px; border-radius: 10px; border: 2px dashed #f472b6; display: inline-block;">
                    {{ $otpCode }}
                </span>
            </div>

            <p style="font-size: 13px; color: #64748b;">* Mã OTP này có hiệu lực trong vòng <strong>10 phút</strong>. Vui lòng không chia sẻ mã này cho bất kỳ ai để bảo mật tài khoản.</p>
            <p style="font-size: 13px; color: #64748b;">Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này hoặc liên hệ bộ phận hỗ trợ khách hàng LuxeLips PRO.</p>
        </div>
        <div style="background-color: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
            © {{ date('Y') }} LuxeLips PRO - Hệ thống phân phối son môi cao cấp.
        </div>
    </div>
</body>
</html>
