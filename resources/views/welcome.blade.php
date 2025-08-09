@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-semibold mb-5">
        User Data
    </h1>
    <form action="" method="post">
        @csrf
        <div class="grid grid-cols-3 gap-4">
            <div class="mb-3">
                <label for="age" class="form-label">
                    Age
                </label>
                <input type="text" name="age" id="age" value="{{ old('age') }}"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] active:ring-[#7F5AF0] rounded p-2">
                @error('age')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="" class="form-label">
                    Height
                </label>
                <input type="text" name="height"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] active:ring-[#7F5AF0] rounded p-2">
                @error('height')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">
                    Weight
                </label>
                <input type="text" name="weight" id="weight"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] active:ring-[#7F5AF0] rounded p-2">
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
                <select name="gender" id="gender"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] active:ring-[#7F5AF0] rounded p-2">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
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
                <select name="activity_level" id="activity_level"
                    class="mt-2 block w-full border border-[#7F5AF0] active:ring-[#7F5AF0] focus:ring-[#7F5AF0] focus:border-[#7F5AF0] active:ring-[#7F5AF0] rounded p-2">
                    <option value="1/10">1/10</option>
                    <option value="2/10">2/10</option>
                    <option value="3/10">3/10</option>
                    <option value="4/10">4/10</option>
                    <option value="5/10">5/10</option>
                    <option value="6/10">6/10</option>
                    <option value="7/10">7/10</option>
                    <option value="8/10">8/10</option>
                    <option value="9/10">9/10</option>
                    <option value="10/10">10/10</option>
                </select>
                @error('activity_level')
                    <span class="text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button
                class="mt-2 cursor-pointer py-2 px-8 font-semibold bg-[#7F5AF0] border border-[#7F5AF0] rounded-lg hover:bg-transparent text-white">
                Save
            </button>
        </div>
    </form>
@endsection
