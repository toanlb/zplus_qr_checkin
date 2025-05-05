<x-guest-layout>
    <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        {{ __('Đăng nhập vào tài khoản của bạn') }}
    </h2>
    
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">
                {{ __('Email') }}
            </label>
            <div class="mt-2">
                <div class="flex items-center">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-800 dark:border-gray-700">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                            <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                        </svg>
                    </span>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
                        class="block w-full min-h-[40px] rounded-none rounded-r-md border border-gray-300 bg-white py-2 px-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:border-gray-700 sm:text-sm" 
                        placeholder="example@email.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">
                    {{ __('Mật khẩu') }}
                </label>
                @if (Route::has('password.request'))
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        {{ __('Quên mật khẩu?') }}
                    </a>
                </div>
                @endif
            </div>
            <div class="mt-2">
                <div class="flex items-center">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-800 dark:border-gray-700">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="block w-full min-h-[40px] rounded-none rounded-r-md border border-gray-300 bg-white py-2 px-3 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:border-gray-700 sm:text-sm" 
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" name="remember" type="checkbox" 
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:focus:ring-indigo-500">
            <label for="remember_me" class="ml-2 block text-sm leading-6 text-gray-900 dark:text-gray-300">
                {{ __('Ghi nhớ đăng nhập') }}
            </label>
        </div>

        <div>
            <button type="submit" 
                class="flex w-full justify-center items-center rounded-md bg-indigo-600 px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                {{ __('Đăng nhập') }}
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Chưa có tài khoản?') }}
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                {{ __('Đăng ký ngay') }}
            </a>
        </p>
    </div>
</x-guest-layout>
