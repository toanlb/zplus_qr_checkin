@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Báo Cáo & Thống Kê</h1>
    
    <!-- Report Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Users & Members Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Thành Viên</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng số thành viên:</span>
                    <span class="font-bold">{{ $totalUsers }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Thành viên đang hoạt động:</span>
                    <span class="font-bold text-green-600">{{ $activeMembers }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Hết hạn trong 7 ngày:</span>
                    <span class="font-bold text-orange-500">{{ $expiringSoon }}</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('reports.membership-status') }}" class="text-blue-600 hover:underline text-sm">Xem chi tiết →</a>
            </div>
        </div>
        
        <!-- Check-ins Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Check-in</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Hôm nay:</span>
                    <span class="font-bold">{{ $todayCheckIns }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tuần này:</span>
                    <span class="font-bold">{{ $weekCheckIns }}</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('reports.daily-trends') }}" class="text-blue-600 hover:underline text-sm">Xem xu hướng →</a>
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Báo Cáo Nhanh</h2>
            <div class="space-y-2">
                <a href="{{ route('reports.user-activity') }}" class="block text-blue-600 hover:underline">
                    <i class="fas fa-user-clock mr-2"></i> Hoạt động người dùng
                </a>
                <a href="{{ route('reports.membership-status') }}?status=active&expiring=7" class="block text-blue-600 hover:underline">
                    <i class="fas fa-clock mr-2"></i> Sắp hết hạn (7 ngày)
                </a>
                <a href="{{ route('reports.custom-report') }}" class="block text-blue-600 hover:underline">
                    <i class="fas fa-file-export mr-2"></i> Xuất báo cáo tùy chỉnh
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Check-ins -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Check-in Gần Đây</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left">Thành viên</th>
                        <th class="py-2 px-4 text-left">Ngày</th>
                        <th class="py-2 px-4 text-left">Giờ Check-in</th>
                        <th class="py-2 px-4 text-left">Giờ Check-out</th>
                        <th class="py-2 px-4 text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCheckIns as $checkIn)
                    <tr class="border-t">
                        <td class="py-2 px-4">{{ $checkIn->user->name }}</td>
                        <td class="py-2 px-4">{{ $checkIn->date->format('d/m/Y') }}</td>
                        <td class="py-2 px-4">{{ $checkIn->check_in_time->format('H:i') }}</td>
                        <td class="py-2 px-4">
                            {{ $checkIn->check_out_time ? $checkIn->check_out_time->format('H:i') : 'Chưa check-out' }}
                        </td>
                        <td class="py-2 px-4">
                            @if($checkIn->check_out_time)
                                <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Hoàn thành</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Đang hoạt động</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Không có dữ liệu check-in gần đây</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('check-ins.index') }}" class="text-blue-600 hover:underline text-sm">Xem tất cả →</a>
        </div>
    </div>
    
    <!-- Report Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reports.user-activity') }}" class="bg-white hover:bg-blue-50 border border-gray-200 rounded-lg p-6 text-center transition duration-150 ease-in-out">
            <i class="fas fa-users text-3xl text-blue-500 mb-3"></i>
            <h3 class="text-lg font-medium">Hoạt Động Người Dùng</h3>
            <p class="text-sm text-gray-600 mt-2">Thống kê check-in theo người dùng</p>
        </a>
        
        <a href="{{ route('reports.membership-status') }}" class="bg-white hover:bg-blue-50 border border-gray-200 rounded-lg p-6 text-center transition duration-150 ease-in-out">
            <i class="fas fa-id-card text-3xl text-blue-500 mb-3"></i>
            <h3 class="text-lg font-medium">Tình Trạng Thành Viên</h3>
            <p class="text-sm text-gray-600 mt-2">Thống kê hội viên đang hoạt động và hết hạn</p>
        </a>
        
        <a href="{{ route('reports.daily-trends') }}" class="bg-white hover:bg-blue-50 border border-gray-200 rounded-lg p-6 text-center transition duration-150 ease-in-out">
            <i class="fas fa-chart-line text-3xl text-blue-500 mb-3"></i>
            <h3 class="text-lg font-medium">Xu Hướng Hàng Ngày</h3>
            <p class="text-sm text-gray-600 mt-2">Biểu đồ thống kê check-in theo thời gian</p>
        </a>
        
        <a href="{{ route('reports.custom-report') }}" class="bg-white hover:bg-blue-50 border border-gray-200 rounded-lg p-6 text-center transition duration-150 ease-in-out">
            <i class="fas fa-file-export text-3xl text-blue-500 mb-3"></i>
            <h3 class="text-lg font-medium">Báo Cáo Tùy Chỉnh</h3>
            <p class="text-sm text-gray-600 mt-2">Tạo và tải xuống báo cáo theo nhu cầu</p>
        </a>
    </div>
</div>
@endsection