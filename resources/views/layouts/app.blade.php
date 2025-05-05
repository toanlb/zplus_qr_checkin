<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QR Check-in') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|be-vietnam-pro:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback CSS for when Vite is not running -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <!-- Additional styles to support dashboard custom classes -->
            <style>
                /* Font family support */
                .font-\[\'Be_Vietnam_Pro\'\] {
                    font-family: 'Be Vietnam Pro', sans-serif;
                }
                
                /* Custom rounded corners */
                .sm\:rounded-xl {
                    border-radius: 0.75rem;
                }
                
                /* Custom opacity settings */
                .dark\:bg-indigo-900\/30 {
                    background-color: rgba(49, 46, 129, 0.3);
                }
                .dark\:bg-red-900\/20 {
                    background-color: rgba(127, 29, 29, 0.2);
                }
                .dark\:bg-orange-900\/20 {
                    background-color: rgba(124, 45, 18, 0.2);
                }
                .dark\:bg-yellow-900\/20 {
                    background-color: rgba(113, 63, 18, 0.2);
                }
                
                /* Hover effects */
                .dark\:hover\:bg-indigo-900\/50:hover {
                    background-color: rgba(49, 46, 129, 0.5);
                }
                
                /* Support for dark mode */
                @media (prefers-color-scheme: dark) {
                    .dark\:bg-gray-800 {
                        background-color: #1f2937;
                    }
                    .dark\:bg-gray-900 {
                        background-color: #111827;
                    }
                    .dark\:text-gray-200 {
                        color: #e5e7eb;
                    }
                    .dark\:text-gray-100 {
                        color: #f3f4f6;
                    }
                    .dark\:text-gray-300 {
                        color: #d1d5db;
                    }
                    .dark\:text-gray-400 {
                        color: #9ca3af;
                    }
                    .dark\:text-indigo-400 {
                        color: #818cf8;
                    }
                    .dark\:text-green-200 {
                        color: #bbf7d0;
                    }
                    .dark\:text-red-200 {
                        color: #fecaca;
                    }
                    .dark\:text-indigo-400 {
                        color: #818cf8;
                    }
                    .dark\:text-purple-400 {
                        color: #c084fc;
                    }
                    .dark\:text-yellow-400 {
                        color: #facc15;
                    }
                    .dark\:text-green-400 {
                        color: #4ade80;
                    }
                    .dark\:text-red-400 {
                        color: #f87171;
                    }
                    .dark\:text-orange-400 {
                        color: #fb923c;
                    }
                    .dark\:bg-indigo-900 {
                        background-color: #312e81;
                    }
                    .dark\:bg-purple-900 {
                        background-color: #581c87;
                    }
                    .dark\:bg-yellow-900 {
                        background-color: #713f12;
                    }
                    .dark\:bg-green-900 {
                        background-color: #14532d;
                    }
                    .dark\:bg-red-900 {
                        background-color: #7f1d1d;
                    }
                    .dark\:bg-orange-900 {
                        background-color: #7c2d12;
                    }
                    .dark\:divide-gray-700 > :not([hidden]) ~ :not([hidden]) {
                        border-color: #374151;
                    }
                    .dark\:hover\:text-indigo-300:hover {
                        color: #a5b4fc;
                    }
                }
                
                /* Grid system */
                @media (min-width: 1024px) {
                    .lg\:grid-cols-3 {
                        grid-template-columns: repeat(3, minmax(0, 1fr));
                    }
                    .lg\:col-span-2 {
                        grid-column: span 2 / span 2;
                    }
                }
                @media (min-width: 768px) {
                    .md\:grid-cols-2 {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }
                .grid-cols-1 {
                    grid-template-columns: repeat(1, minmax(0, 1fr));
                }
                .grid {
                    display: grid;
                }
                .gap-6 {
                    gap: 1.5rem;
                }
                .gap-8 {
                    gap: 2rem;
                }
                .space-y-3 > :not([hidden]) ~ :not([hidden]) {
                    margin-top: 0.75rem;
                }
                .space-y-4 > :not([hidden]) ~ :not([hidden]) {
                    margin-top: 1rem;
                }
                .space-y-6 > :not([hidden]) ~ :not([hidden]) {
                    margin-top: 1.5rem;
                }
            </style>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex items-center">
                            <!-- Home Icon - Link dựa trên vai trò -->
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('dashboard') }}" class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('member.home') }}" class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </a>
                            @endif
                            {{ $header }}
                        </div>
                        
                        <!-- Admin Profile (Moved from Navigation) -->
                        <div class="flex items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    @if(auth()->user()->isAdmin())
                                        <x-dropdown-link :href="route('dashboard')">
                                            {{ __('Bảng điều khiển') }}
                                        </x-dropdown-link>
                                    @else
                                        <x-dropdown-link :href="route('member.home')">
                                            {{ __('Trang chủ') }}
                                        </x-dropdown-link>
                                    @endif

                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Hồ sơ') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Đăng xuất') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </body>
</html>
