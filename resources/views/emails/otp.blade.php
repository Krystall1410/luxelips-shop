<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác nhận OTP</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 30px 0;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #ffffff;
            padding: 24px 30px 15px 30px;
            border-bottom: 1px solid #f1f5f9;
        }
        .header-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .body {
            padding: 28px 30px;
        }
        .greeting {
            font-size: 15px;
            color: #334155;
            margin-bottom: 16px;
        }
        .text {
            font-size: 14px;
            color: #475569;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #db2777;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        .notice {
            font-size: 13px;
            color: #64748b;
            margin-top: 20px;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8fafc;
            padding: 18px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="header-title">LuxeLips</div>
            </div>
            <div class="body">
                <p class="greeting">Xin chào <strong>{{ $recipientName }}</strong>,</p>
                <p class="text">Cảm ơn bạn đã đăng ký tài khoản tại LuxeLips. Dưới đây là mã xác thực OTP của bạn:</p>
                
                <div class="otp-box">
                    <div class="otp-code">{{ $otpCode }}</div>
                </div>

                <p class="notice">
                    Mã OTP này có hiệu lực trong vòng <strong>10 phút</strong>.<br>
                    Vì lý do bảo mật, tuyệt đối không chia sẻ mã này cho bất kỳ ai khác.
                </p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} LuxeLips. Tất cả các quyền được bảo lưu.
            </div>
        </div>
    </div>
</body>
</html>
