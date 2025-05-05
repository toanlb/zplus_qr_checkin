@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Báo Cáo Xu Hướng Check-in</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>
    
    <!-- Period Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('reports.daily-trends') }}" method="get" class="md:flex md:space-x-4 space-y-4 md:space-y-0 items-end">
            <div class="flex-1">
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Khoảng thời gian</label>
                <select name="period" id="period" class="form-select rounded-md shadow-sm w-full">
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>7 ngày gần đây</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>30 ngày gần đây</option>
                    <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>3 tháng gần đây</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>1 năm gần đây</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </form>
    </div>
    
    <!-- Check-in Trends Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Biểu đồ check-in theo ngày</h2>
        
        @if(count($dates) > 0)
            <div class="mb-8">
                <canvas id="dailyCheckInsChart" width="400" height="200"></canvas>
            </div>
        @else
            <div class="text-center text-gray-500 p-6">
                <p>Không có dữ liệu check-in trong khoảng thời gian đã chọn.</p>
            </div>
        @endif
    </div>
    
    <!-- Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Busiest Day -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Ngày bận rộn nhất</h2>
            
            @if($dayOfWeekName)
                <div class="flex items-center">
                    <div class="text-5xl text-blue-600 mr-4">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $dayOfWeekName }}</p>
                        <p class="text-gray-600">là ngày check-in nhiều nhất trong tuần</p>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500">
                    <p>Không đủ dữ liệu để xác định.</p>
                </div>
            @endif
        </div>
        
        <!-- Busiest Hour -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Giờ bận rộn nhất</h2>
            
            @if($busiestHour)
                <div class="flex items-center">
                    <div class="text-5xl text-blue-600 mr-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $busiestHour->hour }}:00</p>
                        <p class="text-gray-600">là giờ check-in nhiều nhất trong ngày</p>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500">
                    <p>Không đủ dữ liệu để xác định.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Daily Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Dữ liệu chi tiết theo ngày</h2>
        </div>
        
        @if(count($dailyCounts) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng số check-in</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dailyCounts as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->date)->locale('vi')->dayName }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->count }}</div>
                                        <div class="ml-2 flex-grow h-2 max-w-xs bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ ($record->count / max($dailyCounts->pluck('count')->toArray())) * 100 }}%"></div>
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
                <p>Không có dữ liệu check-in trong khoảng thời gian đã chọn.</p>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(count($dates) > 0)
            const ctx = document.getElementById('dailyCheckInsChart').getContext('2d');
            
            const checkInChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($dates),
                    datasets: [{
                        label: 'Số lượt check-in',
                        data: @json($counts),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' lượt';
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endsection
@endsection