@props(['for' => '', 'value' => '', 'required' => false])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500 ml-1">*</span>
    @endif
</label>