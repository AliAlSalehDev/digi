@extends('website.layouts.app')
@section('content')
    <h1 class="text-2xl sm:text-3xl md:text-4xl text-center text-gray-800 font-semibold mb-7">
        Enter User Details
    </h1>
    <section class="bg-white p-5 border border-gray-200 rounded-lg shadow-md">

        <form action="{{ route('user-info') }}" method="post">
            @csrf
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="mb-3">
                    <label for="age" class="form-label">
                        Age
                    </label>
                    <input type="number" min="1" name="age" id="age" value="{{ old('age') }}"
                        placeholder="Enter user age" required
                        class="mt-2 block w-full border border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 active:ring-[#69CEBE] rounded p-2">
                    @error('age')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="height" class="form-label">
                        Height (in centimeters)
                    </label>
                    <input type="number" min="1" step="any" required id="height" name="height" placeholder="Enter user height" value="{{ old('height') }}"
                        class="mt-2 block w-full border border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 active:ring-[#69CEBE] rounded p-2">
                    @error('height')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">
                        Weight (in kilograms)
                    </label>
                    <input type="number" min="1" step="any" required name="weight" id="weight" placeholder="Enter user weight" value="{{ old('weight') }}"
                        class="mt-2 block w-full border border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 active:ring-[#69CEBE] rounded p-2">
                    @error('weight')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">
                        Gender
                    </label>
                    <select name="gender" id="gender" required
                        class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 rounded p-2">
                        <option value="male" @selected(old('gender') == 'male')>Male</option>
                        <option value="female" @selected(old('gender') == 'female')>Female</option>
                    </select>
                    @error('gender')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="activity_level" class="form-label">
                        Activity Level
                    </label>
                    <select name="activity_level" id="activity_level" required
                        class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 active:ring-[#69CEBE] rounded p-2">
                        <option value="Sedentary (little or no exercise)" @selected(old('activity_level') == 'Sedentary (little or no exercise)')>Sedentary (little or no exercise)</option>
                        <option value="Lightly active (1–3 days/week)" @selected(old('activity_level') == 'Lightly active (1–3 days/week)')>Lightly active (1–3 days/week)</option>
                        <option value="Moderately active (3–5 days/week)" @selected(old('activity_level') == 'Moderately active (3–5 days/week)')>Moderately active (3–5 days/week)</option>
                        <option value="Very active (6–7 days/week)" @selected(old('activity_level') == 'Very active (6–7 days/week)')>Very active (6–7 days/week)</option>
                        <option value="Super active (twice/day or physical job)" @selected(old('activity_level') == 'Super active (twice/day or physical job)')>Super active (twice/day or physical job)
                        </option>
                    </select>
                    @error('activity_level')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="waist_circumference" class="form-label">
                        Waist circumference (in inches)
                    </label>
                    <input type="number" min="1" step="any" name="waist_circumference" id="waist_circumference" required  value="{{ old('waist_circumference') }}"
                        class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 rounded p-2">
                    @error('waist_circumference')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="neck_circumference" class="form-label">
                        Neck circumference (in inches)
                    </label>
                    <input type="number" min="1" step="any" name="neck_circumference" id="neck_circumference" required value="{{ old('neck_circumference') }}"
                        class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 rounded p-2">
                    @error('neck_circumference')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="hip_circumference" class="form-label">
                        Hip circumference (in inches)
                    </label>
                    <input type="number" min="1" step="any" name="hip_circumference" id="hip_circumference" value="{{ old('hip_circumference') }}"
                        class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 rounded p-2">
                    @error('hip_circumference')
                        <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="plan_period" class="form-label">
                        Plan Period
                    </label>
                    <select name="plan_period" id="plan_period" required
                            class="mt-2 block w-full border  border-gray-300 active:ring-[#69CEBE] focus:ring-[#69CEBE] focus:border-gray-300 active:ring-[#69CEBE] rounded p-2">
                        <option value="7" @selected(old('plan_period') == '7')>7 Days</option>
                        <option value="30" @selected(old('plan_period') == '30')>30 Days</option>
                        </option>
                    </select>
                    @error('plan_period')
                    <span class="text-red-500">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    class="mt-2 cursor-pointer py-2 px-8 font-semibold bg-[#69CEBE] border border-gray-300 rounded-lg text-white">
                    Next
                </button>
            </div>
        </form>
    </section>
@endsection
