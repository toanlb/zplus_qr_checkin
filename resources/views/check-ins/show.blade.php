{{-- resources/views/check-ins/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết check-in') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <a href="{{ route('check-ins.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('← Quay lại') }}
                        </a>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-medium mb-4">Thông tin người dùng</h3>
                        <p class="mb-2"><span class="font-semibold">Tên:</span> {{ $checkIn->user->name }}</p>
                        <p class="mb-2"><span class="font-semibold">Email:</span> {{ $checkIn->user->email }}</p>
                        <p class="mb-2"><span class="font-semibold">Số điện thoại:</span> {{ $checkIn->user->phone ?? 'Không có' }}</p>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-medium mb-4">Thông tin check-in</h3>
                        <p class="mb-2"><span class="font-semibold">Ngày:</span> {{ \Carbon\Carbon::parse($checkIn->date)->format('d/m/Y') }}</p>
                        <p class="mb-2"><span class="font-semibold">Giờ check-in:</span> {{ $checkIn->check_in_time }}</p>
                        <p class="mb-2">
                            <span class="font-semibold">Giờ check-out:</span> 
                            @if($checkIn->check_out_time)
                                {{ $checkIn->check_out_time }}
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Chưa check-out
                                </span>
                            @endif
                        </p>
                        @if($checkIn->check_in_time && $checkIn->check_out_time)
                            <p class="mb-2">
                                <span class="font-semibold">Thời gian ở lại:</span>
                                @php
                                    $checkInTime = \Carbon\Carbon::parse($checkIn->date . ' ' . $checkIn->check_in_time);
                                    $checkOutTime = \Carbon\Carbon::parse($checkIn->date . ' ' . $checkIn->check_out_time);
                                    $duration = $checkOutTime->diff($checkInTime);
                                    echo $duration->format('%h giờ %i phút');
                                @endphp
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('check-ins.edit', $checkIn->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                            {{ __('Chỉnh sửa') }}
                        </a>
                        <form action="{{ route('check-ins.destroy', $checkIn->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" onclick="return confirm('Bạn có chắc muốn xóa check-in này?')">
                                {{ __('Xóa') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>