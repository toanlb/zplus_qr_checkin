{{-- resources/views/memberships/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết thành viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">{{ $membership->user->name }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-md font-medium mb-2">Thông tin thành viên</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p><strong>Email:</strong> {{ $membership->user->email }}</p>
                                    <p><strong>Số điện thoại:</strong> {{ $membership->user->phone }}</p>
                                    <p><strong>Ngày sinh:</strong> {{ $membership->user->birth_date->format('d/m/Y') }}</p>
                                    <p><strong>Địa chỉ:</strong> {{ $membership->user->address }}</p>
                                    <p><strong>Loại thành viên:</strong> {{ ucfirst($membership->user->member_type) }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-md font-medium mb-2">Thông tin đăng ký</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p><strong>Số tiền:</strong> {{ number_format($membership->amount, 0, ',', '.') }} VNĐ</p>
                                    <p><strong>Ngày bắt đầu:</strong> {{ $membership->start_date->format('d/m/Y') }}</p>
                                    <p><strong>Ngày kết thúc:</strong> {{ $membership->end_date->format('d/m/Y') }}</p>
                                    <p>
                                        <strong>Trạng thái:</strong> 
                                        @if($membership->status == 'active')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 rounded-full text-xs">Đang hoạt động</span>
                                        @elseif($membership->status == 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 rounded-full text-xs">Chờ xử lý</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 rounded-full text-xs">Đã hết hạn</span>
                                        @endif
                                    </p>
                                    <p><strong>Ngày đăng ký:</strong> {{ $membership->created_at->format('d/m/Y H:i:s') }}</p>
                                    <p><strong>Cập nhật lần cuối:</strong> {{ $membership->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="text-md font-medium mb-2">Check-in gần đây</h4>
                        @if($membership->user->checkIns->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400">Không có lịch sử check-in nào.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Ngày</th>
                                            <th class="px-4 py-2 text-left">Thời gian check-in</th>
                                            <th class="px-4 py-2 text-left">Thời gian check-out</th>
                                            <th class="px-4 py-2 text-left">Thời gian tập luyện</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($membership->user->checkIns->take(5) as $checkIn)
                                            <tr>
                                                <td class="border px-4 py-2">{{ $checkIn->date->format('d/m/Y') }}</td>
                                                <td class="border px-4 py-2">{{ $checkIn->check_in_time->format('H:i:s') }}</td>
                                                <td class="border px-4 py-2">
                                                    @if($checkIn->check_out_time)
                                                        {{ $checkIn->check_out_time->format('H:i:s') }}
                                                    @else
                                                        <span class="text-blue-600 dark:text-blue-400">Chưa check-out</span>
                                                    @endif
                                                </td>
                                                <td class="border px-4 py-2">
                                                    @if($checkIn->check_out_time)
                                                        @php
                                                            $duration = $checkIn->check_in_time->diff($checkIn->check_out_time);
                                                            $hours = $duration->h;
                                                            $minutes = $duration->i;
                                                        @endphp
                                                        {{ $hours }} giờ {{ $minutes }} phút
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('checkin.history', $membership->user_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Xem tất cả</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            <a href="{{ route('memberships.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Trở về danh sách') }}
                            </a>
                            <a href="{{ route('qrcode.view', $membership->user_id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ml-2">
                                {{ __('Xem QR Code') }}
                            </a>
                        </div>
                        
                        <div>
                            <a href="{{ route('memberships.edit', $membership->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Chỉnh sửa') }}
                            </a>
                            
                            @if($membership->status == 'expired' || $membership->status == 'pending')
                                <button type="button" onclick="openRenewModal({{ $membership->id }})" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ml-2">
                                    {{ __('Gia hạn') }}
                                </button>
                            @endif
                            
                            <form action="{{ route('memberships.destroy', $membership->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thành viên này?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ml-2">
                                    {{ __('Xóa') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Renew Membership Modal -->
    <div id="renew-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 dark:text-white">Gia hạn thành viên</h3>
                <form id="renew-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tiền</label>
                        <input type="number" name="amount" id="amount" class="rounded-md w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="duration_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thời hạn (tháng)</label>
                        <select name="duration_months" id="duration_months" class="rounded-md w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                            <option value="1">1 tháng</option>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="12">12 tháng</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" onclick="closeRenewModal()" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Hủy
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Gia hạn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRenewModal(membershipId) {
            const modal = document.getElementById('renew-modal');
            const form = document.getElementById('renew-form');
            form.action = `/memberships/renew/${membershipId}`;
            modal.classList.remove('hidden');
        }
        
        function closeRenewModal() {
            const modal = document.getElementById('renew-modal');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>