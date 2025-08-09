<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    <title>@yield('title', 'Fitness Meal Planner')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <link rel="stylesheet" href="{{ asset('build/assets/app-c3b3d21a.css') }}">
    <script src="{{ asset('build/assets/app-eff04317.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    @stack('head')
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-200 font-roboto">
        @include('layouts.navigation')

        <div class="flex overflow-hidden flex-col flex-1">
            @include('layouts.header')

            <main class="overflow-y-auto overflow-x-hidden flex-1 bg-slate-200">
                <div class="container px-6 py-8 mx-auto">
                    @if (isset($header))
                        <h3 class="mb-4 text-3xl font-medium text-gray-700">
                            {{ $header }}
                        </h3>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('script')
</body>

</html>
