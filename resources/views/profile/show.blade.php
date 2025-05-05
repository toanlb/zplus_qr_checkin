<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thông tin cá nhân') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- QR Code của thành viên -->
            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center justify-between">
                        {{ __('QR Code Thành Viên') }}
                        
                        <span class="px-3 py-1 text-xs rounded-full {{ $user->hasActiveMembership() ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                            {{ $user->hasActiveMembership() ? 'Hoạt động' : 'Hết hạn' }}
                        </span>
                    </h3>
                    
                    <div class="flex flex-col items-center">
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow-inner mb-4 mx-auto" id="qr-container">
                            @if($user->qr_code)
                                <img 
                                    src="{{ route('qrcode.show', ['user' => $user->id]) }}" 
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
                        
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 text-center">
                            <p>Mã QR dùng để check-in và check-out tại câu lạc bộ</p>
                            @if($user->qr_code)
                                <p class="mt-1 font-medium select-all">{{ $user->qr_code }}</p>
                            @endif
                        </div>
                        
                        <!-- QR Size Control -->
                        <div class="w-full max-w-xs mb-4">
                            <label for="qr-size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kích thước mã QR:</label>
                            <input type="range" id="qr-size" min="100" max="300" value="220" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                        </div>
                        
                        <!-- How to use -->
                        <div class="w-full max-w-md bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 text-sm">
                            <h4 class="font-medium text-indigo-700 dark:text-indigo-300 mb-2">{{ __('Hướng dẫn sử dụng:') }}</h4>
                            <ol class="list-decimal pl-5 text-gray-700 dark:text-gray-300 space-y-1">
                                <li>{{ __('Mở mã QR khi đến câu lạc bộ') }}</li>
                                <li>{{ __('Đưa cho nhân viên hoặc quét tại máy quét để check-in') }}</li>
                                <li>{{ __('Quét lại khi rời đi để check-out') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin cá nhân -->
            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Thông tin người dùng') }}</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Họ tên:') }}</p>
                            <p class="text-base font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Email:') }}</p>
                            <p class="text-base text-gray-800 dark:text-gray-200">{{ $user->email }}</p>
                        </div>
                        
                        @if($user->phone)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Số điện thoại:') }}</p>
                            <p class="text-base text-gray-800 dark:text-gray-200">{{ $user->phone }}</p>
                        </div>
                        @endif
                        
                        @if($user->birth_date)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Ngày sinh:') }}</p>
                            <p class="text-base text-gray-800 dark:text-gray-200">{{ $user->birth_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        @if($user->address)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Địa chỉ:') }}</p>
                            <p class="text-base text-gray-800 dark:text-gray-200">{{ $user->address }}</p>
                        </div>
                        @endif
                        
                        @if($user->member_type)
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Loại thành viên:') }}</p>
                            <p class="text-base text-gray-800 dark:text-gray-200">
                                @if($user->member_type == 'regular')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Thường</span>
                                @elseif($user->member_type == 'premium')
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-300">Premium</span>
                                @elseif($user->member_type == 'vip')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">VIP</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Chỉnh sửa thông tin') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thông tin gói thành viên -->
            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Thông tin gói thành viên') }}</h3>
                    
                    @if($activeMembership)
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 dark:bg-green-900/20 dark:border-green-600">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800 dark:text-green-200">
                                        {{ __('Bạn đang có gói thành viên hoạt động!') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Ngày bắt đầu:') }}</p>
                                <p class="text-base text-gray-800 dark:text-gray-200">{{ $activeMembership->start_date->format('d/m/Y') }}</p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Ngày kết thúc:') }}</p>
                                <p class="text-base text-gray-800 dark:text-gray-200">{{ $activeMembership->end_date->format('d/m/Y') }}</p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Số ngày còn lại:') }}</p>
                                <p class="text-base text-gray-800 dark:text-gray-200">{{ now()->diffInDays($activeMembership->end_date) }} ngày</p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Trạng thái:') }}</p>
                                <p class="text-base text-gray-800 dark:text-gray-200">
                                    @if($activeMembership->status == 'active')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Hoạt động</span>
                                    @elseif($activeMembership->status == 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">Đang chờ</span>
                                    @elseif($activeMembership->status == 'expired')
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Hết hạn</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20 dark:border-yellow-600">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        {{ __('Bạn chưa có gói thành viên hoạt động. Vui lòng liên hệ với quản trị viên để đăng ký.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lịch sử check-in/check-out -->
            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Lịch sử Check-in/Check-out') }}</h3>
                    
                    @if(count($checkIns) > 0)
                        <div class="overflow-x-auto -mx-4 sm:-mx-6">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Ngày') }}
                                            </th>
                                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Giờ Check-in') }}
                                            </th>
                                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Giờ Check-out') }}
                                            </th>
                                            <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Thời gian') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($checkIns as $checkIn)
                                            <tr>
                                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $checkIn->date->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $checkIn->check_in_time->format('H:i:s') }}
                                                </td>
                                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    @if($checkIn->check_out_time)
                                                        {{ $checkIn->check_out_time->format('H:i:s') }}
                                                    @else
                                                        <span class="text-yellow-600 dark:text-yellow-400">{{ __('Chưa check-out') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                    @if($checkIn->check_out_time)
                                                        {{ $checkIn->check_out_time->diffInHours($checkIn->check_in_time) }} giờ 
                                                        {{ $checkIn->check_out_time->diffInMinutes($checkIn->check_in_time) % 60 }} phút
                                                    @else
                                                        <span class="text-yellow-600 dark:text-yellow-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            {{ $checkIns->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4 text-center">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('Bạn chưa có lịch sử check-in nào.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Script để xử lý thay đổi kích thước QR code -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrSizeSlider = document.getElementById('qr-size');
            const qrCodeImage = document.getElementById('qr-code-image');
            
            if (qrSizeSlider && qrCodeImage) {
                qrSizeSlider.addEventListener('input', function() {
                    qrCodeImage.style.width = this.value + 'px';
                });
            }
        });
    </script>
</x-app-layout>