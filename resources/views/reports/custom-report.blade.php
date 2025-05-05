@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Báo Cáo Tùy Chỉnh</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>
    
    <!-- Report Generator -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Tạo báo cáo tùy chỉnh</h2>
        
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('reports.custom-report') }}" method="post" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Report Type -->
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Loại báo cáo</label>
                    <select name="report_type" id="report_type" class="form-select rounded-md shadow-sm w-full" required>
                        <option value="">-- Chọn loại báo cáo --</option>
                        <option value="user_activity">Hoạt động người dùng</option>
                        <option value="memberships">Tình trạng thành viên</option>
                        <option value="check_ins">Lịch sử check-in</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Chọn loại dữ liệu bạn muốn xuất.</p>
                </div>
                
                <!-- Export Format -->
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Định dạng xuất</label>
                    <select name="format" id="format" class="form-select rounded-md shadow-sm w-full" required>
                        <option value="csv">CSV</option>
                        <option value="pdf" disabled>PDF (Sắp có)</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Chọn định dạng file bạn muốn tải xuống.</p>
                </div>
            </div>
            
            <!-- Date Range -->
            <div class="border-t pt-4 mt-4">
                <h3 class="font-medium mb-2">Khoảng thời gian</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                        <input type="date" name="start_date" id="start_date" class="form-input rounded-md shadow-sm w-full" 
                               value="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                        <input type="date" name="end_date" id="end_date" class="form-input rounded-md shadow-sm w-full" 
                               value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-1">Chọn khoảng thời gian bạn muốn xem dữ liệu.</p>
            </div>
            
            <!-- Submit Button -->
            <div class="border-t pt-4 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    <i class="fas fa-file-export mr-2"></i> Tạo báo cáo
                </button>
            </div>
        </form>
    </div>
    
    <!-- Report Types Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-blue-600 text-3xl mb-3">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-lg font-medium mb-2">Hoạt động người dùng</h3>
            <p class="text-gray-600 text-sm">Báo cáo về các hoạt động check-in của từng thành viên, bao gồm tổng số lần check-in, thời gian sử dụng và mức độ thường xuyên trong khoảng thời gian đã chọn.</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-blue-600 text-3xl mb-3">
                <i class="fas fa-id-card"></i>
            </div>
            <h3 class="text-lg font-medium mb-2">Tình trạng thành viên</h3>
            <p class="text-gray-600 text-sm">Báo cáo về tình trạng các gói thành viên, bao gồm ngày bắt đầu, ngày kết thúc, tình trạng hiện tại và thông tin khác về các gói thành viên.</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-blue-600 text-3xl mb-3">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="text-lg font-medium mb-2">Lịch sử check-in</h3>
            <p class="text-gray-600 text-sm">Báo cáo chi tiết về tất cả các lượt check-in trong khoảng thời gian đã chọn, bao gồm thời gian check-in, check-out và thời lượng sử dụng.</p>
        </div>
    </div>
</div>
@endsection