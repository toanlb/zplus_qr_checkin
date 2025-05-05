<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tạo QR Code') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Tạo mã QR cho thành viên') }}</h3>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left">{{ __('Tên') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Email') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Loại thành viên') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $user->name }}</td>
                                        <td class="border px-4 py-2">{{ $user->email }}</td>
                                        <td class="border px-4 py-2">{{ ucfirst($user->member_type) }}</td>
                                        <td class="border px-4 py-2">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('qrcode.view', $user->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank">
                                                    {{ __('Xem QR') }}
                                                </a>
                                                <button 
                                                    onclick="generateQrCode({{ $user->id }})" 
                                                    class="text-green-600 dark:text-green-400 hover:underline"
                                                    id="generate-btn-{{ $user->id }}"
                                                >
                                                    {{ __('Tạo mới') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="border px-4 py-2 text-center text-gray-500 dark:text-gray-400">{{ __('Không có dữ liệu') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateQrCode(userId) {
            const button = document.getElementById(`generate-btn-${userId}`);
            button.disabled = true;
            button.innerText = '{{ __('Đang xử lý...') }}';
            
            fetch(`/qrcode/generate/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ __('Tạo mã QR thành công!') }}');
                    button.innerText = '{{ __('Tạo mới') }}';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('Có lỗi xảy ra!') }}');
                button.innerText = '{{ __('Tạo mới') }}';
                button.disabled = false;
            });
        }
    </script>
</x-app-layout>