@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'disabled' => false,
    'fullWidth' => false,
])

@php
    // Định nghĩa các biến thể của button
    $variants = [
        'primary' => 'bg-gray-800 text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-indigo-500',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 focus:bg-gray-50 active:bg-gray-100 border-gray-300 focus:ring-indigo-500',
        'success' => 'bg-green-600 text-white hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:ring-green-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'bg-blue-600 text-white hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:ring-blue-500',
    ];
    
    // Định nghĩa các kích thước của button
    $sizes = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-4 py-2 text-xs',
        'lg' => 'px-6 py-3 text-sm',
    ];
    
    // Xây dựng các lớp CSS
    $baseClasses = 'inline-flex items-center border border-transparent rounded-md font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';
    $variantClasses = $variants[$variant] ?? $variants['primary'];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
    $widthClasses = $fullWidth ? 'w-full justify-center' : '';
    
    $classes = "{$baseClasses} {$variantClasses} {$sizeClasses} {$disabledClasses} {$widthClasses}";
    
    // Điều chỉnh padding nếu có icon
    if ($icon) {
        if ($iconPosition === 'left') {
            $classes .= ' pl-3';
        } else {
            $classes .= ' pr-3';
        }
    }
@endphp

<button 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }} mr-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }} ml-2"></i>
    @endif
</button>