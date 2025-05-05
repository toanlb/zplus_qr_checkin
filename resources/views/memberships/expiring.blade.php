{{-- resources/views/memberships/expiring.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thành viên sắp hết hạn') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <a href="{{ route('memberships.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('← Quay lại danh sách') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left">Thành viên</th>
                                    <th class="px-4 py-2 text-left">Số tiền</th>
                                    <th class="px-4 py-2 text-left">Ngày bắt đầu</th>
                                    <th class="px-4 py-2 text-left">Ngày kết thúc</th>
                                    <th class="px-4 py-2 text-left">Ngày còn lại</th>
                                    <th class="px-4 py-2 text-left">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringSoon as $membership)
                                    <tr>
                                        <td class="border px-4 py-2">
                                            <div>
                                                <p class="font-medium">{{ $membership->user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $membership->user->email }}</p>
                                            </div>
                                        </td>
                                        <td class="border px-4 py-2">{{ number_format($membership->amount, 0, ',', '.') }} VNĐ</td>
                                        <td class="border px-4 py-2">{{ $membership->start_date->format('d/m/Y') }}</td>
                                        <td class="border px-4 py-2">{{ $membership->end_date->format('d/m/Y') }}</td>
                                        <td class="border px-4 py-2">
                                            @php
                                                $daysLeft = now()->diffInDays($membership->end_date);
                                            @endphp
                                            <span class="px-2 py-1 
                                                @if($daysLeft <= 3) bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 
                                                @elseif($daysLeft <= 5) bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400
                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 
                                                @endif
                                                rounded-full text-xs">
                                                {{ $daysLeft }} ngày
                                            </span>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('memberships.show', $membership->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline" title="Xem">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="openRenewModal({{ $membership->id }})" class="text-green-600 dark:text-green-400 hover:underline" title="Gia hạn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="border px-4 py-2 text-center text-gray-500 dark:text-gray-400">Không có thành viên nào sắp hết hạn</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $expiringSoon->links() }}
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
                <form id="renew-form">
                    @csrf
                    <input type="hidden" id="membership_id" name="membership_id" value="">
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
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Form submission with jQuery AJAX
            $('#renew-form').on('submit', function(e) {
                e.preventDefault();
                
                const membershipId = $('#membership_id').val();
                const amount = $('#amount').val();
                const durationMonths = $('#duration_months').val();
                
                $.ajax({
                    url: `/memberships/renew/${membershipId}`,
                    type: 'POST',
                    data: {
                        amount: amount,
                        duration_months: durationMonths,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        // Reload the page on success to show updated data
                        window.location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error renewing membership:', xhr.responseText);
                        alert('Đã xảy ra lỗi khi gia hạn thành viên. Vui lòng thử lại.');
                    }
                });
            });
        });

        function openRenewModal(membershipId) {
            // Set the membership ID in the hidden field
            $('#membership_id').val(membershipId);
            
            // Show the modal
            const modal = document.getElementById('renew-modal');
            modal.classList.remove('hidden');
        }
        
        function closeRenewModal() {
            const modal = document.getElementById('renew-modal');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>