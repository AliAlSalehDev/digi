<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
        <div class="grid sm:grid-cols-3 gap-5">
            <div style="box-shadow: 0 0 30px rgb(127, 90, 240);"
                class="border-2 border-[#7F5AF0] bg-[#0C0719] text-white rounded-xl p-5 text-5xl font-semibold text-center h-[200px] flex justify-center items-center">
                <div class="gap-10">
                    <p class="mb-10 pb-5">
                        Total Ingredients
                    </p>
                    <p class="mt-5">
                        {{$totalIngredients}}
                    </p>
                </div>
            </div>
            <div style="box-shadow: 0 0 30px rgb(127, 90, 240);"
                class="border-2 border-[#7F5AF0] bg-[#0C0719] text-white rounded-xl p-5 text-5xl font-semibold text-center h-[200px] flex justify-center items-center">
                <div class="gap-10">
                    <p class="mb-10 pb-5">
                        Total Sauces
                    </p>
                    <p class="mt-5">
                        {{$totalSauces}}
                    </p>
                </div>
            </div>
            <div style="box-shadow: 0 0 30px rgb(127, 90, 240);"
                class="border-2 border-[#7F5AF0] bg-[#0C0719] text-white rounded-xl p-5 text-5xl font-semibold text-center h-[200px] flex justify-center items-center">
                <div class="gap-10">
                    <p class="mb-10 pb-5">
                        Total Meals
                    </p>
                    <p class="mt-5">
                        {{$totalMeals}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
