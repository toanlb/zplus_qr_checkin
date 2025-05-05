{{-- resources/views/notifications/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết thông báo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('← Quay lại') }}
                            </a>
                        @else
                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('← Quay lại') }}
                            </a>
                        @endif
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <p class="text-sm mb-1">
                            <span class="font-semibold">Loại thông báo:</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->type == 'announcement' ? 'bg-blue-100 text-blue-800' : ($notification->type == 'promotion' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $notification->type == 'announcement' ? 'Thông báo chung' : ($notification->type == 'promotion' ? 'Khuyến mãi' : 'Gia hạn thành viên') }}
                            </span>
                        </p>
                        <p class="text-sm mb-1">
                            <span class="font-semibold">Thời gian:</span> {{ $notification->created_at->format('d/m/Y H:i:s') }}
                        </p>
                        <p class="text-sm mb-1">
                            <span class="font-semibold">Trạng thái:</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->read ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $notification->read ? 'Đã đọc' : 'Chưa đọc' }}
                            </span>
                        </p>
                        @if(auth()->user()->isAdmin())
                        <p class="text-sm mb-1">
                            <span class="font-semibold">Người nhận:</span> {{ $notification->user->name }} ({{ $notification->user->email }})
                        </p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-medium mb-2">Nội dung thông báo</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $notification->message }}</p>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        @if(!$notification->read)
                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="mr-2">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Đánh dấu đã đọc') }}
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('notifications.edit', $notification->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                                {{ __('Chỉnh sửa') }}
                            </a>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" onclick="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                {{ __('Xóa') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>