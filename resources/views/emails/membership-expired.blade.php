<!DOCTYPE html>
<html>
<head>
    <title>Thông báo: Thành viên của bạn đã hết hạn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #F44336;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thông báo Thành Viên</h1>
    </div>
    
    <div class="content">
        <p>Xin chào {{ $user->name }},</p>
        
        <p>Chúng tôi gửi email này để thông báo rằng <strong>thành viên của bạn tại Câu Lạc Bộ Thể Dục đã hết hạn</strong>.</p>
        
        <p><strong>Chi tiết thành viên:</strong></p>
        <ul>
            <li>Loại thành viên: {{ $user->member_type }}</li>
            <li>Ngày hết hạn: {{ $membership->end_date->format('d/m/Y') }}</li>
        </ul>
        
        <p>Để tiếp tục sử dụng dịch vụ và các tiện ích của Câu Lạc Bộ, vui lòng gia hạn thành viên của bạn ngay hôm nay.</p>
        
        <p><strong>Lưu ý:</strong> Bạn sẽ không thể check-in vào CLB cho đến khi thành viên được gia hạn.</p>
        
        <p>Bạn có thể gia hạn trực tiếp tại Câu Lạc Bộ hoặc qua hệ thống online của chúng tôi.</p>
        
        <a href="{{ url('/dashboard') }}" class="button">Đăng nhập để gia hạn ngay</a>
        
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại được cung cấp dưới đây.</p>
        
        <p>Trân trọng,<br>
        Đội ngũ quản lý Câu Lạc Bộ Thể Dục</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Câu Lạc Bộ Thể Dục. Tất cả các quyền được bảo lưu.</p>
        <p>Địa chỉ: 123 Đường Thể Dục, Quận Thể Thao, TP. Hồ Chí Minh</p>
        <p>Email: info@clbtheduc.com | Điện thoại: (028) 1234 5678</p>
    </div>
</body>
</html>