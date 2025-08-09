@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block mt-1 w-full rounded-md form-input focus:border-[#7F5AF0]']) !!}>
