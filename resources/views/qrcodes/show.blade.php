{{-- resources/views/qrcodes/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('QR Code Thành Viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- QR Code Card -->
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <div class="flex flex-col items-center">
                                <div class="w-full mb-4 flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                    <span class="px-3 py-1 text-xs rounded-full {{ $user->hasActiveMembership() ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $user->hasActiveMembership() ? 'Hoạt động' : 'Hết hạn' }}
                                    </span>
                                </div>
                                <div class="bg-white dark:bg-gray-700 p-3 rounded-xl shadow-inner mb-4 mx-auto" id="qr-container">
                                    <img 
                                        src="{{ route('qrcode.show', ['user' => $user->id]) }}" 
                                        alt="QR Code" 
                                        class="mx-auto"
                                        id="qr-code-image"
                                        style="width: 192px; height: auto;"
                                    >
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 text-center">
                                    <p>Mã QR dùng để check-in và check-out tại câu lạc bộ</p>
                                    <p class="mt-1 font-medium select-all">{{ $user->qr_code }}</p>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-2 mb-4 w-full justify-center">
                                    @if(auth()->user()->isAdmin() || auth()->id() == $user->id)
                                        <form action="{{ route('qrcode.generate', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                {{ __('Tạo mới') }}
                                            </button>
                                        </form>

                                        <button type="button" id="print-pdf-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            {{ __('In PDF') }}
                                        </button>
                                    @endif
                                </div>

                                <!-- QR Size Control -->
                                <div class="w-full mb-4">
                                    <label for="qr-size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kích thước mã QR:</label>
                                    <input type="range" id="qr-size" min="100" max="300" value="192" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                                </div>
                                
                                <!-- How to use -->
                                <div class="w-full bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 text-sm">
                                    <h4 class="font-medium text-indigo-700 dark:text-indigo-300 mb-2">{{ __('Hướng dẫn sử dụng:') }}</h4>
                                    <ol class="list-decimal pl-5 text-gray-700 dark:text-gray-300 space-y-1">
                                        <li>{{ __('Mở mã QR khi đến câu lạc bộ') }}</li>
                                        <li>{{ __('Để nhân viên scan mã để check-in') }}</li>
                                        <li>{{ __('Khi rời đi, quét lại để check-out') }}</li>
                                        <li>{{ __('Lưu mã QR để tiện sử dụng sau này') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Member Information & Check-in History -->
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Thông tin thành viên') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Personal Information -->
                                <div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Họ và tên:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Email:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->email }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Số điện thoại:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->phone ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Địa chỉ:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->address ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Ngày sinh:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Membership Information -->
                                <div>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <div class="mb-2">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Loại thành viên:') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white ml-2">{{ ucfirst($user->member_type ?? 'Thường') }}</span>
                                        </div>
                                        
                                        @if($user->hasActiveMembership())
                                            @php
                                                $membership = $user->getActiveMembership();
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
                    
                    <!-- Recent Check-ins -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Lịch sử check-in gần đây') }}</h3>
                                <a href="{{ route('checkin.history', $user->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Xem tất cả') }}</a>
                            </div>
                            
                            @if(isset($user->checkIns) && $user->checkIns->count() > 0)
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
                                            @foreach($user->checkIns->take(5) as $checkIn)
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
                            @else
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p>{{ __('Chưa có lịch sử check-in nào') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Back Button -->
                    <div class="mt-6 flex justify-start">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('Trở về Dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Template for PDF -->
    <div id="pdf-template" style="display:none;">
        <div class="container" style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; text-align: center;">
            <div class="header" style="margin-bottom: 20px;">
                <h1 style="margin-bottom: 5px; font-size: 24px;">{{ $user->name }}</h1>
                <p style="margin: 5px 0; color: #666;">{{ $user->email }}</p>
                <p style="margin: 5px 0; color: #666;">{{ $user->phone ?? 'N/A' }}</p>
            </div>
            <div class="qr-code" style="margin: 30px 0;">
                <img src="{{ route('qrcode.show', ['user' => $user->id]) }}" alt="QR Code" style="width: 200px; height: auto;">
            </div>
            <div class="info" style="margin: 20px 0; font-size: 14px;">
                <p style="margin: 5px 0; font-weight: bold;">Mã QR: {{ $user->qr_code }}</p>
                @if($user->hasActiveMembership())
                <p style="margin: 5px 0;">Thành viên {{ ucfirst($user->member_type ?? 'Thường') }}</p>
                <p style="margin: 5px 0;">Hợp lệ đến: {{ $user->getActiveMembership()->end_date->format('d/m/Y') }}</p>
                @endif
            </div>
            <div class="footer" style="margin-top: 30px; font-size: 12px; color: #666;">
                <p style="margin: 3px 0;">Đưa mã QR này cho nhân viên để check-in và check-out</p>
                <p style="margin: 3px 0;">Được tạo vào: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Add an event listener that will execute when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            
            // QR code size slider functionality
            var qrSizeSlider = document.getElementById('qr-size');
            var qrCodeImage = document.getElementById('qr-code-image');
            
            if (qrSizeSlider && qrCodeImage) {
                console.log('QR slider elements found');
                qrSizeSlider.addEventListener('input', function() {
                    console.log('Slider value changed to: ' + this.value);
                    qrCodeImage.style.width = this.value + 'px';
                });
            } else {
                console.error('QR slider elements not found');
            }
            
            // Print PDF button functionality
            var printBtn = document.getElementById('print-pdf-btn');
            
            if (printBtn) {
                console.log('Print button found');
                printBtn.addEventListener('click', function() {
                    console.log('Print button clicked');
                    printQRCodePDF();
                });
            } else {
                console.error('Print button not found');
            }
        });
        
        // Function to print QR code as PDF
        function printQRCodePDF() {
            console.log('printQRCodePDF function called');
            
            try {
                // Get the template content
                var templateContent = document.getElementById('pdf-template').innerHTML;
                console.log('Template content retrieved');
                
                // Create a new window
                var printWindow = window.open('', '_blank');
                console.log('New window opened');
                
                if (!printWindow) {
                    alert('Pop-up blocker may be preventing the PDF from opening. Please allow pop-ups for this site.');
                    return;
                }
                
                // Write the template content to the new window
                printWindow.document.write('<!DOCTYPE html><html><head><title>QR Code - {{ $user->name }}</title><meta charset="utf-8"><style>@media print {body {margin: 0;padding: 0;}}</style></head><body>' + templateContent + '<script>window.onload = function() {setTimeout(function() {window.print();setTimeout(function() {window.close();}, 500);}, 500);};<\/script></body></html>');
                
                printWindow.document.close();
                console.log('Print window document written and closed');
            } catch (error) {
                console.error('Error in printQRCodePDF function:', error);
                alert('There was an error generating the PDF. Please try again or contact support.');
            }
        }
    </script>
</x-app-layout>