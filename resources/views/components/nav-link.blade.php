@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'flex items-center mt-4 py-2 px-6 mx-3 border border-[#7F5AF0] rounded bg-[#7F5AF0] text-gray-100'
                : 'flex items-center mt-4 py-2 px-6 mx-3 rounded text-gray-100 hover:bg-[#7F5AF0]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $icon ?? '' }}
    <span class="mx-3">{{ $slot }}</span>
</a>
