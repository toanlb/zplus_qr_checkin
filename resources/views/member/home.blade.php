<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight font-['Be_Vietnam_Pro']">
            {{ __('Trang chủ thành viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Member Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thông tin thành viên</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Họ và tên:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Email:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Số điện thoại:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->phone ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Địa chỉ:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->address ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Ngày sinh:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->birth_date ? Auth::user()->birth_date->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Information -->
                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="mb-2">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Loại thành viên:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white ml-2">{{ ucfirst(Auth::user()->member_type ?? 'Thường') }}</span>
                                </div>
                                
                                @if(Auth::user()->hasActiveMembership())
                                    @php
                                        $membership = Auth::user()->getActiveMembership();
                                        $totalDays = $membership->start_date->diffInDays($membership->end_date);
                                        $remainingDays = now()->diffInDays($membership->end_date, false);
                                        $percentage = max(0, min(100, ($remainingDays / $totalDays) * 100));
                                    @endphp
                                    
                                    <div class="mb-2">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Ngày bắt đầu:') }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $membership->start_date->format('d/m/Y') }}</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Ngày kết thúc:') }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $membership->end_date->format('d/m/Y') }}</span>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Thời hạn còn lại:') }}</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $remainingDays }} {{ __('ngày') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                                            <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <span class="text-red-600 dark:text-red-400 font-medium">{{ __('Thành viên chưa có hiệu lực hoặc đã hết hạn') }}</span>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Vui lòng liên hệ quản trị viên để gia hạn') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Code của bạn</h3>
                    <div class="flex flex-col items-center">
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow-inner mb-4 mx-auto" id="qr-container">
                            @if(Auth::user()->qr_code)
                                <img 
                                    src="{{ route('qrcode.show', ['user' => Auth::user()->id]) }}" 
                                    alt="QR Code" 
                                    class="mx-auto"
                                    id="qr-code-image"
                                    style="width: 220px; height: auto;"
                                >
                            @else
                                <div class="bg-gray-100 dark:bg-gray-600 w-52 h-52 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">QR Code chưa được tạo</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 text-center">
                            <p>Mã QR dùng để check-in và check-out tại câu lạc bộ</p>
                            @if(Auth::user()->qr_code)
                                <p class="mt-1 font-medium select-all">{{ Auth::user()->qr_code }}</p>
                            @endif
                        </div>
                        
                        <a href="{{ route('qrcode.view', Auth::user()->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Xem chi tiết QR Code') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Check-ins -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lịch sử check-in gần đây</h3>
                    
                    @php
                        $recentCheckIns = Auth::user()->checkIns()->orderBy('date', 'desc')->orderBy('check_in_time', 'desc')->take(5)->get();
                    @endphp
                    
                    @if($recentCheckIns->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Ngày') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Giờ check-in') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Giờ check-out') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Thời gian') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentCheckIns as $checkIn)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $checkIn->date->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $checkIn->check_in_time->format('H:i:s') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($checkIn->check_out_time)
                                                    <span class="text-gray-900 dark:text-white">{{ $checkIn->check_out_time->format('H:i:s') }}</span>
                                                @else
                                                    <span class="text-blue-600 dark:text-blue-400">{{ __('Chưa check-out') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @if($checkIn->check_out_time)
                                                    @php
                                                        $duration = $checkIn->check_in_time->diff($checkIn->check_out_time);
                                                        $hours = $duration->h;
                                                        $minutes = $duration->i;
                                                    @endphp
                                                    {{ $hours }} {{ __('giờ') }} {{ $minutes }} {{ __('phút') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('profile.show') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ __('Xem tất cả') }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>{{ __('Bạn chưa có lịch sử check-in nào') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thông báo gần đây</h3>
                    
                    @php
                        // Lấy thông báo riêng của người dùng và các thông báo chung
                        $notifications = \App\Models\Notification::where(function($query) {
                                // Thông báo riêng của người dùng
                                $query->where('user_id', Auth::id());
                            })
                            ->orWhere(function($query) {
                                // Thông báo dạng announcement và promotion cho tất cả người dùng
                                $query->whereNull('user_id')
                                    ->whereIn('type', ['announcement', 'promotion']);
                            })
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="bg-indigo-50 dark:bg-indigo-900/20 border dark:border-indigo-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-indigo-700 dark:text-indigo-300 font-medium">
                                                {{ ucfirst($notification->type) }}
                                            </p>
                                            <p class="mt-1 text-sm text-indigo-600 dark:text-indigo-400">
                                                {{ Str::limit($notification->message, 100) }}
                                            </p>
                                            <p class="mt-2 text-xs text-indigo-500 dark:text-indigo-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-2 text-right">
                                <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('Xem tất cả thông báo') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p>{{ __('Bạn chưa có thông báo nào') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>