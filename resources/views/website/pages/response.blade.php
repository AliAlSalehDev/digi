{{--@php--}}
{{--    $jsonData = [--}}
{{--        'goal' => 'Gain',--}}
{{--        'current_day' => 7,--}}
{{--        'total_meals' => 21,--}}
{{--        'total_calories' => 14700,--}}
{{--        'total_price' => 350.75,--}}
{{--        'daily_total' => [--}}
{{--            ['price' => 50.25, 'calories' => 2100],--}}
{{--            ['price' => 49.80, 'calories' => 2100],--}}
{{--            ['price' => 48.00, 'calories' => 2100],--}}
{{--            ['price' => 50.00, 'calories' => 2100],--}}
{{--            ['price' => 49.70, 'calories' => 2100],--}}
{{--            ['price' => 52.00, 'calories' => 2100],--}}
{{--            ['price' => 51.00, 'calories' => 2100],--}}
{{--        ],--}}
{{--        'meals' => array_map(fn ($day) => [--}}
{{--            [--}}
{{--                'type' => 'Breakfast',--}}
{{--                'time' => '10:00 AM',--}}
{{--                'name' => 'Chicken & Rice',--}}
{{--                'quantity' => '1 serving',--}}
{{--                'portion_notes' => 'Served warm',--}}
{{--                'total_price' => 18.50,--}}
{{--                'total_cal' => 700,--}}
{{--                'total_protein' => 40,--}}
{{--                'total_carbs' => 50,--}}
{{--                'total_fat' => 20,--}}
{{--                'instructions' => 'Heat rice and chicken in microwave for 2 minutes.',--}}
{{--                'ingredients' => [--}}
{{--                    ['name' => 'Chicken Breast', 'amount' => '150g', 'price' => 10, 'cal' => 300, 'protein' => 35, 'carbs' => 0, 'fat' => 10],--}}
{{--                    ['name' => 'White Rice', 'amount' => '200g', 'price' => 5, 'cal' => 250, 'protein' => 5, 'carbs' => 45, 'fat' => 2],--}}
{{--                ],--}}
{{--                'sauces' => [--}}
{{--                    ['name' => 'Soy Sauce', 'amount' => '15ml', 'price' => 0.5, 'cal' => 20, 'protein' => 1, 'carbs' => 2, 'fat' => 0],--}}
{{--                ]--}}
{{--            ],--}}
{{--            [--}}
{{--                'type' => 'Lunch',--}}
{{--                'time' => '1:30 PM',--}}
{{--                'name' => 'Grilled Salmon & Quinoa',--}}
{{--                'quantity' => '1 plate',--}}
{{--                'portion_notes' => '',--}}
{{--                'total_price' => 20.00,--}}
{{--                'total_cal' => 800,--}}
{{--                'total_protein' => 45,--}}
{{--                'total_carbs' => 40,--}}
{{--                'total_fat' => 25,--}}
{{--                'instructions' => 'Grill salmon and serve with steamed quinoa.',--}}
{{--                'ingredients' => [--}}
{{--                    ['name' => 'Salmon Fillet', 'amount' => '150g', 'price' => 12, 'cal' => 400, 'protein' => 40, 'carbs' => 0, 'fat' => 20],--}}
{{--                    ['name' => 'Quinoa', 'amount' => '100g', 'price' => 5, 'cal' => 200, 'protein' => 5, 'carbs' => 35, 'fat' => 3],--}}
{{--                ],--}}
{{--                'sauces' => []--}}
{{--            ],--}}
{{--            [--}}
{{--                'type' => 'Dinner',--}}
{{--                'time' => '9:00 PM',--}}
{{--                'name' => 'Greek Yogurt with Nuts',--}}
{{--                'quantity' => '1 bowl',--}}
{{--                'portion_notes' => 'Cold',--}}
{{--                'total_price' => 11.75,--}}
{{--                'total_cal' => 600,--}}
{{--                'total_protein' => 30,--}}
{{--                'total_carbs' => 20,--}}
{{--                'total_fat' => 25,--}}
{{--                'instructions' => 'Mix yogurt and nuts in a bowl.',--}}
{{--                'ingredients' => [--}}
{{--                    ['name' => 'Greek Yogurt', 'amount' => '200g', 'price' => 6, 'cal' => 250, 'protein' => 20, 'carbs' => 10, 'fat' => 5],--}}
{{--                    ['name' => 'Mixed Nuts', 'amount' => '50g', 'price' => 5.75, 'cal' => 350, 'protein' => 10, 'carbs' => 10, 'fat' => 20],--}}
{{--                ],--}}
{{--                'sauces' => []--}}
{{--            ]--}}
{{--        ], range(1, 7))--}}
{{--    ];--}}
{{--@endphp--}}

<section
    class="meal-section py-8 max-w-full mx-auto px-4 sm:px-6 md:px-8"
    x-data="{
    tab: Number(localStorage.getItem('activeTab') ?? {{ $jsonData['current_day'] - 1 }}),
    setTab(i) {
        this.tab = i;
        localStorage.setItem('activeTab', i);
    }
}"
>
    <h1 class="text-3xl sm:text-4xl text-gray-800 text-center font-semibold mb-7 flex flex-col sm:flex-row gap-4 justify-center items-center">
{{--        <img class="h-14 w-14" src="{{ asset('assets/images/chef-bot.png') }}" alt="">--}}
        AI Prepared Meal Plan
    </h1>

    <!-- Two Cards Row: Goal Summary + Overview -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8">
        <!-- Goal Summary -->
        <div class="flex-1 bg-white border border-gray-300 shadow rounded-xl p-6 sm:p-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-green-600 mb-4 flex items-center gap-3">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Your Fitness Goal
            </h2>
            <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-lg font-semibold text-gray-800 mb-2">
                    Goal: <span class="text-green-600 capitalize">{{ $jsonData['goal'] }} Weight</span>
                </p>
            </div>
        </div>

        <!-- Overview -->
        <div class="flex-1 bg-white border border-gray-300 shadow rounded-xl p-6 sm:p-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-green-600 mb-4 flex items-center gap-3">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Overview Meal Plan
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-[#34d399] to-[#10b981] text-white rounded-lg p-4 text-center shadow-md">
                    <div class="text-xl font-bold">{{ $jsonData['current_day'] }}</div>
                    <div class="text-sm opacity-90">Day</div>
                </div>

                <div class="bg-gradient-to-r from-[#6366f1] to-[#8b5cf6] text-white rounded-lg p-4 text-center shadow-md">
                    <div class="text-xl font-bold">{{ number_format($jsonData['total_meals']) }}</div>
                    <div class="text-sm opacity-90">Meals</div>
                </div>

                <div class="bg-gradient-to-r from-[#f97316] to-[#ef4444] text-white rounded-lg p-4 text-center shadow-md">
                    <div class="text-xl font-bold">{{ number_format($jsonData['total_calories']) }}</div>
                    <div class="text-sm opacity-90">Total Calories</div>
                </div>

                <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg p-4 text-center shadow-md">
                    <div class="text-xl font-bold">{{ number_format($jsonData['total_price']) }} AED</div>
                    <div class="text-sm opacity-90">Total Cost</div>
                </div>

            </div>
        </div>
    </div>


    <!-- Tabs Navigation -->
    <div class="flex flex-wrap gap-2 justify-center mb-6">
        @foreach ($jsonData['meals'] as $index => $meals)
            <button
                @click="setTab({{ $index }})"
                class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 cursor-pointer"
                :class="tab === {{ $index }} ? 'bg-[#69CEBE] text-white' : 'bg-gray-100 text-gray-700 hover:bg-[#69CEBE]/20'"
            >
                Day {{ $index + 1 }}
            </button>
        @endforeach
    </div>

    <!-- Tabs Content -->
    <div class="relative">
        @foreach ($jsonData['meals'] as $index => $meals)
            <div x-show="tab === {{ $index }}" x-cloak class="w-full">
                <div class="bg-white border border-gray-300 shadow-sm rounded-xl p-6 sm:p-8 mb-8 max-w-full w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <h2 class="text-2xl sm:text-3xl font-semibold text-[#69CEBE] flex items-center gap-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-[#69CEBE] text-white rounded-full flex items-center justify-center font-bold">
                                {{ $index + 1 }}
                            </div>
                            Day {{ $index + 1 }}
                        </h2>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Daily Cost</div>
                            <div class="text-lg font-semibold text-[#69CEBE]">
                                {{ number_format($jsonData['daily_total'][$index]['price']) }} AED
                            </div>
                            <div class="text-sm text-gray-600">Daily Total</div>
                            <div class="text-lg font-semibold text-[#69CEBE]">
                                {{ number_format($jsonData['daily_total'][$index]['calories']) }} cal
                            </div>
                        </div>
                    </div>

                    <!-- Meals -->
                    <div class="flex flex-col gap-6">
                        @foreach ($meals as $meal)
                            <div class="bg-white/30 border-2 border-[#69CEBE]/50 rounded-xl p-5 hover:shadow-lg transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-semibold capitalize mb-1">
                                            {{ $meal['type'] ?? '' }}
                                            @if (isset($meal['time']))
                                                <span class="text-sm text-gray-600 font-normal ml-2">{{ $meal['time'] }}</span>
                                            @endif
                                        </h3>
                                        <h4 class="text-[#69CEBE] font-semibold">{{ $meal['name'] ?? '' }}
                                            {{ isset($meal['quantity']) ? '(' . $meal['quantity'] . ')' : '' }}</h4>
                                        <h4 class="text-white text-sm font-normal">{{ $meal['portion_notes'] ?? '' }}</h4>
                                    </div>
                                    <div class="text-right mt-2 sm:mt-0">
                                        <div class="text-lg font-bold text-[#69CEBE]">{{ $meal['total_price'] ?? 0 }} AED</div>
                                        <div class="text-lg font-bold text-[#69CEBE]">{{ $meal['total_cal'] ?? 0 }} cal</div>
                                    </div>
                                </div>

                                <!-- Ingredients Table -->
                                @if (!empty($meal['ingredients']))
                                    <h5 class="text-base sm:text-lg font-semibold mt-4 mb-2 text-gray-800">Ingredients:</h5>
                                    <div class="overflow-x-auto mb-4">
                                        <table class="w-full text-sm border-collapse border border-[#69CEBE]/50 overflow-hidden">
                                            <thead class="bg-[#69CEBE]/20">
                                            <tr>
                                                <th class="px-3 py-2">Name</th>
                                                <th class="px-3 py-2 text-center">Amount</th>
                                                <th class="px-3 py-2 text-center">Price</th>
                                                <th class="px-3 py-2 text-center">Calories</th>
                                                <th class="px-3 py-2 text-center">Protein</th>
                                                <th class="px-3 py-2 text-center">Carbs</th>
                                                <th class="px-3 py-2 text-center">Fat</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($meal['ingredients'] as $ingredient)
                                                <tr class="hover:bg-white/70">
                                                    <td class="px-3 py-2">{{ $ingredient['name'] }}</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['amount'] }}</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['price'] }} AED</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['cal'] }}</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['protein'] }}g</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['carbs'] }}g</td>
                                                    <td class="px-3 py-2 text-center">{{ $ingredient['fat'] }}g</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <!-- Macros -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="text-center border border-[#69CEBE]/50 rounded-lg p-3 bg-white-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Calories</div>
                                        <div class="text-lg font-bold text-[#69CEBE]">{{ $meal['total_cal'] }}</div>
                                    </div>
                                    <div class="text-center border border-green-500/50 rounded-lg p-3 bg-green-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Protein</div>
                                        <div class="text-lg font-bold text-green-600">{{ $meal['total_protein'] }}g</div>
                                    </div>
                                    <div class="text-center border border-orange-500/50 rounded-lg p-3 bg-orange-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Carbs</div>
                                        <div class="text-lg font-bold text-orange-600">{{ $meal['total_carbs'] }}g</div>
                                    </div>
                                    <div class="text-center border border-red-500/50 rounded-lg p-3 bg-red-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Fat</div>
                                        <div class="text-lg font-bold text-red-600">{{ $meal['total_fat'] }}g</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

