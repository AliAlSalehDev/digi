<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    <title>@yield('title', 'Fitness Meal Planner')</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app-c3b3d21a.css') }}">
    <script src="{{ asset('build/assets/app-eff04317.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <div class="flex justify-center items-center h-screen bg-[#0C0719]  px-6">
        <div class="p-6 max-w-sm w-full bg-white shadow-md rounded-md">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
