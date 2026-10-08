# QUY TẮC PHÁT TRIỂN & BẢO TRÌ DỰ ÁN LARAVEL (AGENT RULES)

Tài liệu này chứa các quy tắc bắt buộc mà AI Agent phải tuân thủ nghiêm ngặt mỗi khi tiếp nhận bất kỳ yêu cầu công việc mới nào trong dự án.

---

## 📌 QUY TẮC BẮT BUỘC (MANDATORY RULES)

### 1. 📂 Tuân Thủ Cấu Trúc Thư Mục Laravel Chuyên Nghiệp (MVC Architecture)
- **Controllers**: Đặt trong `app/Http/Controllers/` và phân nhóm rõ ràng theo Namespace:
  - `Admin/`: Quản lý các chức năng quản trị viên (Backoffice).
  - `User/`: Quản lý các chức năng khách hàng/người dùng (Client).
  - `Auth/`: Quản lý đăng nhập, đăng ký, xác thực OTP, quên mật khẩu.
- **Models**: Đặt trong `app/Models/`. Khai báo đầy đủ `$fillable`, `$casts`, và các quan hệ Eloquent Relationship (`hasMany`, `belongsTo`, `belongsToMany`).
- **Views**: Đặt trong `resources/views/` theo từng phân khu:
  - `resources/views/admin/`: Giao diện Admin.
  - `resources/views/user/`: Giao diện Khách hàng.
  - `resources/views/auth/`: Giao diện Đăng nhập/Đăng ký/Xác thực.
  - `resources/views/emails/`: Blade template gửi Email.
- **Routes**: Khai báo gọn gàng trong `routes/web.php` hoặc `routes/api.php` có middleware bảo vệ (`auth`, `admin`), đặt tên route (`.name()`) chuẩn hóa.
- **Migrations**: Đặt trong `database/migrations/` theo đúng chuẩn thời gian và cú pháp của Laravel.

---

### 2. 📋 Bắt Buộc Lập Kế Hoạch (Plan) Trước Khi Sửa Từ 2 File Trở Lên
- **Nguyên tắc**: Bất kỳ yêu cầu nào đòi hỏi thay đổi hoặc tạo mới **từ 2 file trở lên**, Agent **KHÔNG ĐƯỢC SỬA CODE NGAY** mà phải tạo file `implementation_plan.md` (Implementation Plan) trước.
- **Quy trình bắt buộc**:
  1. Phân tích nguyên nhân và giải pháp kỹ thuật.
  2. Tạo file `implementation_plan.md` với đầy đủ các phần: Nguyên nhân, Chi tiết thay đổi từng file (Proposed Changes), Kế hoạch kiểm tra (Verification Plan).
  3. Dừng lại chờ Người dùng (USER) xem xét và duyệt (bấm **Proceed** hoặc đồng ý) mới bắt đầu thi công code.

---

### 3. 🔍 Không Được Giấu/Nuốt Lỗi SQL & Database Errors
- Nghiêm cấm sử dụng `try/catch` rỗng (silent exception handling) hoặc nuốt lỗi database làm ẩn đi nguyên nhân thật sự của sự cố.
- Mọi truy vấn CSDL bị lỗi (truy vấn sai tên cột, lỗi khóa ngoại, NULL pointer...) phải được log chi tiết qua `Log::error()` và phản hồi thông báo lỗi rõ ràng.
- Bắt buộc kiểm tra nguyên nhân gốc rễ (Root Cause) từ log hoặc stack trace trước khi thực hiện sửa mã nguồn.

---

### 4. 🔗 Khóa Chính, Khóa Ngoại & Migrations Phải Trùng Khớp Hoàn Toàn
- Tất cả khóa chính (`id`), khóa ngoại (`foreignId`, `unsignedBigInteger`) và các trường dữ liệu liên kết giữa các bảng trong Migrations phải khớp 100% về kiểu dữ liệu và định dạng.
- Trong Eloquent Models: mảng `$fillable` phải chứa đầy đủ tất cả các cột được phép nhập liệu (bao gồm `email_verified_at`, `status`, `foreign_id`...), tránh lỗi bỏ sót thuộc tính làm CSDL bị `NULL`.
- Đảm bảo các ràng buộc khóa ngoại (Foreign Key Constraints) được khai báo chính xác (`onDelete('cascade')` hoặc `nullOnDelete()`) đúng theo nghiệp vụ CSDL.

---

### 5. 🛠 Quy Trình Kiểm Tra & Chạy Test
- Sau khi viết/sửa code, Agent chỉ cần chạy lệnh kiểm tra cú pháp (ví dụ: `php -l <path-to-file>`) hoặc chạy lệnh test tự động để xác nhận mã nguồn không bị lỗi. Không cần làm các bước kiểm thử thủ công phức tạp.
- Báo cáo kết quả công việc ngắn gọn, minh bạch, chỉ rõ các file đã thay đổi kèm clickable links (`file:///...`).
