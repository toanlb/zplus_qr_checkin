<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Check-in - Laptop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        /* Đảm bảo class .hidden có specificity cao và không bị ghi đè */
        .hidden {
            display: none !important;
        }
        
        .camera-container {
            aspect-ratio: 4/3;
            max-width: 640px;
            max-height: 480px;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1); /* Flip the video horizontally */
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
            transform: scaleX(-1); /* Flip the overlay to match the video */
        }
        .scanner-box {
            width: 250px;
            height: 250px;
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
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-indigo-600 text-white p-4 shadow-md">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold">QR Check-in</h1>
                    <span class="ml-2 px-2 py-1 bg-indigo-800 text-xs font-medium rounded-full">Laptop Mode</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('check-ins.index') }}" class="flex items-center text-white hover:bg-indigo-700 px-3 py-2 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Quay lại Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow py-8">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Camera -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                                <!-- Mode Selector -->
                                <div class="flex items-center border-b">
                                    <button id="checkin-btn" class="flex-1 py-3 text-center bg-green-100 text-green-700 transition-all btn-mode-active" data-mode="checkin">
                                        <div class="flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Check-in
                                        </div>
                                    </button>
                                    <button id="checkout-btn" class="flex-1 py-3 text-center bg-gray-100 text-gray-700 transition-all" data-mode="checkout">
                                        <div class="flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Check-out
                                        </div>
                                    </button>
                                </div>
                                
                                <!-- Camera Title -->
                                <div class="p-4 bg-indigo-50 text-indigo-800 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span id="camera-title">Quét mã QR để check-in</span>
                                </div>
                                
                                <!-- Camera View -->
                                <div class="p-6 flex flex-col items-center">
                                    <div class="camera-container bg-black mb-4">
                                        <video id="video" autoplay muted playsinline></video>
                                        <div class="scanner-overlay">
                                            <div class="scanner-box">
                                                <div class="scanner-line"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mb-4">
                                        <p id="scanner-status" class="text-sm text-gray-500">Đang chuẩn bị camera...</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 w-full">
                                        <button id="toggle-camera" class="py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Đổi camera
                                        </button>
                                        <button id="reset-scanner" class="py-2 px-4 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Khởi động lại camera
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Manual Input -->
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b text-gray-800 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Nhập mã thủ công
                                </div>
                                <div class="p-6">
                                    <div class="mb-4">
                                        <label for="manual-code" class="block text-sm font-medium text-gray-700 mb-1">Mã QR</label>
                                        <div class="flex">
                                            <input type="text" id="manual-code" class="flex-1 rounded-l-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nhập mã QR thủ công">
                                            <button id="process-manual" class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Xử lý
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">Sử dụng chức năng này khi camera không hoạt động hoặc mã QR bị hỏng.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Recent Activity -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b text-gray-800 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Hoạt động gần đây
                                </div>
                                <div id="recent-activity" class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
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
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Overlay (hidden by default) -->
    <div id="status-overlay" class="overlay hidden">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 text-center">
            <div id="status-icon" class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center">
                <!-- Icon will be inserted here -->
            </div>
            <h3 id="status-title" class="text-lg font-bold mb-2"></h3>
            <p id="status-message" class="text-gray-600 mb-4"></p>
            <div id="status-details" class="mb-4 p-3 bg-gray-50 rounded-lg text-left hidden">
                <!-- Details will be inserted here -->
            </div>
            <button id="status-close" class="w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">Đóng</button>
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
            const resetScannerBtn = document.getElementById('reset-scanner');
            const statusOverlay = document.getElementById('status-overlay');
            const statusIcon = document.getElementById('status-icon');
            const statusTitle = document.getElementById('status-title');
            const statusMessage = document.getElementById('status-message');
            const statusDetails = document.getElementById('status-details');
            const statusClose = document.getElementById('status-close');
            const recentActivity = document.getElementById('recent-activity');
            const manualCodeInput = document.getElementById('manual-code');
            const processManualBtn = document.getElementById('process-manual');
            
            // Đảm bảo overlay luôn ẩn khi trang tải
            if (statusOverlay) {
                statusOverlay.classList.add('hidden');
            }
            
            // Variables
            let html5QrCode;
            let currentMode = 'checkin';
            let currentCamera = 'environment'; // 'environment' (back) or 'user' (front)
            let cameraId = null;
            
            // Initialize Scanner
            function initScanner() {
                // Tạo container cho HTML5 QR Scanner nếu cần
                const readerContainer = document.createElement('div');
                readerContainer.id = 'reader';
                readerContainer.style.width = '100%';
                readerContainer.style.height = '100%';
                readerContainer.style.position = 'absolute';
                readerContainer.style.top = '0';
                readerContainer.style.left = '0';
                readerContainer.style.zIndex = '1'; // Đảm bảo nằm trên video nhưng dưới overlay
                
                // Thêm container vào camera-container
                const cameraContainer = document.querySelector('.camera-container');
                if (cameraContainer) {
                    // Đảm bảo video hiển thị
                    if (video) {
                        video.style.display = 'block';
                    }
                    
                    // Đảm bảo container có position relative
                    cameraContainer.style.position = 'relative';
                    
                    // Xóa reader container cũ nếu có
                    const oldReader = document.getElementById('reader');
                    if (oldReader) {
                        oldReader.remove();
                    }
                    
                    // Thêm container mới
                    cameraContainer.appendChild(readerContainer);
                }
                
                // Khởi tạo HTML5 QR Code scanner
                try {
                    html5QrCode = new Html5Qrcode("reader");
                    scannerStatus.textContent = 'Đang tìm kiếm camera...';
                    
                    // Nhận tất cả camera có sẵn
                    Html5Qrcode.getCameras()
                        .then(devices => {
                            console.log('Available cameras:', devices);
                            if (devices && devices.length) {
                                scannerStatus.textContent = `Tìm thấy ${devices.length} camera.`;
                                
                                // Ưu tiên camera sau (environment) nếu có
                                let selectedCameraId = devices[0].id;
                                for(let i = 0; i < devices.length; i++) {
                                    if(devices[i].label.toLowerCase().includes('back') || 
                                       devices[i].label.toLowerCase().includes('environment') || 
                                       devices[i].label.toLowerCase().includes('sau')) {
                                        selectedCameraId = devices[i].id;
                                        break;
                                    }
                                }
                                cameraId = selectedCameraId;
                                startScanner();
                            } else {
                                scannerStatus.textContent = 'Không tìm thấy camera. Vui lòng kiểm tra quyền truy cập hoặc nhập mã QR thủ công.';
                            }
                        })
                        .catch(err => {
                            console.error('Error getting cameras', err);
                            scannerStatus.textContent = 'Lỗi khi truy cập camera: ' + err.message;
                        });
                } catch (err) {
                    console.error('Error initializing scanner', err);
                    scannerStatus.textContent = 'Lỗi khởi tạo scanner: ' + err.message;
                }
            }
            
            // Start QR Scanner
            function startScanner() {
                if (!cameraId) {
                    scannerStatus.textContent = 'Không tìm thấy camera. Vui lòng nhập mã QR thủ công.';
                    return;
                }
                
                scannerStatus.textContent = 'Đang khởi động camera...';
                
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.33,
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    videoConstraints: {
                        deviceId: cameraId,
                        facingMode: currentCamera
                    }
                };
                
                // Đảm bảo video hiển thị
                if (video) {
                    video.style.display = 'block';
                }
                
                try {
                    html5QrCode.start(
                        { deviceId: cameraId },
                        config,
                        onScanSuccess,
                        onScanFailure
                    )
                    .then(() => {
                        scannerStatus.textContent = 'Camera đang hoạt động. Hướng vào mã QR để quét.';
                        console.log('Camera started successfully');
                        
                        // Thiết lập hiển thị video từ camera lên thẻ video
                        // Lấy stream từ HTML5QrCode
                        const videoElement = document.getElementById('reader').querySelector('video');
                        if (videoElement && videoElement.srcObject) {
                            // Sao chép stream từ scanner vào thẻ video chính
                            video.srcObject = videoElement.srcObject;
                            video.play().catch(e => console.error('Error playing video:', e));
                        }
                    })
                    .catch((err) => {
                        console.error('Error starting camera:', err);
                        scannerStatus.textContent = 'Không thể khởi động camera: ' + err.message;
                    });
                } catch (err) {
                    console.error('Exception when starting camera:', err);
                    scannerStatus.textContent = 'Lỗi khi khởi động camera: ' + err.message;
                }
            }
            
            // Toggle Camera
            toggleCameraBtn.addEventListener('click', function() {
                scannerStatus.textContent = 'Đang chuyển đổi camera...';
                
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop()
                        .then(() => {
                            console.log('Camera stopped successfully');
                            // Get all cameras
                            Html5Qrcode.getCameras()
                                .then(devices => {
                                    if (devices.length > 1) {
                                        // Find the next camera in the list
                                        const currentIndex = devices.findIndex(device => device.id === cameraId);
                                        const nextIndex = (currentIndex + 1) % devices.length;
                                        cameraId = devices[nextIndex].id;
                                        console.log('Switching to camera:', cameraId);
                                        startScanner();
                                    } else {
                                        // Only one camera, restart with the same camera
                                        console.log('Only one camera available, restarting');
                                        startScanner();
                                    }
                                })
                                .catch(err => {
                                    console.error("Error getting cameras", err);
                                    scannerStatus.textContent = 'Lỗi khi liệt kê camera: ' + err.message;
                                    startScanner();
                                });
                        })
                        .catch(err => {
                            console.error('Error stopping camera:', err);
                            scannerStatus.textContent = 'Lỗi khi dừng camera: ' + err.message;
                            // Attempt to reinitialize
                            initScanner();
                        });
                } else {
                    console.log('Scanner not running, initializing');
                    initScanner();
                }
            });
            
            // Reset Scanner
            resetScannerBtn.addEventListener('click', function() {
                scannerStatus.textContent = 'Đang khởi động lại camera...';
                
                if (html5QrCode) {
                    if (html5QrCode.isScanning) {
                        html5QrCode.stop()
                            .then(() => {
                                console.log('Camera stopped for reset');
                                initScanner();
                            })
                            .catch(err => {
                                console.error('Error stopping camera for reset:', err);
                                scannerStatus.textContent = 'Lỗi khi dừng camera: ' + err.message;
                                // Force reinitialize
                                initScanner();
                            });
                    } else {
                        console.log('Scanner not running, reinitializing');
                        initScanner();
                    }
                } else {
                    console.log('Scanner not initialized, initializing');
                    initScanner();
                }
            });
            
            // Process QR Code
            function processQrCode(qrCode) {
                // Show scanning status
                scannerStatus.textContent = 'Đang xử lý...';
                
                // Stop scanner temporarily while processing
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.pause();
                }
                
                // Send to server for processing
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route('mobile.process-qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        qr_code: qrCode,
                        action: currentMode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatusOverlay(
                            data.action === 'checkin' ? 'success-checkin' : 'success-checkout', 
                            data.message, 
                            data.details
                        );
                        
                        // Add to recent activity
                        updateRecentActivity(data.activity);
                    } else {
                        showStatusOverlay('error', data.message, data.details);
                    }
                })
                .catch(error => {
                    console.error('Error processing QR code:', error);
                    showStatusOverlay('error', 'Có lỗi xảy ra khi xử lý mã QR', 'Vui lòng thử lại sau');
                })
                .finally(() => {
                    // Resume scanner after processing
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.resume();
                    }
                    scannerStatus.textContent = 'Camera sẵn sàng. Hướng vào mã QR để quét.';
                });
            }
            
            // QR Scan Success Handler
            function onScanSuccess(decodedText, decodedResult) {
                console.log('Code scanned:', decodedText);
                scannerStatus.textContent = 'Đã quét được mã QR. Đang xử lý...';
                
                // Tạm dừng quét
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.pause();
                }
                
                // Xử lý mã QR
                processQRCode(decodedText);
            }
            
            // QR Scan Failure Handler
            function onScanFailure(error) {
                // Không cần log lỗi quét, điều này xảy ra liên tục khi không có mã QR
                // console.log('QR scan error:', error);
            }
            
            // Process QR Code
            function processQRCode(code) {
                const checkMode = currentMode; // 'checkin' or 'checkout'
                
                fetch('{{ route("mobile.process-qr") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qr_code: code,
                        action: checkMode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Process response:', data);
                    
                    if (data.success) {
                        // Thành công
                        showStatus(
                            'success',
                            data.title || (checkMode === 'checkin' ? 'Check-in thành công!' : 'Check-out thành công!'),
                            data.message,
                            data.user
                        );
                        
                        // Cập nhật danh sách hoạt động gần đây (nếu có dữ liệu)
                        if (data.recentActivity) {
                            updateRecentActivity(data.recentActivity);
                        } else {
                            // Chỉ thêm hoạt động mới nhất
                            if (data.checkIn) {
                                prependActivityItem(data.checkIn);
                            }
                        }

                        // Thêm tuỳ chọn tiếp tục quét sau khi thành công
                        showContinueOptions();
                    } else {
                        // Lỗi
                        showStatus(
                            'error',
                            data.title || 'Đã xảy ra lỗi!',
                            data.message || 'Không thể xử lý mã QR này.',
                            null
                        );
                        
                        // Tự động tiếp tục quét sau lỗi
                        setTimeout(() => {
                            if (html5QrCode && html5QrCode.isPaused) {
                                html5QrCode.resume();
                            }
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error processing QR code:', error);
                    showStatus(
                        'error',
                        'Lỗi kết nối!',
                        'Không thể kết nối với máy chủ. Vui lòng kiểm tra kết nối mạng.',
                        null
                    );
                    
                    // Tự động tiếp tục quét sau lỗi
                    setTimeout(() => {
                        if (html5QrCode && html5QrCode.isPaused) {
                            html5QrCode.resume();
                        }
                    }, 3000);
                });
            }
            
            // Show Continue Options
            function showContinueOptions() {
                // Tạo overlay cho tuỳ chọn tiếp tục
                const continueOverlay = document.createElement('div');
                continueOverlay.className = 'overlay';
                continueOverlay.id = 'continue-overlay';
                
                continueOverlay.innerHTML = `
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 text-center">
                        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2">Tiếp tục quét?</h3>
                        <p class="text-gray-600 mb-4">Bạn có muốn tiếp tục quét mã QR khác không?</p>
                        <div class="grid grid-cols-2 gap-4">
                            <button id="continue-yes" class="py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                Có, tiếp tục
                            </button>
                            <button id="continue-no" class="py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Không, dừng lại
                            </button>
                        </div>
                    </div>
                `;
                
                // Thêm overlay vào body
                document.body.appendChild(continueOverlay);
                
                // Xử lý các nút
                document.getElementById('continue-yes').addEventListener('click', function() {
                    // Đóng overlay
                    continueOverlay.remove();
                    
                    // Đóng status nếu đang mở
                    if (!statusOverlay.classList.contains('hidden')) {
                        statusOverlay.classList.add('hidden');
                    }
                    
                    // Tiếp tục quét
                    if (html5QrCode) {
                        if (html5QrCode.isPaused) {
                            html5QrCode.resume();
                        } else if (!html5QrCode.isScanning) {
                            startScanner();
                        }
                        scannerStatus.textContent = 'Camera đang hoạt động. Hướng vào mã QR để quét.';
                    }
                });
                
                document.getElementById('continue-no').addEventListener('click', function() {
                    // Đóng overlay
                    continueOverlay.remove();
                    
                    // Hiển thị thông báo dừng quét
                    scannerStatus.textContent = 'Đã dừng quét. Nhấn "Khởi động lại camera" để tiếp tục.';
                    
                    // Dừng scanner nếu đang quét
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.stop().then(() => {
                            console.log('Camera stopped by user');
                        }).catch(err => {
                            console.error('Error stopping camera:', err);
                        });
                    }
                });
            }
            
            // Show Status
            function showStatus(type, title, message, userData) {
                statusTitle.textContent = title;
                statusMessage.textContent = message;
                
                // Clear previous classes
                statusIcon.className = 'mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center';
                
                // Set icon and color based on type
                if (type === 'success') {
                    statusIcon.classList.add('bg-green-100', 'text-green-600');
                    statusIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                } else if (type === 'error') {
                    statusIcon.classList.add('bg-red-100', 'text-red-600');
                    statusIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `;
                } else if (type === 'warning') {
                    statusIcon.classList.add('bg-yellow-100', 'text-yellow-600');
                    statusIcon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    `;
                }
                
                // Show user details if provided
                if (userData) {
                    // Format user details
                    const membershipStatus = userData.membership && userData.membership.is_active ? 
                        `<div class="text-green-600 font-medium">Thành viên đang hoạt động</div>` : 
                        `<div class="text-red-600 font-medium">Thành viên không hoạt động</div>`;
                    
                    const membershipExpiry = userData.membership && userData.membership.expiry_date ? 
                        `<div class="mt-1">Ngày hết hạn: ${userData.membership.expiry_date}</div>` : 
                        `<div class="mt-1">Không có thông tin hết hạn</div>`;
                    
                    statusDetails.innerHTML = `
                        <div>
                            <div class="font-medium">${userData.name}</div>
                            <div class="text-sm text-gray-500">${userData.email}</div>
                            <div class="mt-2">${membershipStatus}</div>
                            ${membershipExpiry}
                        </div>
                    `;
                    statusDetails.classList.remove('hidden');
                } else {
                    statusDetails.classList.add('hidden');
                }
                
                // Show overlay
                statusOverlay.classList.remove('hidden');
            }
            
            // Prepend Activity Item
            function prependActivityItem(checkIn) {
                const isCheckout = checkIn.check_out_time;
                const iconSvg = isCheckout ? 
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />` : 
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />`;
                
                const time = isCheckout ? 
                    `Check-out lúc ${new Date(checkIn.check_out_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}` : 
                    `Check-in lúc ${new Date(checkIn.check_in_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}`;
                
                const itemHtml = `
                    <div class="p-4 activity-item">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full ${isCheckout ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'} flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        ${iconSvg}
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${checkIn.user.name}</div>
                                <div class="text-xs text-gray-500">
                                    ${time}
                                </div>
                            </div>
                            <div class="ml-auto text-right">
                                <div class="text-xs text-gray-500">vừa xong</div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Thêm item vào đầu danh sách
                const emptyMessage = recentActivity.querySelector('.text-center.text-gray-500');
                if (emptyMessage) {
                    // Xóa thông báo "Chưa có hoạt động nào"
                    emptyMessage.remove();
                }
                
                // Tạo và thêm phần tử mới
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = itemHtml;
                const newItem = tempDiv.firstElementChild;
                
                // Thêm vào đầu danh sách
                if (recentActivity.firstChild) {
                    recentActivity.insertBefore(newItem, recentActivity.firstChild);
                } else {
                    recentActivity.appendChild(newItem);
                }
            }
            
            // Update Recent Activity
            function updateRecentActivity(activities) {
                // Xóa tất cả hoạt động hiện tại
                recentActivity.innerHTML = '';
                
                if (activities.length === 0) {
                    recentActivity.innerHTML = `
                        <div class="p-6 text-center text-gray-500">
                            Chưa có hoạt động nào hôm nay
                        </div>
                    `;
                    return;
                }
                
                // Thêm các hoạt động mới
                activities.forEach(checkIn => {
                    const isCheckout = checkIn.check_out_time;
                    const iconSvg = isCheckout ? 
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />` : 
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />`;
                    
                    const time = isCheckout ? 
                        `Check-out lúc ${new Date(checkIn.check_out_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}` : 
                        `Check-in lúc ${new Date(checkIn.check_in_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}`;
                    
                    const timeAgo = checkIn.created_at_diff;
                    
                    const itemHtml = `
                        <div class="p-4 activity-item">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full ${isCheckout ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'} flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            ${iconSvg}
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">${checkIn.user.name}</div>
                                    <div class="text-xs text-gray-500">
                                        ${time}
                                    </div>
                                </div>
                                <div class="ml-auto text-right">
                                    <div class="text-xs text-gray-500">${timeAgo}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    recentActivity.innerHTML += itemHtml;
                });
            }
            
            // Update Mode (Check-in or Check-out)
            function updateMode(mode) {
                // ...existing code...
            }
            
            // Mode Switch Event Listeners
            checkinBtn.addEventListener('click', function() {
                updateMode('checkin');
            });
            
            checkoutBtn.addEventListener('click', function() {
                updateMode('checkout');
            });
            
            // Status Close Button
            if (statusClose && statusOverlay) {
                // Xóa tất cả event listener cũ (nếu có)
                const newStatusClose = statusClose.cloneNode(true);
                statusClose.parentNode.replaceChild(newStatusClose, statusClose);
                
                // Thêm event listener mới
                newStatusClose.addEventListener('click', function() {
                    statusOverlay.classList.add('hidden');
                });
            }
            
            // Manual Code Processing
            processManualBtn.addEventListener('click', function() {
                const code = manualCodeInput.value.trim();
                if (code) {
                    processQrCode(code);
                    manualCodeInput.value = '';
                }
            });
            
            // Enter key in manual input
            manualCodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    processManualBtn.click();
                }
            });
            
            // Log trình duyệt và thông tin thiết bị để debug
            console.log('User Agent:', navigator.userAgent);
            console.log('Browser supports mediaDevices:', !!navigator.mediaDevices);
            if (navigator.mediaDevices) {
                console.log('Browser supports getUserMedia:', !!navigator.mediaDevices.getUserMedia);
            }
            
            // Initialize the scanner
            setTimeout(() => {
                console.log('Initializing scanner...');
                initScanner();
            }, 1000);
        });
    </script>
</body>
</html>