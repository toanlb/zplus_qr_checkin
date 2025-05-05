<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thêm thành viên mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Hiển thị lỗi validation -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('members.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Thông tin cá nhân -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    {{ __('Thông tin cá nhân') }}
                                </h3>

                                <!-- Họ tên -->
                                <div class="mb-4">
                                    <x-label for="name" :value="__('Họ tên')" required />
                                    <x-input id="name" name="name" type="text" :value="old('name')" 
                                        placeholder="Nhập họ tên đầy đủ" required autofocus
                                        icon="fas fa-user" class="w-full" />
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <x-label for="email" :value="__('Email')" required />
                                    <x-input id="email" name="email" type="email" :value="old('email')" 
                                        placeholder="example@email.com" required
                                        icon="fas fa-envelope" class="w-full" />
                                </div>

                                <!-- Số điện thoại -->
                                <div class="mb-4">
                                    <x-label for="phone" :value="__('Số điện thoại')" required />
                                    <x-input id="phone" name="phone" type="text" :value="old('phone')" 
                                        placeholder="Nhập số điện thoại" required
                                        icon="fas fa-phone" class="w-full" />
                                </div>

                                <!-- Địa chỉ -->
                                <div class="mb-4">
                                    <x-label for="address" :value="__('Địa chỉ')" />
                                    <x-input id="address" name="address" type="text" :value="old('address')" 
                                        placeholder="Nhập địa chỉ" 
                                        icon="fas fa-map-marker-alt" class="w-full" />
                                </div>

                                <!-- Ngày sinh -->
                                <div class="mb-4">
                                    <x-label for="birth_date" :value="__('Ngày sinh')" />
                                    <x-input id="birth_date" name="birth_date" type="date" :value="old('birth_date')" 
                                        class="w-full" />
                                </div>
                            </div>

                            <!-- Thông tin thành viên và bảo mật -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    {{ __('Thông tin thành viên và bảo mật') }}
                                </h3>

                                <!-- Loại thành viên -->
                                <div class="mb-4">
                                    <x-label for="member_type" :value="__('Loại thành viên')" required />
                                    <select id="member_type" name="member_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="">-- Chọn loại thành viên --</option>
                                        <option value="regular" {{ old('member_type') == 'regular' ? 'selected' : '' }}>Thường</option>
                                        <option value="premium" {{ old('member_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                                        <option value="vip" {{ old('member_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                                    </select>
                                </div>

                                <!-- Vai trò -->
                                <div class="mb-4">
                                    <x-label for="role_id" :value="__('Vai trò')" />
                                    <select id="role_id" name="role_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="">-- Chọn vai trò --</option>
                                        @foreach($roles ?? [] as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Mật khẩu -->
                                <div class="mb-4">
                                    <x-label for="password" :value="__('Mật khẩu')" required />
                                    <x-input id="password" name="password" type="password" 
                                        placeholder="Nhập mật khẩu" required
                                        icon="fas fa-lock" class="w-full" />
                                </div>

                                <!-- Xác nhận mật khẩu -->
                                <div class="mb-4">
                                    <x-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" required />
                                    <x-input id="password_confirmation" name="password_confirmation" type="password" 
                                        placeholder="Nhập lại mật khẩu" required
                                        icon="fas fa-lock" class="w-full" />
                                </div>

                                <!-- Ghi chú bổ sung -->
                                <div class="mb-4">
                                    <x-label for="notes" :value="__('Ghi chú')" />
                                    <textarea id="notes" name="notes" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        placeholder="Nhập ghi chú bổ sung nếu có">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Hủy') }}
                            </a>
                            <x-button type="submit" variant="success" icon="fas fa-user-plus">
                                {{ __('Thêm thành viên') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>