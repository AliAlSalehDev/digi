<x-app-layout>
    <div class="flex justify-between">
        <p class="text-3xl font-semibold">
            {{ __('Meals') }}
        </p>
        <div>
            {{-- <button>
                Create
            </button> --}}
            <button
                class="show-modal mt-2 cursor-pointer py-2 px-8 font-semibold bg-[#7F5AF0] border-2 border-[#7F5AF0] rounded-lg hover:!bg-[#0C0719] text-white">
                Import
            </button>
        </div>
    </div>

    <div class="rounded-2xl overflow-hidden mt-7">
        <table class="w-full rounded p-5">
            <thead class=" bg-white border-b">
                <tr>
                    <th class="p-3 text-[16px]">#</th>
                    <th class="p-2 text-[16px]">Name</th>
                    <th class="p-2 text-[16px]">Unit</th>
                    <th class="p-2 text-[16px]">Price</th>
                    <th class="p-2 text-[16px]">Calories</th>
                    <th class="p-2 text-[16px]">Protein</th>
                    <th class="p-2 text-[16px]">Carbs</th>
                    <th class="p-2 text-[16px]">Fats Per 100g</th>
                </tr>
            </thead>
            <tbody class="bg-[#7F5AF0]/10">
                @forelse ($meals as $index => $meal)
                    <tr>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center">{{ $index + 1 }}</td>
                        <td class="border-b !border-gray-400 p-3 text-[14px]">{{ $meal->name }}</td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->unit }} </td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->price }} </td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->calories }} </td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->protein }} </td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->carbs }} </td>
                        <td class="border-b !border-gray-400 p-3 text-[14px] text-center"> {{ $meal->fats }} </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <p class="text-red-500 text-center">
                                No meals found
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{$meals->links()}}
        </div>
    </div>

    @push('script')
        <script>
            jQuery(document).ready(function() {
                jQuery('.show-modal').click(function(e) {
                    jQuery('#import-modal').toggleClass('hidden');
                });

                jQuery('#import-modal').click(function() {
                    jQuery(this).addClass('hidden');
                });

                jQuery('#import-modal > div').click(function(e) {
                    e.stopPropagation();
                });
            });
        </script>
    @endpush

</x-app-layout>
<div id="import-modal" class="hidden h-full bg-black/50 w-full fixed top-0 z-100 flex justify-center items-center">
    <div class="w-xl p-10 bg-white rounded-xl relative">
        <h1 class="text-2xl font-semibold text-center mb-7">
            Import Meals
        </h1>
        <form method="POST" action="{{ route('meals.store') }}" enctype="multipart/form-data">

            @csrf <div class="mb-3">
                <div class="flex justify-between">
                    <label for="age" class="form-label">
                        File
                    </label>
                    <a class="font-semibold text-[#7F5AF0] hover:underline"
                        href="{{ asset('assets/meal-template-sheet.csv') }}">
                        Download template
                    </a>
                </div>
                <input type="file" accept=".csv,.json" name="file" id="file"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] rounded-lg p-2">
                @error('age')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            <button
                class="mt-2 cursor-pointer py-2 px-8 font-semibold bg-[#7F5AF0] border-2 border-[#7F5AF0] rounded-lg hover:!bg-[#0C0719] text-white">
                Import
            </button>
        </form>

        <button
            class="show-modal h-[25px] w-[25px] text-[10px] rounded-full border !border-red-500 !bg-red-500 hover:!bg-white text-white hover:!text-red-500 flex justify-center items-center absolute top-5 right-5">
            <svg class="h-[20px] w-[20px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M10.9393 12L6.9696 15.9697L8.03026 17.0304L12 13.0607L15.9697 17.0304L17.0304 15.9697L13.0607 12L17.0303 8.03039L15.9696 6.96973L12 10.9393L8.03038 6.96973L6.96972 8.03039L10.9393 12Z"
                    fill="currentColor" />
            </svg>
        </button>
    </div>
</div>
