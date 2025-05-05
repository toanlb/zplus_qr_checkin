{{-- resources/views/notifications/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tạo thông báo mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('notifications.store') }}">
                        @csrf

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="message" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Nội dung thông báo') }}
                            </label>
                            <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label for="type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Loại thông báo') }}
                            </label>
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>Thông báo chung</option>
                                <option value="promotion" {{ old('type') == 'promotion' ? 'selected' : '' }}>Khuyến mãi</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Send to All or Specific User -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="send_to_all" type="checkbox" name="send_to_all" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('send_to_all') ? 'checked' : '' }}>
                                <label for="send_to_all" class="ml-2 font-medium text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('Gửi cho tất cả người dùng') }}
                                </label>
                            </div>
                            @error('send_to_all')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Specific User -->
                        <div id="user_select_container" class="mb-4" {{ old('send_to_all') ? 'style=display:none' : '' }}>
                            <label for="user_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Người nhận') }}
                            </label>
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Chọn người nhận</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3">
                                {{ __('Hủy') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Gửi thông báo') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sendToAllCheckbox = document.getElementById('send_to_all');
            const userSelectContainer = document.getElementById('user_select_container');
            
            sendToAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    userSelectContainer.style.display = 'none';
                } else {
                    userSelectContainer.style.display = 'block';
                }
            });
        });
    </script>
</x-app-layout>