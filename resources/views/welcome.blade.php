<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>QR Check-in - Hệ Thống Quản Lý Check-in Thông Minh</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|be-vietnam-pro:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback CSS for when Vite is not running -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <!-- Additional styles to support custom classes -->
            <style>
                @keyframes blob {
                    0%, 100% { transform: translate(0, 0) scale(1); }
                    25% { transform: translate(20px, -30px) scale(1.1); }
                    50% { transform: translate(-20px, 20px) scale(0.9); }
                    75% { transform: translate(20px, 30px) scale(1.05); }
                }
                .animate-blob {
                    animation: blob 12s ease-in-out infinite;
                }
                .animation-delay-2000 {
                    animation-delay: 2s;
                }
                
                /* Custom utilities for blur and backdrop */
                .backdrop-blur-sm {
                    backdrop-filter: blur(8px);
                }
                .bg-white\/30 {
                    background-color: rgba(255, 255, 255, 0.3);
                }
                .dark .dark\:bg-gray-800\/30 {
                    background-color: rgba(31, 41, 55, 0.3);
                }
                .filter {
                    filter: var(--tw-filter);
                }
                .blur-3xl {
                    --tw-blur: blur(64px);
                    filter: var(--tw-blur);
                }
                .mix-blend-multiply {
                    mix-blend-mode: multiply;
                }
                .dark .dark\:mix-blend-soft-light {
                    mix-blend-mode: soft-light;
                }
                
                /* Font family support */
                .font-\[\'Be_Vietnam_Pro\'\] {
                    font-family: 'Be Vietnam Pro', sans-serif;
                }
                
                /* Dark mode support */
                .dark .dark\:from-gray-900 {
                    --tw-gradient-from: #111827;
                }
                .dark .dark\:to-indigo-950 {
                    --tw-gradient-to: #1e1b4b;
                }
                
                /* Shadows */
                .shadow-lg {
                    --tw-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    box-shadow: var(--tw-shadow);
                }
                .shadow-indigo-200 {
                    --tw-shadow-color: rgba(199, 210, 254, 0.5);
                    box-shadow: var(--tw-shadow);
                }
                .shadow-2xl {
                    --tw-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    box-shadow: var(--tw-shadow);
                }
                .shadow-inner {
                    --tw-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
                    box-shadow: var(--tw-shadow);
                }
                
                /* Custom rounded corners */
                .rounded-3xl {
                    border-radius: 1.5rem;
                }
                .rounded-2xl {
                    border-radius: 1rem;
                }
                
                /* Gradients */
                .bg-gradient-to-br {
                    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
                }
                .from-blue-50 {
                    --tw-gradient-from: #eff6ff;
                    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0));
                }
                .to-indigo-50 {
                    --tw-gradient-to: #eef2ff;
                }
                .from-indigo-500 {
                    --tw-gradient-from: #6366f1;
                    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(99, 102, 241, 0));
                }
                .to-purple-600 {
                    --tw-gradient-to: #9333ea;
                }

                /* Media query support */
                @media (min-width: 1024px) {
                    .lg\:w-1\/2 {
                        width: 50%;
                    }
                    .lg\:flex-row {
                        flex-direction: row;
                    }
                    .lg\:text-left {
                        text-align: left;
                    }
                    .lg\:items-start {
                        align-items: flex-start;
                    }
                    .lg\:justify-start {
                        justify-content: flex-start;
                    }
                    .lg\:gap-20 {
                        gap: 5rem;
                    }
                }
                
                /* Support for dark mode toggle */
                @media (prefers-color-scheme: dark) {
                    .dark\:bg-gray-900 {
                        background-color: #111827;
                    }
                    .dark\:text-gray-200 {
                        color: #e5e7eb;
                    }
                    .dark\:text-white {
                        color: #ffffff;
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
                    .dark\:bg-gray-800 {
                        background-color: #1f2937;
                    }
                    .dark\:bg-indigo-900 {
                        background-color: #312e81;
                    }
                    .dark\:bg-purple-900 {
                        background-color: #581c87;
                    }
                    .dark\:border-gray-700 {
                        border-color: #374151;
                    }
                    .dark\:shadow-none {
                        box-shadow: none;
                    }
                    .dark\:hover\:bg-indigo-600:hover {
                        background-color: #4f46e5;
                    }
                    .dark\:hover\:border-indigo-500:hover {
                        border-color: #6366f1;
                    }
                    .dark\:hover\:text-indigo-400:hover {
                        color: #818cf8;
                    }
                }
            </style>
        @endif
    </head>
    <body class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-indigo-950 text-gray-800 dark:text-gray-200 min-h-screen flex flex-col">
        <header class="w-full px-6 py-4 sm:px-10">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                Bảng Điều Khiển
                            </a>
                        @else
                            <a href="{{ route('member.home') }}" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                Trang Chủ Thành Viên
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium transition hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-500 dark:hover:text-indigo-400">
                            Đăng Nhập
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                Đăng Ký
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-grow flex items-center justify-center px-4 sm:px-6">
            <div class="max-w-7xl w-full mx-auto py-12 md:py-24 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-indigo-600 dark:text-indigo-400 mb-6 font-['Be_Vietnam_Pro']">QR Check-in</h1>
                    <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-gray-100 mb-6 font-['Be_Vietnam_Pro']">Hệ Thống Quản Lý Check-in Thông Minh</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-xl">Quản lý thành viên hiệu quả với hệ thống quét mã QR tiên tiến. Theo dõi lịch sử check-in, quản lý thành viên và tối ưu hóa quy trình làm việc của bạn.</p>
                    
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                        @if (Route::has('login'))
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('dashboard') }}" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-200 dark:shadow-none">
                                        Truy Cập Bảng Điều Khiển
                                    </a>
                                @else
                                    <a href="{{ route('member.home') }}" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-200 dark:shadow-none">
                                        Truy Cập Trang Cá Nhân
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-200 dark:shadow-none">
                                    Bắt Đầu Ngay
                                </a>
                            @endauth
                        @endif
                        
                        <a href="#features" class="px-8 py-3 rounded-lg border border-gray-300 dark:border-gray-700 font-medium transition hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-500 dark:hover:text-indigo-400">
                            Tìm Hiểu Thêm
                        </a>
                    </div>
                </div>
                
                <div class="w-full lg:w-1/2 flex justify-center">
                    <div class="relative w-full max-w-lg">
                        <!-- Decoration elements -->
                        <div class="absolute -top-10 -left-10 w-72 h-72 bg-indigo-200 dark:bg-indigo-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-50 animate-blob"></div>
                        <div class="absolute -bottom-14 right-0 w-72 h-72 bg-purple-200 dark:bg-purple-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
                        
                        <!-- QR code illustration -->
                        <div class="relative backdrop-blur-sm bg-white/30 dark:bg-gray-800/30 border border-gray-200 dark:border-gray-700 p-8 rounded-3xl shadow-2xl">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-inner flex flex-col items-center">
                                <div class="w-48 h-48 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-2 mb-4">
                                    <div class="w-full h-full bg-white flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-40 h-40" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 9H9V3H3V9Z" fill="currentColor" />
                                            <path d="M3 21H9V15H3V21Z" fill="currentColor" />
                                            <path d="M15 3V9H21V3H15Z" fill="currentColor" />
                                            <path d="M15 21H21V15H15V21Z" fill="currentColor" />
                                            <path d="M3 3H5V5H3V3Z" fill="white" />
                                            <path d="M7 3H9V5H7V3Z" fill="white" />
                                            <path d="M3 7H5V9H3V7Z" fill="white" />
                                            <path d="M7 7H9V9H7V7Z" fill="white" />
                                            <path d="M15 3H17V5H15V3Z" fill="white" />
                                            <path d="M19 3H21V5H19V3Z" fill="white" />
                                            <path d="M15 7H17V9H15V7Z" fill="white" />
                                            <path d="M19 7H21V9H19V7Z" fill="white" />
                                            <path d="M3 15H5V17H3V15Z" fill="white" />
                                            <path d="M7 15H9V17H7V15Z" fill="white" />
                                            <path d="M3 19H5V21H3V19Z" fill="white" />
                                            <path d="M7 19H9V21H7V19Z" fill="white" />
                                            <path d="M15 15H17V17H15V15Z" fill="white" />
                                            <path d="M19 15H21V17H19V15Z" fill="white" />
                                            <path d="M15 19H17V21H15V19Z" fill="white" />
                                            <path d="M19 19H21V21H19V19Z" fill="white" />
                                            <path d="M12 9V3H15V6H13.5V7.5H15V9H12Z" fill="currentColor" />
                                            <path d="M13.5 12H12V9H15V10.5H13.5V12Z" fill="currentColor" />
                                            <path d="M9 10.5H10.5V12H9V10.5Z" fill="currentColor" />
                                            <path d="M10.5 13.5H9V15H12V12H10.5V13.5Z" fill="currentColor" />
                                            <path d="M12 10.5H13.5V12H12V10.5Z" fill="currentColor" />
                                            <path d="M13.5 13.5H12V15H13.5V13.5Z" fill="currentColor" />
                                            <path d="M15 15H13.5V13.5H15V15Z" fill="currentColor" />
                                            <path d="M15 10.5V12H13.5V10.5H15Z" fill="currentColor" />
                                            <path d="M10.5 10.5V9H12V10.5H10.5Z" fill="currentColor" />
                                            <path d="M12 13.5H10.5V12H12V13.5Z" fill="currentColor" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Quét mã để check-in</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <section id="features" class="py-16 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 font-['Be_Vietnam_Pro']">Tính Năng Nổi Bật</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">Giải pháp toàn diện giúp việc quản lý thành viên trở nên dễ dàng và hiệu quả hơn bao giờ hết.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Check-in Nhanh Chóng</h3>
                        <p class="text-gray-600 dark:text-gray-300">Quét mã QR để check-in chỉ trong vài giây, không cần thiết bị phức tạp hay quy trình rườm rà.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Quản Lý Thành Viên</h3>
                        <p class="text-gray-600 dark:text-gray-300">Theo dõi và quản lý thông tin thành viên, bao gồm thời hạn thành viên và các gói dịch vụ.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Báo Cáo Chi Tiết</h3>
                        <p class="text-gray-600 dark:text-gray-300">Truy cập các báo cáo chi tiết về số lượng check-in, thời gian sử dụng và xu hướng tham gia.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Thông Báo Tự Động</h3>
                        <p class="text-gray-600 dark:text-gray-300">Gửi thông báo tự động khi thành viên check-in hoặc khi sắp hết hạn thành viên.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Bảo Mật Cao</h3>
                        <p class="text-gray-600 dark:text-gray-300">Hệ thống được bảo mật chặt chẽ, đảm bảo thông tin thành viên luôn được bảo vệ an toàn.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 font-['Be_Vietnam_Pro']">Dữ Liệu Đồng Bộ</h3>
                        <p class="text-gray-600 dark:text-gray-300">Dữ liệu được đồng bộ hóa thời gian thực, cho phép truy cập từ mọi thiết bị mọi lúc, mọi nơi.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-gray-100 dark:bg-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mb-4 font-['Be_Vietnam_Pro']">QR Check-in</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Hệ thống quản lý check-in thông minh dành cho doanh nghiệp</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">&copy; {{ date('Y') }} Zplus Software. Bảo lưu mọi quyền.</p>
                </div>
            </div>
        </footer>

        <style>
            @keyframes blob {
                0%, 100% { transform: translate(0, 0) scale(1); }
                25% { transform: translate(20px, -30px) scale(1.1); }
                50% { transform: translate(-20px, 20px) scale(0.9); }
                75% { transform: translate(20px, 30px) scale(1.05); }
            }
            .animate-blob {
                animation: blob 12s ease-in-out infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
        </style>
    </body>
</html>
