{{-- resources/views/checkins/scan.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quét QR Check-in/Check-out') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col items-center mb-6">
                        <h3 class="text-lg font-semibold mb-4">Quét mã QR để check-in hoặc check-out</h3>
                        
                        <div id="reader" class="w-full md:w-1/2 mb-6"></div>
                        
                        <div id="result-container" class="hidden w-full md:w-1/2 p-4 border rounded-lg">
                            <div id="result-message" class="text-center text-lg font-semibold mb-4"></div>
                            
                            <div id="member-info" class="mb-4">
                                <p><strong>Tên:</strong> <span id="member-name"></span></p>
                                <p><strong>Email:</strong> <span id="member-email"></span></p>
                                <p><strong>Loại thành viên:</strong> <span id="member-type"></span></p>
                                <p id="membership-status"></p>
                            </div>
                            
                            <div id="checkin-actions" class="flex justify-center">
                                <button id="checkin-btn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                                    Check-in
                                </button>
                                <button id="checkout-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Check-out
                                </button>
                            </div>
                        </div>
                        
                        <div id="error-container" class="hidden w-full md:w-1/2 p-4 border border-red-300 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200">
                            <p id="error-message" class="text-center"></p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Check-in hôm nay</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Tên</th>
                                        <th class="px-4 py-2 text-left">Thời gian check-in</th>
                                        <th class="px-4 py-2 text-left">Thời gian check-out</th>
                                        <th class="px-4 py-2 text-left">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="checkins-today">
                                    <!-- Check-in data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentUserId = null;
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: 250 }
            );
            
            function onScanSuccess(decodedText, decodedResult) {
                // Stop scanner
                html5QrcodeScanner.clear();
                
                // Validate QR code with backend
                validateQrCode(decodedText);
            }
            
            function validateQrCode(qrCode) {
                fetch('{{ route("qrcode.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessResult(data.user);
                    } else {
                        showErrorResult(data.message, data.user);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorResult('Có lỗi xảy ra khi xác thực mã QR');
                });
            }
            
            function showSuccessResult(user) {
                currentUserId = user.id;
                
                // Hide error container
                document.getElementById('error-container').classList.add('hidden');
                
                // Show result container
                const resultContainer = document.getElementById('result-container');
                resultContainer.classList.remove('hidden');
                
                // Set user information
                document.getElementById('result-message').textContent = 'Xác thực thành công!';
                document.getElementById('member-name').textContent = user.name;
                document.getElementById('member-email').textContent = user.email;
                document.getElementById('member-type').textContent = user.member_type;
                
                const membershipStatus = document.getElementById('membership-status');
                membershipStatus.innerHTML = `<strong>Thành viên có hiệu lực đến:</strong> <span class="text-green-600 dark:text-green-400">${new Date(user.membership_end_date).toLocaleDateString('vi-VN')}</span>`;
                
                // Load today's check-ins
                loadTodayCheckIns();
            }
            
            function showErrorResult(message, user = null) {
                // Hide result container
                document.getElementById('result-container').classList.add('hidden');
                
                // Show error container
                const errorContainer = document.getElementById('error-container');
                errorContainer.classList.remove('hidden');
                
                document.getElementById('error-message').textContent = message;
                
                if (user) {
                    // User exists but doesn't have active membership
                    currentUserId = user.id;
                    
                    // Append user info to error message
                    document.getElementById('error-message').innerHTML += `<br><br>Thông tin thành viên:<br>
                        <strong>Tên:</strong> ${user.name}<br>
                        <strong>Email:</strong> ${user.email}<br>
                        <strong>Loại thành viên:</strong> ${user.member_type}`;
                }
                
                // Start scanner again after 5 seconds
                setTimeout(() => {
                    errorContainer.classList.add('hidden');
                    html5QrcodeScanner.render(onScanSuccess);
                }, 5000);
            }
            
            // Check-in action
            document.getElementById('checkin-btn').addEventListener('click', function() {
                if (!currentUserId) return;
                
                fetch('{{ route("checkin.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ user_id: currentUserId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Check-in thành công!');
                        loadTodayCheckIns();
                    } else {
                        alert(data.message);
                    }
                    
                    // Reset and restart scanner
                    document.getElementById('result-container').classList.add('hidden');
                    html5QrcodeScanner.render(onScanSuccess);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi check-in');
                });
            });
            
            // Check-out action
            document.getElementById('checkout-btn').addEventListener('click', function() {
                if (!currentUserId) return;
                
                fetch('{{ route("checkout.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ user_id: currentUserId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Check-out thành công!');
                        loadTodayCheckIns();
                    } else {
                        alert(data.message);
                    }
                    
                    // Reset and restart scanner
                    document.getElementById('result-container').classList.add('hidden');
                    html5QrcodeScanner.render(onScanSuccess);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi check-out');
                });
            });
            
            function loadTodayCheckIns() {
                fetch('{{ route("checkins.today") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('checkins-today');
                        tbody.innerHTML = '';
                        
                        data.check_ins.forEach(checkIn => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="border px-4 py-2">${checkIn.user.name}</td>
                                <td class="border px-4 py-2">${new Date(checkIn.check_in_time).toLocaleTimeString('vi-VN')}</td>
                                <td class="border px-4 py-2">${checkIn.check_out_time ? new Date(checkIn.check_out_time).toLocaleTimeString('vi-VN') : '-'}</td>
                                <td class="border px-4 py-2">${checkIn.check_out_time ? '<span class="text-green-600 dark:text-green-400">Đã check-out</span>' : '<span class="text-blue-600 dark:text-blue-400">Đang ở câu lạc bộ</span>'}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            
            // Load initial today's check-ins
            loadTodayCheckIns();
            
            // Start scanner
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
    @endpush
</x-app-layout>