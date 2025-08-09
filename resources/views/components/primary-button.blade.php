<button {{ $attributes->merge(['type' => 'submit', 'class' => 'py-2 px-4 text-center bg-[#7F5AF0] rounded-md text-white text-sm hover:bg-indigo-500']) }}>
    {{ $slot }}
</button>
