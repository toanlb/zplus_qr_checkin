<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Check-in') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            <div id="alert-container">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900/20 dark:border-red-600 dark:text-red-400 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Main Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Check-in Control Panel -->
                <div class="lg:col-span-1">
                    <!-- Check-in/Check-out Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-indigo-600 dark:bg-indigo-700">
                            <h3 class="text-lg font-bold text-white">{{ __('Quét mã QR') }}</h3>
                        </div>
                        
                        <div class="p-6">
                            <!-- Scanner Area -->
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-4 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Hướng camera về phía mã QR để quét tự động') }}</p>
                                </div>
                            </div>
                            
                            <!-- QR Input Form -->
                            <form id="qr-code-form" class="mb-4">
                                <div class="mb-4">
                                    <label for="qr_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nhập mã QR hoặc ID thành viên') }}</label>
                                    <input type="text" id="qr_code" name="qr_code" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nhập mã QR hoặc ID" autofocus>
                                </div>
                            </form>
                            
                            <!-- Action Buttons -->
                            <div class="grid grid-cols-2 gap-4">
                                <button id="check-in-btn" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    <span>{{ __('Check-in') }}</span>
                                </button>
                                <button id="check-out-btn" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>{{ __('Check-out') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Hoạt động gần đây') }}</h3>
                        </div>
                        
                        <div class="p-6">
                            <div id="recent-activity" class="space-y-4 max-h-96 overflow-y-auto">
                                @php
                                    $recentActivity = \App\Models\CheckIn::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp

                                @forelse($recentActivity as $activity)
                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="h-10 w-10 rounded-full {{ $activity->check_out_time ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' }} flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    @if($activity->check_out_time)
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                    @endif
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $activity->user->name }} {{ $activity->check_out_time ? 'đã check-out' : 'đã check-in' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity->check_out_time ? $activity->check_out_time->diffForHumans() : $activity->check_in_time->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <p class="text-gray-500 dark:text-gray-400">{{ __('Chưa có hoạt động nào gần đây') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Active Check-ins and History -->
                <div class="lg:col-span-2">
                    <!-- Active Check-ins -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-yellow-500 dark:bg-yellow-600">
                            <h3 class="text-lg font-bold text-white">{{ __('Thành viên đang hoạt động hôm nay') }}</h3>
                        </div>
                        
                        <div class="p-6">
                            @php
                                $activeCheckIns = $checkIns->filter(function($checkIn) {
                                    return $checkIn->date->isToday() && !$checkIn->check_out_time;
                                });
                                $activeCount = $activeCheckIns->count();
                            @endphp
                            
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Tổng số: ') }} <span class="font-bold">{{ $activeCount }}</span> {{ __('thành viên') }}</p>
                                <span class="px-3 py-1 text-xs rounded-full {{ $activeCount > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $activeCount > 0 ? 'Đang hoạt động' : 'Không có ai' }}
                                </span>
                            </div>
                            
                            @if($activeCount > 0)
                                <div id="active-members" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($activeCheckIns as $checkIn)
                                        <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm p-4">
                                            <div class="flex items-center mb-3">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                                    {{ substr($checkIn->user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $checkIn->user->name }}</h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $checkIn->user->member_type ? ucfirst($checkIn->user->member_type) : 'Thành viên' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                                <span>{{ __('Vào lúc:') }}</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $checkIn->check_in_time->format('H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                                <span>{{ __('Thời gian:') }}</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $checkIn->check_in_time->diffForHumans(null, true) }}</span>
                                            </div>
                                            <button 
                                                class="checkout-btn w-full mt-2 py-1 px-3 bg-red-100 hover:bg-red-200 text-red-800 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded flex items-center justify-center text-sm font-medium transition-colors"
                                                data-user-id="{{ $checkIn->user_id }}" 
                                                data-user-name="{{ $checkIn->user->name }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2002000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                {{ __('Check-out') }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Chưa có thành viên nào check-in hôm nay') }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Các thành viên đã check-in sẽ xuất hiện ở đây') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Check-in History -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Lịch sử check-in') }}</h3>
                            
                            <div class="flex items-center space-x-2">
                                <select id="history-filter" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="all">{{ __('Tất cả') }}</option>
                                    <option value="today" selected>{{ __('Hôm nay') }}</option>
                                    <option value="yesterday">{{ __('Hôm qua') }}</option>
                                    <option value="week">{{ __('Tuần này') }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Thành viên') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Thời gian vào') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Thời gian ra') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Thời lượng') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Trạng thái') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($checkIns->take(10) as $checkIn)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $checkIn->user->name }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $checkIn->date->format('d/m/Y') }}</div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $checkIn->check_in_time->format('H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($checkIn->check_out_time)
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $checkIn->check_out_time->format('H:i') }}</div>
                                                    @else
                                                        <span class="text-sm text-yellow-600 dark:text-yellow-400">{{ __('Chưa check-out') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    @if($checkIn->check_out_time)
                                                        @php
                                                            $duration = $checkIn->check_in_time->diff($checkIn->check_out_time);
                                                            $hours = $duration->h;
                                                            $minutes = $duration->i;
                                                            
                                                            if ($hours > 0) {
                                                                echo $hours . 'h ' . $minutes . 'm';
                                                            } else {
                                                                echo $minutes . ' phút';
                                                            }
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($checkIn->check_out_time)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                            {{ __('Hoàn thành') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                                            {{ __('Đang hoạt động') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ __('Không có dữ liệu') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4 flex justify-center">
                                <a href="{{ route('check-ins.all-history') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Xem tất cả lịch sử') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for AJAX Requests -->
    <form id="ajax-form" class="hidden">
        @csrf
        <input type="hidden" id="form-user-id" name="user_id" value="">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrCodeInput = document.getElementById('qr_code');
            const checkInBtn = document.getElementById('check-in-btn');
            const checkOutBtn = document.getElementById('check-out-btn');
            const ajaxForm = document.getElementById('ajax-form');
            const formUserId = document.getElementById('form-user-id');
            const checkoutButtons = document.querySelectorAll('.checkout-btn');
            const alertContainer = document.getElementById('alert-container');
            
            // Set focus to the QR code input
            qrCodeInput.focus();
            
            // Check-in button click handler
            checkInBtn.addEventListener('click', function() {
                const qrCode = qrCodeInput.value.trim();
                if (!qrCode) {
                    showAlert('Vui lòng nhập mã QR hoặc ID thành viên', 'error');
                    return;
                }
                
                formUserId.value = qrCode;
                submitForm('checkin');
            });
            
            // Check-out button click handler
            checkOutBtn.addEventListener('click', function() {
                const qrCode = qrCodeInput.value.trim();
                if (!qrCode) {
                    showAlert('Vui lòng nhập mã QR hoặc ID thành viên', 'error');
                    return;
                }
                
                formUserId.value = qrCode;
                submitForm('checkout');
            });
            
            // Individual check-out buttons in active members list
            checkoutButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    
                    if (confirm(`Bạn có chắc muốn check-out cho ${userName}?`)) {
                        formUserId.value = userId;
                        submitForm('checkout');
                    }
                });
            });
            
            // Submit form via AJAX
            function submitForm(action) {
                const formData = new FormData(ajaxForm);
                const url = action === 'checkin' ? '{{ route("checkin.create") }}' : '{{ route("checkout.create") }}';
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message || (action === 'checkin' ? 'Check-in thành công!' : 'Check-out thành công!'), 'success');
                        
                        // Clear the QR input field
                        qrCodeInput.value = '';
                        qrCodeInput.focus();
                        
                        // Reload the page to update the active members list after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Có lỗi xảy ra khi xử lý yêu cầu', 'error');
                });
            }
            
            // Show alert message
            function showAlert(message, type) {
                const alertClass = type === 'success' 
                    ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400'
                    : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/20 dark:border-red-600 dark:text-red-400';
                
                const alertHtml = `
                    <div class="${alertClass} px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline">${message}</span>
                    </div>
                `;
                
                alertContainer.innerHTML = alertHtml;
                
                // Scroll to top to show the alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Clear the alert after 5 seconds
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }
            
            // QR Code Input: Submit form on Enter key
            qrCodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    checkInBtn.click();
                }
            });
        });
    </script>
</x-app-layout>