@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'id' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $id = $id ?? $name;
    $classes = 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    
    if ($icon) {
        $classes .= $iconPosition === 'left' ? ' pl-10' : ' pr-10';
    }
@endphp

<div class="relative">
    @if($icon && $iconPosition === 'left')
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="{{ $icon }} text-gray-400"></i>
        </div>
    @endif
    
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $autofocus ? 'autofocus' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
    
    @if($icon && $iconPosition === 'right')
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <i class="{{ $icon }} text-gray-400"></i>
        </div>
    @endif
</div>