@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Báo Cáo Tình Trạng Thành Viên</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Tổng thành viên</p>
            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Đang hoạt động</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Đã hết hạn</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Hết hạn trong 7 ngày</p>
            <p class="text-2xl font-bold text-orange-500">{{ $stats['expiring7days'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Hết hạn trong 30 ngày</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $stats['expiring30days'] }}</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('reports.membership-status') }}" method="get" class="md:flex md:space-x-4 space-y-4 md:space-y-0 items-end">
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status" id="status" class="form-select rounded-md shadow-sm w-full" onchange="toggleExpiringField()">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                </select>
            </div>
            
            <div id="expiring_container" class="flex-1 {{ $status != 'active' ? 'hidden' : '' }}">
                <label for="expiring" class="block text-sm font-medium text-gray-700 mb-1">Sắp hết hạn trong (ngày)</label>
                <select name="expiring" id="expiring" class="form-select rounded-md shadow-sm w-full">
                    <option value="">Tất cả</option>
                    <option value="7" {{ request('expiring') == '7' ? 'selected' : '' }}>7 ngày</option>
                    <option value="14" {{ request('expiring') == '14' ? 'selected' : '' }}>14 ngày</option>
                    <option value="30" {{ request('expiring') == '30' ? 'selected' : '' }}>30 ngày</option>
                    <option value="60" {{ request('expiring') == '60' ? 'selected' : '' }}>60 ngày</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </form>
    </div>
    
    <!-- Membership Data -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">
                @if($status == 'all')
                    Tất cả thành viên
                @elseif($status == 'active')
                    Thành viên đang hoạt động
                    @if(request('expiring'))
                        (hết hạn trong {{ request('expiring') }} ngày)
                    @endif
                @elseif($status == 'expired')
                    Thành viên đã hết hạn
                @endif
            </h2>
        </div>
        
        @if($memberships->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại gói</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày kết thúc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian còn lại</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($memberships as $membership)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $membership->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $membership->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $membership->membership_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($membership->start_date)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($membership->end_date)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($membership->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đang hoạt động
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Đã hết hạn
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $now = \Carbon\Carbon::now();
                                $endDate = \Carbon\Carbon::parse($membership->end_date);
                                
                                if($membership->status == 'expired') {
                                    $daysText = 'Đã hết hạn';
                                } elseif($now->greaterThan($endDate)) {
                                    $daysText = 'Đã hết hạn';
                                } else {
                                    $daysRemaining = $now->diffInDays($endDate);
                                    
                                    if($daysRemaining <= 7) {
                                        $textColor = 'text-red-600';
                                    } elseif($daysRemaining <= 30) {
                                        $textColor = 'text-orange-500';
                                    } else {
                                        $textColor = 'text-green-600';
                                    }
                                    
                                    $daysText = $daysRemaining . ' ngày';
                                }
                            @endphp
                            
                            <div class="text-sm {{ isset($textColor) ? $textColor : '' }}">
                                {{ $daysText }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t">
            {{ $memberships->appends(request()->query())->links() }}
        </div>
        @else
        <div class="p-6 text-center text-gray-500">
            <p>Không có dữ liệu thành viên phù hợp với bộ lọc.</p>
        </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
    function toggleExpiringField() {
        const status = document.getElementById('status').value;
        const expiringContainer = document.getElementById('expiring_container');
        
        if (status === 'active') {
            expiringContainer.classList.remove('hidden');
        } else {
            expiringContainer.classList.add('hidden');
        }
    }
</script>
@endsection
@endsection