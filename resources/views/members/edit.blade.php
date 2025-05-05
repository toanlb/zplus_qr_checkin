<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Member') }}: {{ $member->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('members.update', $member) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mt-4">
                            <x-label for="name" :value="__('Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $member->name)" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-label for="email" :value="__('Email')" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $member->email)" required />
                        </div>

                        <!-- Phone -->
                        <div class="mt-4">
                            <x-label for="phone" :value="__('Phone')" />
                            <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $member->phone)" required />
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <x-label for="address" :value="__('Address')" />
                            <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $member->address)" />
                        </div>

                        <!-- Birth Date -->
                        <div class="mt-4">
                            <x-label for="birth_date" :value="__('Birth Date')" />
                            <x-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date', $member->birth_date ? $member->birth_date->format('Y-m-d') : '')" />
                        </div>

                        <!-- Member Type -->
                        <div class="mt-4">
                            <x-label for="member_type" :value="__('Member Type')" />
                            <select id="member_type" name="member_type" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full">
                                <option value="regular" {{ (old('member_type', $member->member_type) == 'regular') ? 'selected' : '' }}>Regular</option>
                                <option value="premium" {{ (old('member_type', $member->member_type) == 'premium') ? 'selected' : '' }}>Premium</option>
                                <option value="vip" {{ (old('member_type', $member->member_type) == 'vip') ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('members.show', $member) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-button>
                                {{ __('Update Member') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>