@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Báo Cáo Hoạt Động Người Dùng</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>
    
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('reports.user-activity') }}" method="get" class="md:flex md:space-x-4 space-y-4 md:space-y-0 items-end">
            <div class="flex-1">
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Khoảng thời gian</label>
                <select name="date_range" id="date_range" class="form-select rounded-md shadow-sm w-full" onchange="toggleCustomDateFields()">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="week" {{ $dateRange == 'week' ? 'selected' : '' }}>Tuần này</option>
                    <option value="month" {{ $dateRange == 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="year" {{ $dateRange == 'year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                </select>
            </div>
            
            <div id="custom_date_container" class="flex-1 flex space-x-4 {{ $dateRange != 'custom' ? 'hidden' : '' }}">
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" name="start_date" id="start_date" class="form-input rounded-md shadow-sm w-full" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="end_date" id="end_date" class="form-input rounded-md shadow-sm w-full" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                </div>
            </div>
            
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </form>
    </div>
    
    <!-- User Activity Data -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Hoạt động người dùng từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}</h2>
        </div>
        
        @if(count($userStats) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng lượt check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian trung bình</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lần gần nhất</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($userStats as $userId => $stats)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $stats['user']->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $stats['user']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $stats['total_visits'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @php
                                    $hours = floor($stats['total_duration_minutes'] / 60);
                                    $minutes = $stats['total_duration_minutes'] % 60;
                                    echo $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . ' phút';
                                @endphp
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @php
                                    $avgHours = floor($stats['avg_duration_minutes'] / 60);
                                    $avgMinutes = $stats['avg_duration_minutes'] % 60;
                                    echo $avgHours > 0 ? $avgHours . 'h ' . $avgMinutes . 'm' : $avgMinutes . ' phút';
                                @endphp
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($stats['last_visit'])->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button type="button" class="text-blue-600 hover:text-blue-900"
                                onclick="toggleUserDetails('user-details-{{ $userId }}')">
                                <i class="fas fa-chevron-down mr-1"></i> Hiển thị
                            </button>
                        </td>
                    </tr>
                    <tr id="user-details-{{ $userId }}" class="bg-gray-50 hidden">
                        <td colspan="6" class="px-6 py-4">
                            <div class="text-sm text-gray-700">
                                <h4 class="font-semibold mb-2">Chi tiết check-in của {{ $stats['user']->name }}</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($stats['checkIns'] as $checkIn)
                                            <tr class="hover:bg-gray-100">
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($checkIn->date)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    {{ $checkIn->check_in_time->format('H:i') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    {{ $checkIn->check_out_time ? $checkIn->check_out_time->format('H:i') : 'Chưa check-out' }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    @if($checkIn->check_out_time)
                                                        @php
                                                            $checkInTime = \Carbon\Carbon::parse($checkIn->check_in_time);
                                                            $checkOutTime = \Carbon\Carbon::parse($checkIn->check_out_time);
                                                            $durationMinutes = $checkInTime->diffInMinutes($checkOutTime);
                                                            $durationHours = floor($durationMinutes / 60);
                                                            $remainingMinutes = $durationMinutes % 60;
                                                            echo $durationHours > 0 ? $durationHours . 'h ' . $remainingMinutes . 'm' : $remainingMinutes . ' phút';
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-6 text-center text-gray-500">
            <p>Không có dữ liệu hoạt động người dùng trong khoảng thời gian đã chọn.</p>
        </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
    function toggleCustomDateFields() {
        const dateRange = document.getElementById('date_range').value;
        const customDateContainer = document.getElementById('custom_date_container');
        
        if (dateRange === 'custom') {
            customDateContainer.classList.remove('hidden');
        } else {
            customDateContainer.classList.add('hidden');
        }
    }
    
    function toggleUserDetails(id) {
        const details = document.getElementById(id);
        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }
    }
</script>
@endsection
@endsection