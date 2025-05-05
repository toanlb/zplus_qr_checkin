# Hệ Thống Điểm Danh QR Cho Câu Lạc Bộ Thể Dục

Hệ thống quản lý thành viên và điểm danh dựa trên mã QR toàn diện cho câu lạc bộ thể dục.

## Tính Năng

- **Quản Lý Thành Viên**: Thêm, sửa, xem và quản lý thành viên câu lạc bộ với các loại thành viên khác nhau (Tiêu chuẩn, Cao cấp, VIP)
- **Điểm Danh Bằng Mã QR**: Tạo mã QR độc nhất cho thành viên và xử lý quy trình check-in và check-out
- **Quản Lý Tư Cách Thành Viên**: Theo dõi phí thành viên, ngày hết hạn và trạng thái tư cách thành viên
- **Thông Báo**: Tự động gửi thông báo cho các tư cách thành viên sắp hết hạn
- **Kiểm Soát Truy Cập Dựa Trên Vai Trò**: Các cấp độ truy cập khác nhau cho quản trị viên và thành viên
- **Báo Cáo**: Tạo báo cáo về hoạt động của thành viên, lượt check-in và doanh thu
- **Giao Diện Thân Thiện Với Thiết Bị Di Động**: Truy cập cho cả thiết bị di động và máy tính

## Yêu Cầu Hệ Thống

- PHP 8.1 trở lên
- MySQL 5.7 trở lên
- Composer
- Node.js và NPM

## Cài Đặt

1. Sao chép kho lưu trữ:
```bash
git clone [URL kho lưu trữ]
cd qr-checkin
```

2. Cài đặt các phụ thuộc PHP:
```bash
composer install
```

3. Cài đặt các phụ thuộc JavaScript:
```bash
npm install
```

4. Tạo bản sao của file .env:
```bash
cp .env.example .env
```

5. Tạo khóa ứng dụng:
```bash
php artisan key:generate
```

6. Cấu hình kết nối cơ sở dữ liệu trong file .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qr_checkin
DB_USERNAME=root
DB_PASSWORD=your_password
```

7. Chạy migration cơ sở dữ liệu và tạo dữ liệu ban đầu:
```bash
php artisan migrate --seed
```

8. Biên dịch tài nguyên:
```bash
npm run dev
```

9. Khởi động máy chủ phát triển:
```bash
php artisan serve
```

10. Truy cập ứng dụng tại http://localhost:8000

## Tài Khoản Quản Trị Mặc Định

Sau khi chạy seeder cơ sở dữ liệu, bạn có thể đăng nhập bằng thông tin đăng nhập quản trị sau:

- Email: admin@example.com
- Mật khẩu: password

## Tài Liệu

- [Hướng Dẫn Quản Trị Viên](docs/admin-guide.md) - Hướng dẫn chi tiết cho quản trị viên hệ thống
- [Hướng Dẫn Người Dùng](docs/user-guide.md) - Hướng dẫn cho thành viên thông thường

## Giấy Phép

Dự án này được cấp phép theo Giấy Phép MIT - xem file LICENSE để biết chi tiết.

## Thiết Kế Hệ Thống Gốc

Tài liệu thiết kế hệ thống gốc có sẵn trong [DESIGN.md](DESIGN.md)