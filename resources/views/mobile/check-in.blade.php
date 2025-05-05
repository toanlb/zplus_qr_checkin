<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Check-in | Mobile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        .camera-container {
            aspect-ratio: 1 / 1;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        #video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .scanner-box {
            width: 65%;
            height: 65%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.5);
        }
        .scanner-line {
            position: absolute;
            height: 2px;
            width: 100%;
            background: rgba(24, 144, 255, 0.8);
            box-shadow: 0 0 8px rgba(24, 144, 255, 0.8);
            animation: scan 2s linear infinite;
        }
        @keyframes scan {
            0% {
                top: 20%;
            }
            50% {
                top: 80%;
            }
            100% {
                top: 20%;
            }
        }
        .btn-mode-active {
            font-weight: bold;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        /* Add explicit hidden style for overlay */
        .overlay-hidden {
            display: none !important;
        }
        .activity-item {
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-indigo-600 text-white p-4 shadow-md">
            <div class="container mx-auto flex items-center justify-between">
                <h1 class="text-xl font-bold">QR Check-in</h1>
                <div>
                    <a href="{{ route('check-ins.index') }}" class="text-white p-2 rounded-full hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto p-4">
            <!-- Mode Selector -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <div class="flex justify-center space-x-2">
                    <button id="checkin-btn" class="flex-1 py-2 px-4 text-center rounded-lg bg-green-100 text-green-700 transition-all btn-mode-active" data-mode="checkin">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Check-in
                    </button>
                    <button id="checkout-btn" class="flex-1 py-2 px-4 text-center rounded-lg bg-gray-100 text-gray-700 transition-all" data-mode="checkout">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Check-out
                    </button>
                </div>
            </div>

            <!-- Camera View -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-4">
                <div class="p-4 bg-indigo-600 text-white text-center font-medium">
                    <span id="camera-title">Quét mã QR để check-in</span>
                </div>
                <div class="p-4">
                    <div class="camera-container mx-auto bg-black">
                        <div id="reader"></div>
                        <video id="video" playsinline autoplay style="display:none;"></video>
                        <div class="scanner-overlay">
                            <div class="scanner-box">
                                <div class="scanner-line"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p id="scanner-status" class="text-sm text-gray-500">Đang chuẩn bị camera...</p>
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <button id="toggle-camera" class="w-full py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Đổi camera
                    </button>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-100 border-b text-gray-800 font-medium">
                    Hoạt động gần đây
                </div>
                <div id="recent-activity" class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                    @forelse($recentCheckIns as $checkIn)
                        <div class="p-4 activity-item">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full {{ $checkIn->check_out_time ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($checkIn->check_out_time)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $checkIn->user->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($checkIn->check_out_time)
                                            <span class="font-medium text-red-600">Check-out</span> lúc {{ $checkIn->check_out_time->format('H:i') }}
                                        @else
                                            <span class="font-medium text-green-600">Check-in</span> lúc {{ $checkIn->check_in_time->format('H:i') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-auto text-right">
                                    <div class="text-xs text-gray-500">
                                        @if($checkIn->check_out_time)
                                            {{ $checkIn->check_out_time->diffForHumans() }}
                                        @else
                                            {{ $checkIn->check_in_time->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">
                            Chưa có hoạt động nào hôm nay
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- Status Overlay (hidden by default) -->
        <div id="status-overlay" class="overlay overlay-hidden">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 text-center">
                <div id="status-icon" class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center">
                    <!-- Icon will be inserted here -->
                </div>
                <h3 id="status-title" class="text-lg font-bold mb-2"></h3>
                <p id="status-message" class="text-gray-600 mb-4"></p>
                <div id="status-details" class="mb-4 p-3 bg-gray-50 rounded-lg text-left hidden">
                    <!-- Details will be inserted here -->
                </div>
                <button id="status-close" class="w-full py-2 bg-indigo-600 text-white rounded-lg">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Load QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Polyfill for older browsers -->
    <script src="https://unpkg.com/webrtc-adapter@8.2.3/out/adapter.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const video = document.getElementById('video');
            const checkinBtn = document.getElementById('checkin-btn');
            const checkoutBtn = document.getElementById('checkout-btn');
            const scannerStatus = document.getElementById('scanner-status');
            const cameraTitle = document.getElementById('camera-title');
            const toggleCameraBtn = document.getElementById('toggle-camera');
            const statusOverlay = document.getElementById('status-overlay');
            const statusIcon = document.getElementById('status-icon');
            const statusTitle = document.getElementById('status-title');
            const statusMessage = document.getElementById('status-message');
            const statusDetails = document.getElementById('status-details');
            const statusClose = document.getElementById('status-close');
            const recentActivity = document.getElementById('recent-activity');
            
            // Variables
            let html5QrCode;
            let currentMode = 'checkin';
            let currentCamera = 'environment'; // 'environment' (back) or 'user' (front)
            let isProcessingQrCode = false; // Flag to prevent multiple processing of the same QR code
            
            // Initialize Scanner
            function initScanner() {
                html5QrCode = new Html5Qrcode("reader");
                startScanner();
            }
            
            // Start QR Scanner
            function startScanner() {
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1,
                    formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ]
                };
                
                // Try to select the back camera first
                html5QrCode.start(
                    { facingMode: currentCamera },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    scannerStatus.textContent = 'Camera sẵn sàng. Hướng vào mã QR để quét.';
                }).catch((err) => {
                    console.error('Error starting camera:', err);
                    scannerStatus.textContent = 'Không thể khởi động camera. Vui lòng kiểm tra quyền truy cập.';
                });
            }
            
            // Toggle Camera (Front/Back)
            toggleCameraBtn.addEventListener('click', function() {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
                        startScanner();
                    }).catch(err => {
                        console.error('Error stopping camera:', err);
                    });
                }
            });
            
            // Process successful scan
            function onScanSuccess(decodedText, decodedResult) {
                // Check if we're already processing a QR code
                if (isProcessingQrCode) {
                    return; // Ignore new scans while processing an existing one
                }
                
                // Set flag to prevent multiple processing
                isProcessingQrCode = true;
                
                // Pause scanning
                html5QrCode.pause();
                
                // Process the QR code
                processQrCode(decodedText);
            }
            
            // Handle scan failures
            function onScanFailure(error) {
                // Do nothing on failure - this is called very frequently
            }
            
            // Process QR Code
            function processQrCode(qrCode) {
                // Show scanning status
                scannerStatus.textContent = 'Đang xử lý...';
                
                // Send to server
                fetch('{{ route("mobile.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        qr_code: qrCode,
                        action: currentMode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Only show the status overlay once
                    showStatusOverlay(data);
                    
                    // We'll handle resuming in the close button click, 
                    // not automatically after a delay
                    
                    // Update activity list if successful
                    if (data.success) {
                        updateActivityList(data);
                    }
                })
                .catch(error => {
                    console.error('Error processing QR code:', error);
                    showStatusOverlay({
                        success: false,
                        message: 'Lỗi kết nối. Vui lòng thử lại.'
                    });
                    
                    // For error cases, still resume after delay
                    setTimeout(() => {
                        if (html5QrCode && html5QrCode.isPaused()) {
                            html5QrCode.resume();
                            scannerStatus.textContent = 'Camera sẵn sàng. Hướng vào mã QR để quét.';
                        }
                    }, 2000);
                });
            }
            
            // Show status overlay
            function showStatusOverlay(data) {
                const success = data.success;
                
                // Set icon
                if (success) {
                    const isCheckIn = data.type === 'checkin';
                    statusIcon.className = `mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center ${isCheckIn ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'}`;
                    statusIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            ${isCheckIn 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                        </svg>
                    `;
                } else {
                    statusIcon.className = 'mx-auto mb-4 w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center';
                    statusIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    `;
                }
                
                // Set title and message
                statusTitle.textContent = success 
                    ? (data.type === 'checkin' ? 'Check-in Thành công' : 'Check-out Thành công') 
                    : 'Thất bại';
                statusMessage.textContent = data.message;
                
                // Show/hide details
                if (success && data.user) {
                    let detailsHTML = '';
                    if (data.type === 'checkin') {
                        detailsHTML = `
                            <div class="text-sm mb-2">
                                <span class="font-medium">Tên:</span> ${data.user.name}
                            </div>
                            <div class="text-sm mb-2">
                                <span class="font-medium">Loại thành viên:</span> ${data.user.member_type || 'Thành viên'}
                            </div>
                            <div class="text-sm">
                                <span class="font-medium">Giờ check-in:</span> ${data.user.check_in_time}
                            </div>
                        `;
                    } else { // checkout
                        detailsHTML = `
                            <div class="text-sm mb-2">
                                <span class="font-medium">Tên:</span> ${data.user.name}
                            </div>
                            <div class="text-sm mb-2">
                                <span class="font-medium">Giờ check-in:</span> ${data.user.check_in_time}
                            </div>
                            <div class="text-sm mb-2">
                                <span class="font-medium">Giờ check-out:</span> ${data.user.check_out_time}
                            </div>
                            <div class="text-sm font-medium text-indigo-600">
                                <span class="font-medium">Thời gian sử dụng:</span> ${data.user.duration}
                            </div>
                        `;
                    }
                    
                    statusDetails.innerHTML = detailsHTML;
                    statusDetails.classList.remove('hidden');
                } else {
                    statusDetails.classList.add('hidden');
                }
                
                // Show overlay - use the new class
                statusOverlay.classList.remove('overlay-hidden');
            }
            
            // Close status overlay
            statusClose.addEventListener('click', function() {
                // Reset the processing flag before reloading
                isProcessingQrCode = false;
                
                // Refresh the page when closing the popup
                window.location.reload();
            });
            
            // Update activity list
            function updateActivityList(data) {
                if (!data.user) return;
                
                const now = new Date();
                const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                                now.getMinutes().toString().padStart(2, '0');
                
                const newActivityHTML = `
                    <div class="p-4 activity-item">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full ${data.type === 'checkin' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'} flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        ${data.type === 'checkin'
                                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />'
                                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />'}
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${data.user.name}</div>
                                <div class="text-xs text-gray-500">
                                    ${data.type === 'checkin' 
                                        ? 'Check-in lúc ' + (data.user.check_in_time || timeString)
                                        : 'Check-out lúc ' + (data.user.check_out_time || timeString)}
                                </div>
                            </div>
                            <div class="ml-auto text-right">
                                <div class="text-xs text-gray-500">Vừa xong</div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add new activity to top
                const emptyMessage = recentActivity.querySelector('.text-center');
                if (emptyMessage) {
                    recentActivity.innerHTML = newActivityHTML;
                } else {
                    const firstActivity = recentActivity.firstChild;
                    const newActivity = document.createElement('div');
                    newActivity.innerHTML = newActivityHTML;
                    recentActivity.insertBefore(newActivity.firstChild, firstActivity);
                }
            }
            
            // Mode switching
            checkinBtn.addEventListener('click', function() {
                if (currentMode !== 'checkin') {
                    currentMode = 'checkin';
                    updateModeUI();
                }
            });
            
            checkoutBtn.addEventListener('click', function() {
                if (currentMode !== 'checkout') {
                    currentMode = 'checkout';
                    updateModeUI();
                }
            });
            
            function updateModeUI() {
                // Update buttons
                if (currentMode === 'checkin') {
                    checkinBtn.classList.add('bg-green-100', 'text-green-700', 'btn-mode-active');
                    checkinBtn.classList.remove('bg-gray-100', 'text-gray-700');
                    
                    checkoutBtn.classList.add('bg-gray-100', 'text-gray-700');
                    checkoutBtn.classList.remove('bg-red-100', 'text-red-700', 'btn-mode-active');
                    
                    cameraTitle.textContent = 'Quét mã QR để check-in';
                } else {
                    checkinBtn.classList.add('bg-gray-100', 'text-gray-700');
                    checkinBtn.classList.remove('bg-green-100', 'text-green-700', 'btn-mode-active');
                    
                    checkoutBtn.classList.add('bg-red-100', 'text-red-700', 'btn-mode-active');
                    checkoutBtn.classList.remove('bg-gray-100', 'text-gray-700');
                    
                    cameraTitle.textContent = 'Quét mã QR để check-out';
                }
            }
            
            // Initialize
            initScanner();
        });
    </script>
</body>
</html>