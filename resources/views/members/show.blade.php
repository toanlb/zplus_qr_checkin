<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Member Details') }}: {{ $member->name }}
            </h2>
            <div>
                <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:border-yellow-800 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                    {{ __('Edit Member') }}
                </a>
                <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Back to Members') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="md:flex">
                        <div class="md:w-1/3 p-4 border-r">
                            <div class="text-center">
                                <div class="bg-gray-100 p-4 rounded-lg inline-block mb-4">
                                    @if(Storage::disk('public')->exists('qrcodes/' . $member->id . '.svg'))
                                        <img 
                                            src="{{ url('qrcode/image/' . $member->id) }}" 
                                            alt="QR Code" 
                                            class="mx-auto"
                                            id="qr-code-image"
                                            style="width: 192px; height: auto;"
                                        >
                                    @elseif(Storage::disk('public')->exists('qrcodes/' . $member->id . '.png'))
                                        <img 
                                            src="{{ url('qrcode/image/' . $member->id) }}" 
                                            alt="QR Code" 
                                            class="mx-auto"
                                            id="qr-code-image"
                                            style="width: 192px; height: auto;"
                                        >
                                    @else
                                        <div class="text-gray-500 p-12">QR Code not generated</div>
                                    @endif
                                </div>
                                <div class="mt-4 text-sm text-gray-600">
                                    <p>QR Code ID: <span class="font-medium select-all">{{ $member->qr_code }}</span></p>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex space-x-2 justify-center">
                                            <form action="{{ route('members.regenerate-qr', $member) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    {{ __('Regenerate QR') }}
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('qrcode.view', $member->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ __('View QR Page') }}
                                            </a>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" id="print-pdf-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                {{ __('Print PDF') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- QR Size Control -->
                                <div class="w-full mt-4">
                                    <label for="qr-size" class="block text-sm font-medium text-gray-700 mb-2">QR Code Size:</label>
                                    <input type="range" id="qr-size" min="100" max="300" value="192" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:w-2/3 p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-1">{{ $member->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1">{{ $member->email }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Phone</p>
                                    <p class="mt-1">{{ $member->phone }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Birth Date</p>
                                    <p class="mt-1">{{ $member->birth_date ? $member->birth_date->format('M d, Y') : 'Not provided' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Address</p>
                                    <p class="mt-1">{{ $member->address ?: 'Not provided' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Member Type</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($member->member_type == 'vip') bg-purple-100 text-purple-800 
                                            @elseif($member->member_type == 'premium') bg-blue-100 text-blue-800 
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($member->member_type) }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Registration Date</p>
                                    <p class="mt-1">{{ $member->created_at->format('M d, Y') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Current Membership Status</p>
                                    <p class="mt-1">
                                        @if($member->activeMembership)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($member->activeMembership->status == 'active') bg-green-100 text-green-800 
                                                @elseif($member->activeMembership->status == 'expired') bg-red-100 text-red-800 
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($member->activeMembership->status) }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">
                                                Expires: {{ $member->activeMembership->end_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No Active Membership
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Membership History</h3>
                        <a href="{{ route('memberships.create', ['member_id' => $member->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Add Membership') }}
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        End Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($member->memberships as $membership)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $membership->start_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $membership->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($membership->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($membership->status == 'active') bg-green-100 text-green-800 
                                                @elseif($membership->status == 'expired') bg-red-100 text-red-800 
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($membership->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('memberships.show', $membership) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                            <a href="{{ route('memberships.edit', $membership) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No membership records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Check-in History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Check-in History</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-in Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Check-out Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Duration
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($member->checkIns as $checkIn)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $checkIn->date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $checkIn->check_in_time->format('h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $checkIn->check_out_time ? $checkIn->check_out_time->format('h:i A') : 'Not checked out' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($checkIn->check_out_time)
                                                {{ $checkIn->check_in_time->diffInMinutes($checkIn->check_out_time) }} minutes
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No check-in records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Template for PDF -->
    <div id="pdf-template" style="display:none;">
        <div class="container" style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; text-align: center;">
            <div class="header" style="margin-bottom: 20px;">
                <h1 style="margin-bottom: 5px; font-size: 24px;">{{ $member->name }}</h1>
                <p style="margin: 5px 0; color: #666;">{{ $member->email }}</p>
                <p style="margin: 5px 0; color: #666;">{{ $member->phone ?? 'N/A' }}</p>
            </div>
            <div class="qr-code" style="margin: 30px 0;">
                <img src="{{ url('qrcode/image/' . $member->id) }}" alt="QR Code" style="width: 200px; height: auto;">
            </div>
            <div class="info" style="margin: 20px 0; font-size: 14px;">
                <p style="margin: 5px 0; font-weight: bold;">QR Code ID: {{ $member->qr_code }}</p>
                <p style="margin: 5px 0;">Member Type: {{ ucfirst($member->member_type) }}</p>
                @if($member->activeMembership)
                <p style="margin: 5px 0;">Valid until: {{ $member->activeMembership->end_date->format('M d, Y') }}</p>
                @endif
            </div>
            <div class="footer" style="margin-top: 30px; font-size: 12px; color: #666;">
                <p style="margin: 3px 0;">Present this QR code to staff for check-in and check-out</p>
                <p style="margin: 3px 0;">Generated on: {{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Add an event listener that will execute when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // QR code size slider functionality
            var qrSizeSlider = document.getElementById('qr-size');
            var qrCodeImage = document.getElementById('qr-code-image');
            
            if (qrSizeSlider && qrCodeImage) {
                qrSizeSlider.addEventListener('input', function() {
                    qrCodeImage.style.width = this.value + 'px';
                });
            }
            
            // Print PDF button functionality
            var printBtn = document.getElementById('print-pdf-btn');
            
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    printQRCodePDF();
                });
            }
        });
        
        // Function to print QR code as PDF
        function printQRCodePDF() {
            try {
                // Get the template content
                var templateContent = document.getElementById('pdf-template').innerHTML;
                
                // Create a new window
                var printWindow = window.open('', '_blank');
                
                if (!printWindow) {
                    alert('Pop-up blocker may be preventing the PDF from opening. Please allow pop-ups for this site.');
                    return;
                }
                
                // Write the template content to the new window
                printWindow.document.write('<!DOCTYPE html><html><head><title>QR Code - {{ $member->name }}</title><meta charset="utf-8"><style>@media print {body {margin: 0;padding: 0;}}</style></head><body>' + templateContent + '<script>window.onload = function() {setTimeout(function() {window.print();setTimeout(function() {window.close();}, 500);}, 500);};<\/script></body></html>');
                
                printWindow.document.close();
            } catch (error) {
                console.error('Error in printQRCodePDF function:', error);
                alert('There was an error generating the PDF. Please try again or contact support.');
            }
        }
    </script>
</x-app-layout>