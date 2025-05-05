# Thiết Kế Hệ Thống Quản Lý Thành Viên CLB Thể Dục

## 1. Mục tiêu
Hệ thống quản lý thành viên CLB thể dục được thiết kế để
- Quản lý thông tin thành viên (thêm, sửa, xóa, xem chi tiết).
- Theo dõi và quản lý các khoản phí thành viên, bao gồm nhắc nhở khi phí sắp hết hạn.
- Hỗ trợ check-incheck-out bằng mã QR, lưu lịch sử ravào.
- Cung cấp giao diện quản trị cho admin và giao diện cá nhân cho thành viên.

## 2. Tính năng chính

### 2.1. Quản lý thành viên
- Mô tả Quản lý toàn bộ thông tin thành viên của CLB.
- Thông tin thành viên
  - Họ tên, email, số điện thoại, ngày sinh, địa chỉ.
  - Loại thành viên Standard, Premium, VIP.
  - Mã QR duy nhất cho check-incheck-out.
- Chức năng
  - Thêm thành viên mới, tự động tạo mã QR.
  - Sửa thông tin thành viên.
  - Xóa thành viên (chỉ khi không có phí hoặc lịch sử check-in liên quan).
  - Xem danh sách và chi tiết thành viên (lọc theo loại thành viên hoặc trạng thái phí).

### 2.2. Quản lý đóng phí
- Mô tả Quản lý các khoản phí thành viên, theo dõi trạng thái và gửi nhắc nhở.
- Thông tin phí
  - Số tiền, ngày bắt đầu, ngày hết hạn.
  - Trạng thái Active, Expired, Pending.
- Chức năng
  - Ghi nhận phí mới cho thành viên.
  - Tự động cập nhật trạng thái phí (chuyển sang Expired khi hết hạn).
  - Gửi thông báo nhắc nhở (email hoặc giao diện) khi phí còn 7 ngày hoặc ít hơn.
  - Xem lịch sử đóng phí của từng thành viên.

### 2.3. Check-incheck-out bằng QR code
- Mô tả Cho phép thành viên check-incheck-out tại CLB bằng mã QR.
- Thông tin check-incheck-out
  - Thời gian check-in, thời gian check-out, ngày.
- Chức năng
  - Quét mã QR để check-in (kiểm tra phí Active).
  - Quét mã QR để check-out (cập nhật thời gian ra).
  - Lưu lịch sử check-incheck-out.
  - Xem lịch sử ravào của từng thành viên.

### 2.4. Quản trị hệ thống
- Mô tả Giao diện dành cho admin để quản lý toàn bộ hệ thống.
- Chức năng
  - Quản lý tài khoản (admin và thành viên) với phân quyền.
  - Xem báo cáo Tổng số thành viên, tổng phí thu, thống kê check-incheck-out.
  - Xuất báo cáo dưới dạng PDF hoặc Excel (tùy chọn).

### 2.5. Giao diện thành viên
- Mô tả Giao diện dành cho thành viên để xem thông tin và tương tác.
- Chức năng
  - Xem thông tin cá nhân, mã QR.
  - Xem lịch sử đóng phí và check-incheck-out.
  - Nhận thông báo nhắc nhở phí (email hoặc giao diện).

## 3. Luồng hoạt động

### 3.1. Quản lý thành viên
1. Thêm thành viên
   - Admin truy cập giao diện quản trị, chọn Thêm thành viên.
   - Nhập thông tin Họ tên, email, số điện thoại, ngày sinh, địa chỉ, loại thành viên.
   - Hệ thống
     - Tạo tài khoản thành viên (mật khẩu mặc định hoặc gửi email xác nhận).
     - Tạo mã QR duy nhất dựa trên ID thành viên.
     - Lưu thông tin vào cơ sở dữ liệu.
   - Kết quả Thông báo thành công, hiển thị mã QR (in hoặc gửi cho thành viên).

2. Cập nhật thông tin
   - Admin chọn thành viên từ danh sách, chỉnh sửa thông tin.
   - Hệ thống cập nhật cơ sở dữ liệu, giữ nguyên mã QR trừ khi cần tái tạo.

3. Xóa thành viên
   - Admin chọn thành viên, xác nhận xóa.
   - Hệ thống kiểm tra Nếu không có phí hoặc lịch sử check-in, xóa thành viên; nếu không, báo lỗi.

4. Xem danh sáchchi tiết
   - Admin xem danh sách thành viên (lọc theo loại hoặc trạng thái phí).
   - Nhấp vào thành viên để xem chi tiết (thông tin, lịch sử phí, check-in).

### 3.2. Quản lý đóng phí
1. Ghi nhận phí mới
   - Admin chọn thành viên, nhập thông tin phí Số tiền, ngày bắt đầu, ngày hết hạn.
   - Hệ thống
     - Lưu phí, đặt trạng thái Active.
     - Gửi thông báo xác nhận (email hoặc giao diện).

2. Nhắc nhở phí hết hạn
   - Hệ thống tự động kiểm tra hàng ngày.
   - Nếu phí còn 7 ngày hoặc ít hơn, gửi thông báo nhắc nhở (email hoặc giao diện).

3. Cập nhật trạng thái phí
   - Hệ thống tự động chuyển trạng thái sang Expired khi hết hạn.
   - Thành viên không thể check-in nếu không có phí Active.

4. Xem lịch sử phí
   - Adminthành viên xem danh sách phí đã đóng (số tiền, ngày bắt đầu, ngày hết hạn, trạng thái).

### 3.3. Check-incheck-out bằng QR code
1. Check-in
   - Thành viên đến CLB, trình mã QR (điện thoại hoặc thẻ in).
   - Nhân viên quét mã QR bằng thiết bị (webcammáy quét).
   - Hệ thống
     - Giải mã QR để lấy ID thành viên.
     - Kiểm tra phí Active.
     - Nếu hợp lệ, ghi nhận check-in (thời gian vào).
     - Nếu không, báo lỗi (phí hết hạn).
   - Kết quả Thông báo Check-in thành công.

2. Check-out
   - Thành viên rời CLB, quét mã QR lần nữa.
   - Hệ thống
     - Tìm bản ghi check-in chưa có thời gian check-out.
     - Cập nhật thời gian check-out.
   - Kết quả Thông báo Check-out thành công.

3. Xem lịch sử check-incheck-out
   - Adminthành viên xem danh sách ravào (thời gian check-in, check-out, ngày).

### 3.4. Quản trị hệ thống
1. Đăng nhập admin
   - Admin đăng nhập bằng tài khoản có vai trò admin.
   - Hệ thống kiểm tra phân quyền.

2. Báo cáo
   - Admin xem báo cáo
     - Tổng số thành viên (theo loại).
     - Tổng phí thu (theo thángnăm).
     - Thống kê check-incheck-out (theo ngàytháng).
   - Xuất báo cáo (PDFExcel, tùy chọn).

### 3.5. Giao diện thành viên
1. Đăng nhập
   - Thành viên đăng nhập bằng emailmật khẩu.
   - Hệ thống hiển thị giao diện cá nhân.

2. Xem thông tin
   - Xem thông tin cá nhân, mã QR, lịch sử phí, lịch sử check-incheck-out.
   - Tải mã QR hoặc hiển thị để quét.

3. Nhận thông báo
   - Hiển thị thông báo phí sắp hết hạn (email hoặc giao diện).

## 4. Luồng người dùng chính

### 4.1. Luồng Admin
1. Đăng nhập → Xem dashboard (tổng quan thành viên, phí, check-in).
2. Quản lý thành viên Thêmsửaxóa, xem chi tiết.
3. Quản lý phí Ghi nhận phí, xem lịch sử.
4. Quản lý check-incheck-out Quét QR, xem lịch sử.
5. Xemxuất báo cáo.

### 4.2. Luồng Thành viên
1. Đăng nhập → Xem thông tin cá nhân, mã QR.
2. Xem lịch sử phí, check-incheck-out.
3. Nhận thông báo phí sắp hết hạn.
4. Sử dụng mã QR để check-incheck-out tại CLB.

### 4.3. Luồng Check-in tại CLB
1. Nhân viên bật giao diện quét QR.
2. Thành viên trình mã QR → Nhân viên quét.
3. Hệ thống xác nhận, ghi nhận check-incheck-out.
4. Hiển thị kết quả trên giao diện.

## 5. Yêu cầu kỹ thuật
- Công nghệ
  - Backend Laravel (PHP).
  - Database MySQL.
  - Frontend Blade (Laravel) hoặc Vue.js (tùy chọn).
  - QR Code Tạoquét bằng thư viện (simplesoftwareiosimple-qrcode, jsQR).
- Bảo mật
  - Phân quyền adminthành viên.
  - Mã hóa mật khẩu, bảo vệ API.
- Hiệu suất
  - Tối ưu truy vấn (Eager Loading).
  - Tự động hóa (kiểm tra phí, gửi thông báo) bằng Scheduler.
- Thông báo
  - Email (Laravel Mail hoặc Mailgun).
  - Tùy chọn SMS (Twilio).

## 6. Ghi chú
- Mở rộng
  - Tích hợp thanh toán trực tuyến (PayPal, Stripe).
  - Ứng dụng di động cho thành viên.
  - Biểu đồ thống kê (Chart.js).
- Giao diện Thân thiện, responsive (PCmobile).
- QR Code Đảm bảo duy nhất, không thể giả mạo.

---