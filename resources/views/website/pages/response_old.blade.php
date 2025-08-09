<section class="meal-section py-8 max-w-full mx-auto px-4 sm:px-6 md:px-8">
    <h1
        class="text-3xl sm:text-4xl text-center font-semibold mb-7 flex flex-col sm:flex-row gap-4 justify-center items-center">
        <img class="h-14 w-14" src="{{ asset('assets/images/chef-bot.png') }}" alt="">
        AI Prepared Meal Plan
    </h1>

    <!-- Goal Summary -->
    @if (isset($jsonData['goal']))
        <div class="border-2 border-green-500/70 rounded-xl p-6 sm:p-8 max-w-full mb-8 bg-green-50"
            style="box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);">
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
                {{-- <p class="text-gray-700">Target Calories: {{ $jsonData['target_calories'] ?? 'Not specified' }} per day
                </p> --}}
            </div>
        </div>
    @endif

    <!-- Progress Overview -->
    @if (isset($jsonData['meals']) && count($jsonData['meals']) > 0)
        <div class="border-2 border-[#7F5AF0]/70 rounded-xl p-6 sm:p-8 mb-8 max-w-full" 
            style="box-shadow: 0 0 10px rgb(127, 90, 240);">
            <h2 class="text-2xl sm:text-3xl font-semibold text-[#7F5AF0] mb-4 flex items-center gap-3">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Overview Meal Plan
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-[#7F5AF0] to-purple-600 text-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold">{{ $jsonData['current_day'] ?? 1 }}</div>
                    <div class="text-sm opacity-90">Day</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold">{{ number_format($jsonData['total_meals'] ?? []) }}</div>
                    <div class="text-sm opacity-90">Meals</div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold">{{ number_format($jsonData['total_calories'] ?? 0) }}</div>
                    <div class="text-sm opacity-90">Total Calories</div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold">{{ number_format($jsonData['total_price'] ?? 0) }} AED</div>
                    <div class="text-sm opacity-90">Total Cost</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Single Day Meal Plan -->
    @if (isset($jsonData['meals']) && count($jsonData['meals']) > 0)


        <!-- Daily Summary -->
        {{-- <div
                class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 p-4 bg-white/50 rounded-lg border border-[#7F5AF0]/30">
                <div class="text-center">
                    <div class="text-sm text-gray-600">Calories</div>
                    <div class="font-semibold text-[#7F5AF0]">
                        {{ number_format($jsonData['daily_total']['calories'] ?? 0) }}
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Protein</div>
                    <div class="font-semibold text-green-600">
                        {{ number_format($jsonData['daily_total']['protein'] ?? 0, 1) }}g
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Carbs</div>
                    <div class="font-semibold text-orange-600">
                        {{ number_format($jsonData['daily_total']['carbs'] ?? 0, 1) }}g
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Fat</div>
                    <div class="font-semibold text-red-600">
                        {{ number_format($jsonData['daily_total']['fat'] ?? 0, 1) }}g
                    </div>
                </div>
            </div> --}}

        <!-- Meals for this day -->
        <div class="flex flex-col gap-6">
            @foreach ($jsonData['meals'] as $index => $meals)
                <div class="border-2 border-[#7F5AF0]/70 rounded-xl p-6 sm:p-8 mb-8 max-w-full w-full"
                    style="box-shadow: 0 0 10px rgb(127, 90, 240);">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">

                        <h2 class="text-2xl sm:text-3xl font-semibold text-[#7F5AF0] flex items-center gap-3">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-[#7F5AF0] text-white rounded-full flex items-center justify-center font-bold">
                                {{ $index + 1 }}
                            </div>
                            Day {{ $index + 1 }}
                            {{-- {{ print_r($meals)}} --}}
                        </h2>

                        <div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">Daily Cost</div>
                                <div class="text-lg font-semibold text-[#7F5AF0]">
                                    {{ number_format($jsonData['daily_total'][$index]['price'] ?? 0) }} AED
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">Daily Total</div>
                                <div class="text-lg font-semibold text-[#7F5AF0]">
                                    {{ number_format($jsonData['daily_total'][$index]['calories'] ?? 0) }} cal
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="flex  flex-col gap-6">
                        @foreach ($meals as $meal)
                            <div
                                class="bg-white/30 border-2 border-[#7F5AF0]/50 rounded-xl p-5 hover:shadow-lg transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-semibold capitalize mb-1">
                                            {{ $meal['type'] ?? '' }}
                                            @if (isset($meal['time']))
                                                <span
                                                    class="text-sm text-gray-600 font-normal ml-2">{{ $meal['time'] }}</span>
                                            @endif
                                        </h3>
                                        <h4 class="text-[#7F5AF0] font-semibold">{{ $meal['name'] ?? '' }}
                                            {{ isset($meal['quantity']) ? '(' . $meal['quantity'] . ')' : '' }}</h4>
                                        <h4 class="text-white text-sm font-normal">{{ $meal['portion_notes'] ?? '' }}
                                        </h4>
                                    </div>
                                    <div class="text-right mt-2 sm:mt-0">
                                        <div class="text-lg font-bold text-[#7F5AF0]">
                                            {{ $meal['total_price'] ?? 0 }} AED</div>
                                        <div class="text-lg font-bold text-[#7F5AF0]">
                                            {{ $meal['total_cal'] ?? 0 }} cal</div>
                                    </div>
                                </div>

                                <!-- Ingredients Table -->
                                @if (count($meal['ingredients'] ?? []) > 0)
                                    <h5 class="text-base sm:text-lg font-semibold mt-4 mb-2 text-gray-800">Ingredients:
                                    </h5>
                                    <div class="max-w-full overflow-x-auto mb-4">
                                        <table
                                            class="w-full text-sm border-collapse border border-[#7F5AF0]/50 rounded-lg overflow-hidden">
                                            <thead class="bg-[#7F5AF0]/20">
                                                <tr class="text-left">
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30">Name</th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Amount
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Price
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Calories
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Protein
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">Carbs
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">Fat
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white/50">
                                                @if (is_array($meal['ingredients'] ?? null))
                                                    @foreach ($meal['ingredients'] as $ingredient)
                                                        @if (is_array($ingredient))
                                                            <tr class="hover:bg-white/70">
                                                                <td class="px-3 py-2">
                                                                    {{ $ingredient['name'] ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['amount'] ?? 'N/A' }}</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['price'] ?? 0 }} AED</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['cal'] ?? 0 }}
                                                                </td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['protein'] ?? 0 }}g</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['carbs'] ?? 0 }}g</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    {{ $ingredient['fat'] ?? 0 }}g</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6"
                                                            class="px-3 py-2 text-center text-gray-600 italic">
                                                            No structured ingredients found for this meal.
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <!-- Sauces Table -->
                                @if (count($meal['sauces'] ?? []) > 0)
                                    <h5 class="text-base sm:text-lg font-semibold mb-2 text-gray-800">Sauces &
                                        Seasonings:
                                    </h5>
                                    <div class="max-w-full overflow-x-auto mb-4">
                                        <table
                                            class="w-full text-sm border-collapse border border-[#7F5AF0]/50 rounded-lg overflow-hidden">
                                            <thead class="bg-[#7F5AF0]/20">
                                                <tr class="text-left">
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30">Name</th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Amount
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Price
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Calories
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">
                                                        Protein
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">Carbs
                                                    </th>
                                                    <th class="px-3 py-2 border-b border-[#7F5AF0]/30 text-center">Fat
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white/50">
                                                @foreach ($meal['sauces'] ?? [] as $sauce)
                                                    @if (is_array($sauce))
                                                        <tr class="hover:bg-white/70">
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 font-medium">
                                                                {{ $sauce['name'] ?? 'N/A' }}</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center">
                                                                {{ $sauce['amount'] ?? 'N/A' }}</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center">
                                                                {{ $sauce['price'] ?? '0' }} AED</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center">
                                                                {{ $sauce['cal'] ?? 0 }}</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center text-green-600 font-semibold">
                                                                {{ $sauce['protein'] ?? 0 }}g</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center text-orange-600 font-semibold">
                                                                {{ $sauce['carbs'] ?? 0 }}g</td>
                                                            <td
                                                                class="px-3 py-2 border-b border-[#7F5AF0]/20 text-center text-red-600 font-semibold">
                                                                {{ $sauce['fat'] ?? 0 }}g</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="6"
                                                                class="px-3 py-2 text-center text-gray-600 italic">
                                                                Invalid sauce data: {{ $sauce }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600 mb-4 italic">No sauces or seasonings used in this
                                        meal
                                    </p>
                                @endif

                                <!-- Cooking Instructions -->
                                @if (isset($meal['instructions']))
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                        <h5
                                            class="text-base font-semibold text-green-700 mb-2 flex items-center gap-2">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Cooking Instructions:
                                        </h5>
                                        <p class="text-green-800 leading-relaxed">
                                            {{ $meal['instructions'] ?? 'No specific instructions provided.' }}</p>
                                    </div>
                                @endif

                                <!-- Meal Totals -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="text-center border border-[#7F5AF0]/50 rounded-lg p-3 bg-white/60">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Calories</div>
                                        <div class="text-lg font-bold text-[#7F5AF0]">{{ $meal['total_cal'] ?? 0 }}
                                        </div>
                                    </div>
                                    <div class="text-center border border-green-500/50 rounded-lg p-3 bg-green-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Protein</div>
                                        <div class="text-lg font-bold text-green-600">
                                            {{ $meal['total_protein'] ?? 0 }}g
                                        </div>
                                    </div>
                                    <div class="text-center border border-orange-500/50 rounded-lg p-3 bg-orange-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Carbs</div>
                                        <div class="text-lg font-bold text-orange-600">
                                            {{ $meal['total_carbs'] ?? 0 }}g
                                        </div>
                                    </div>
                                    <div class="text-center border border-red-500/50 rounded-lg p-3 bg-red-50">
                                        <div class="text-xs text-gray-600 uppercase tracking-wide">Fat</div>
                                        <div class="text-lg font-bold text-red-600">{{ $meal['total_fat'] ?? 0 }}g
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (isset($jsonData[$index]['adjustments_made']))
                                <span class="text-red-500">{{ $jsonData[$index]['adjustments_made'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No meal plan generated yet</h3>
            <p class="mt-2 text-gray-600">Click "Generate Meal Plan" to start creating your personalized meal plan.</p>
        </div>
    @endif
</section>
