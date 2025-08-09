<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/images/24digi.png') }}" type="image/x-icon">
    <title>@yield('title', '24 Digi - Your Personalized Nutrition Journey')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #16161a;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #7F5AF0, #5C4CE6);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(127, 90, 240, 0.3);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #8F6BFF, #6D53F7);
            box-shadow: 0 0 15px rgba(127, 90, 240, 0.5);
        }

        /* Base Styles */
        body {
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* Background Animation */
        @keyframes moveGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient {
            background-size: 200% 200%;
            animation: moveGradient 15s ease infinite;
        }

        /* Glow Effect */
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(127, 90, 240, 0.5),
                           0 0 40px rgba(127, 90, 240, 0.3),
                           inset 0 0 20px rgba(127, 90, 240, 0.1);
            }
            50% {
                box-shadow: 0 0 30px rgba(127, 90, 240, 0.7),
                           0 0 60px rgba(127, 90, 240, 0.4),
                           inset 0 0 30px rgba(127, 90, 240, 0.2);
            }
        }

        .glow-effect {
            animation: pulse-glow 3s ease-in-out infinite;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-surface-base text-text-primary overflow-x-hidden custom-scrollbar">
    <!-- Background Effects -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-surface-base via-primary-950/10 to-surface-base"></div>
        <div class="absolute inset-0 bg-gradient-mesh opacity-20"></div>
        <div class="absolute inset-0 bg-dots-pattern opacity-5"></div>
    </div>

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-300 shadow-sm">
        <div class="container mx-auto px-6 lg:px-[10%]">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('assets/images/24digi.png') }}" alt="24 Digi"
                         class="h-20 w-auto sm:h-24 animate-float">
                    <span class="text-2xl sm:text-3xl font-bold font-display text-gradient">24 Digi</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-text-secondary hover:text-primary-400 transition-colors duration-200 font-medium">Home</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-text-secondary hover:text-primary-400 transition-colors duration-200 font-medium">Login</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-text-secondary hover:text-primary-400 focus:outline-none focus-visible-ring">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative pb-15 bg-gray-100">
        <div class="container mx-auto px-6 lg:px-[10%] py-8 lg:py-12">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative bg-surface-elevated/90 backdrop-blur-sm">
        <div class="container mx-auto px-6 lg:px-[10%] pt-8 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('assets/images/24digi.png') }}" alt="Logo" class="h-10 w-10">
                        <span class="text-lg font-bold font-display">24 Digi</span>
                    </div>
                    <p class="text-text-muted text-sm">Your personalized nutrition journey starts here. AI-powered meal planning for your fitness goals.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4 text-primary-400">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="text-gray-600 hover:text-black transition-colors">Home</a></li>
                        <li><a href="/generate-meal" class="text-gray-600 hover:text-black transition-colors">Generate Meal Plan</a></li>
                        <li><a href="/about" class="text-gray-600 hover:text-black transition-colors">About Us</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-semibold mb-4 text-primary-400">Get in Touch</h4>
                    <p class="text-gray-600 text-sm">Have questions? We're here to help you achieve your fitness goals.</p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-600 hover:text-black transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-black transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-300 border-surface-700/50 text-center text-sm text-gray-700">
                <p>&copy; {{ date('Y') }} 24 Digi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('script')
</body>

</html>
