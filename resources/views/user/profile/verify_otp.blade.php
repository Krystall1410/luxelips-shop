<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực OTP Email Mới - LuxeLips PRO</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-dark: #f8fafc;
            --card-dark: #ffffff;
            --border-dark: rgba(236, 72, 153, 0.22);
            --rose-primary: #ec4899;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(236, 72, 153, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
        }

        .text-white {
            color: #0f172a !important;
        }

        /* Bảo đảm icon trên nền hồng gradient luôn trắng sáng */
        .btn-rose-submit,
        .btn-rose-submit i,
        .rounded-circle[style*="linear-gradient"] i {
            color: #ffffff !important;
        }

        .brand-font { font-family: 'Plus Jakarta Sans', sans-serif; }

        .otp-card {
            background-color: #ffffff;
            backdrop-filter: blur(16px);
            border: 1px solid rgba(236, 72, 153, 0.25);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.08);
            max-width: 480px;
            width: 100%;
        }

        .otp-input {
            letter-spacing: 12px;
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            background-color: #ffffff !important;
            border: 2px solid rgba(236, 72, 153, 0.3) !important;
            color: #0f172a !important;
            border-radius: 16px;
            padding: 0.75rem;
        }
        .otp-input:focus {
            border-color: var(--rose-primary) !important;
            box-shadow: 0 0 20px rgba(236, 72, 153, 0.25) !important;
        }

        .btn-rose-submit {
            background: linear-gradient(135deg, #ec4899 0%, #e11d48 100%);
            color: #ffffff !important;
            border: none;
            padding: 0.85rem 1.8rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.45);
            width: 100%;
        }
        .btn-rose-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(236, 72, 153, 0.65);
        }
    </style>
</head>
<body>

    <div class="container p-3">
        <div class="otp-card p-4 p-md-5 mx-auto text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-lg" style="width: 64px; height: 64px; background: linear-gradient(135deg, #ec4899, #e11d48);">
                <i class="bi bi-envelope-check-fill fs-2" style="color: #ffffff !important;"></i>
            </div>

            <h4 class="fw-bold text-white mb-2 brand-font">Xác Thực Email Mới</h4>
            <p class="fs-7 mb-4" style="color: #64748b;">
                Chúng tôi đã gửi mã OTP 6 chữ số tới địa chỉ Email mới: <strong class="text-rose" style="color: #ec4899;">{{ $newEmail }}</strong>. Vui lòng nhập mã để hoàn tất.
            </p>

            @if(session('error'))
                <div class="alert alert-danger rounded-3 mb-4 text-white border-0 fs-7" style="background: linear-gradient(135deg, #e11d48, #be123c);">
                    <i class="bi bi-x-circle me-1"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('profile.verify_email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" 
                           name="otp_code" 
                           class="form-control otp-input" 
                           placeholder="000000" 
                           maxlength="6" 
                           pattern="\d{6}" 
                           required 
                           autofocus>
                </div>

                <button type="submit" class="btn-rose-submit d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-shield-check fs-5"></i>
                    <span>Xác Nhận & Cập Nhật Email</span>
                </button>
            </form>

            <div class="mt-4 fs-8" style="color: #64748b;">
                <a href="{{ route('profile.index') }}" class="text-decoration-none" style="color: #ec4899; font-weight: 600;">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Hồ sơ cá nhân
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
