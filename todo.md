# Checklist Công Việc Dự Án Quản Lý Thành Viên CLB Thể Dục

## 1. Cấu trúc và Thiết lập Dự án
- [x] Khởi tạo dự án Laravel
- [x] Cấu hình cơ sở dữ liệu
- [x] Cài đặt các package cần thiết (QR code generation, etc.)
- [x] Thiết lập authentication (sử dụng Laravel Breeze hoặc Jetstream)
- [x] Cấu hình mail server cho thông báo

## 2. Thiết kế Cơ sở dữ liệu
- [x] Tạo migration cho bảng `users` (mở rộng bảng mặc định)
  - Thêm các trường: phone, birth_date, address, member_type, qr_code, role
- [x] Tạo migration cho bảng `memberships`
  - Các trường: user_id, amount, start_date, end_date, status
- [x] Tạo migration cho bảng `check_ins`
  - Các trường: user_id, check_in_time, check_out_time, date
- [x] Tạo migration cho bảng `notifications`
  - Các trường: user_id, message, type, read
- [x] Tạo seeder dữ liệu mẫu

## 3. Xây dựng Models
- [x] Mở rộng model User
  - Tạo relationship với Membership và CheckIn
  - Tạo các accessor/mutator cần thiết
- [x] Tạo model Membership
  - Tạo relationship với User
  - Thêm scope để lọc theo trạng thái
- [x] Tạo model CheckIn
  - Tạo relationship với User
- [x] Tạo model Notification
  - Tạo relationship với User

## 4. Quản lý thành viên
- [x] Tạo controller MemberController
- [x] Xây dựng view danh sách thành viên (index)
- [x] Xây dựng form thêm thành viên mới
- [x] Xây dựng view chi tiết thành viên
- [x] Xây dựng form chỉnh sửa thành viên
- [x] Xây dựng chức năng xóa thành viên
- [x] Tạo QR code khi thêm thành viên mới

## 5. Quản lý đóng phí
- [x] Tạo controller MembershipController
- [x] Xây dựng view danh sách phí
- [x] Xây dựng form thêm phí mới
- [x] Xây dựng form chỉnh sửa phí
- [x] Xây dựng view chi tiết phí
- [x] Tạo chức năng gia hạn phí
- [x] Tạo chức năng cập nhật trạng thái phí tự động
- [x] Tạo thông báo khi phí sắp hết hạn
- [x] Xây dựng view danh sách phí sắp hết hạn

## 6. Check-in/Check-out bằng QR code
- [x] Tạo controller CheckInController
- [x] Xây dựng controller QrCodeController
- [x] Xây dựng giao diện quét QR
- [x] Xây dựng chức năng check-in (quét QR, kiểm tra phí)
- [x] Xây dựng chức năng check-out (quét QR, cập nhật thời gian ra)
- [x] Xây dựng view lịch sử check-in/check-out

## 7. Hệ thống thông báo
- [x] Tạo NotificationController
- [x] Xây dựng view danh sách thông báo
- [x] Xây dựng chức năng đánh dấu thông báo đã đọc
- [x] Tạo chức năng gửi thông báo cho người dùng
- [x] Tạo event/listener cho thông báo khi phí sắp hết hạn
- [x] Tạo scheduler để kiểm tra phí hết hạn hàng ngày
- [x] Cấu hình hệ thống email để gửi thông báo

## 8. Giao diện admin
- [ ] Xây dựng dashboard với biểu đồ thống kê
- [x] Tạo view quản lý thành viên
- [x] Tạo view quản lý phí
- [x] Tạo view quản lý check-in/check-out
- [x] Tạo view quản lý thông báo
- [ ] Tạo chức năng xuất báo cáo (PDF/Excel)

## 9. Giao diện thành viên
- [x] Xây dựng view thông tin cá nhân
- [x] Xây dựng view hiển thị QR code
- [x] Xây dựng view lịch sử đóng phí
- [x] Xây dựng view lịch sử check-in/check-out
- [x] Xây dựng view xem thông báo

## 10. Bảo mật và phân quyền
- [x] Tạo middleware kiểm tra role
- [x] Áp dụng middleware vào các route cần bảo vệ
- [x] Thêm validation cho tất cả form nhập liệu
- [ ] Cấu hình Gate/Policy cho các model

## 11. Kiểm thử
- [ ] Viết Unit tests cho các model
- [ ] Viết Feature tests cho các controller
- [ ] Kiểm thử chức năng QR code
- [ ] Kiểm thử chức năng thông báo tự động

## 12. Tối ưu hóa và hoàn thiện
- [ ] Tối ưu hóa truy vấn database
- [ ] Thêm cache nếu cần thiết
- [ ] Kiểm tra responsive trên mobile
- [ ] Refactor code và clean up

## 13. Triển khai
- [ ] Chuẩn bị môi trường production
- [ ] Triển khai database
- [ ] Triển khai application
- [ ] Cấu hình scheduler cho các tác vụ tự động
- [ ] Kiểm tra sau triển khai