{{-- resources/views/checkins/history.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lịch sử Check-in') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">{{ $user->name }}</h3>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Loại thành viên:</strong> {{ ucfirst($user->member_type) }}</p>
                        @if($user->hasActiveMembership())
                            <p class="text-green-600 dark:text-green-400">
                                <strong>Thành viên có hiệu lực đến:</strong> 
                                {{ $user->activeMembership()->end_date->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-red-600 dark:text-red-400">
                                <strong>Thành viên chưa có hiệu lực</strong>
                            </p>
                        @endif
                    </div>

                    <div class="my-6">
                        <h3 class="text-lg font-semibold mb-4">Lịch sử Check-in</h3>
                        
                        @if($check_ins->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400">Không có lịch sử check-in nào.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Ngày</th>
                                            <th class="px-4 py-2 text-left">Thời gian check-in</th>
                                            <th class="px-4 py-2 text-left">Thời gian check-out</th>
                                            <th class="px-4 py-2 text-left">Thời gian tập luyện</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($check_ins as $check_in)
                                            <tr>
                                                <td class="border px-4 py-2">{{ $check_in->date->format('d/m/Y') }}</td>
                                                <td class="border px-4 py-2">{{ $check_in->check_in_time->format('H:i:s') }}</td>
                                                <td class="border px-4 py-2">
                                                    @if($check_in->check_out_time)
                                                        {{ $check_in->check_out_time->format('H:i:s') }}
                                                    @else
                                                        <span class="text-blue-600 dark:text-blue-400">Chưa check-out</span>
                                                    @endif
                                                </td>
                                                <td class="border px-4 py-2">
                                                    @if($check_in->check_out_time)
                                                        @php
                                                            $duration = $check_in->check_in_time->diff($check_in->check_out_time);
                                                            $hours = $duration->h;
                                                            $minutes = $duration->i;
                                                        @endphp
                                                        {{ $hours }} giờ {{ $minutes }} phút
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                {{ $check_ins->links() }}
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Trở về Dashboard') }}
                        </a>
                        
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('qrcode.show', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ml-4">
                                {{ __('Xem QR Code') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>