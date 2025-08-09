@extends('website.layouts.app')
@section('content')
    @push('styles')
        <style>
            .loader {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #69CEBE, #3ABFAE);
                position: relative;
                animation: rotate 1s linear infinite;
                box-shadow: 0 0 10px #69CEBE80;
            }

            .loader::before {
                content: '';
                position: absolute;
                inset: 5px;
                background: #ffffff;
                border-radius: 50%;
                z-index: 2;
            }

            .loader::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 50%;
                background: linear-gradient(135deg, #69CEBE, transparent 40%);
                animation: rotate 1s linear infinite;
                z-index: 1;
            }

            @keyframes rotate {
                to {
                    transform: rotate(360deg);
                }
            }
            @keyframes bounceSlow {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }

            .animate-bounce-slow {
                animation: bounceSlow 1.5s infinite;
            }

            .progress-bar {
                transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .stat-value {
                font-size: 1.875rem;
                font-weight: 700;
                letter-spacing: -0.025em;
            }

            .stat-label {
                font-size: 0.875rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
        </style>
    @endpush

    <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold font-display mb-4">
            <span class="text-gray-800">Your Fitness Results</span>
        </h1>
        <p class="text-gray-600 text-lg font-medium">Personalized calculations based on your profile</p>
    </div>

    <section class="grid md:grid-cols-2 gap-8 mb-10">
        <!-- Fitness Calculations Card -->
        <div class="stat-card rounded-2xl bg-white shadow p-8 animate-slide-up">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold font-display text-text-primary mb-2">
                        Fitness Calculations
                    </h2>
                    <p class="text-sm text-text-muted">Your body composition metrics</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-primary-500/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-surface-base p-5">
                    <span class="stat-label text-text-muted block mb-2">BMI Status</span>
                    @php
                        $bmi_class = "";
                        $bmi_status = session('user-info')['bmi_overview'];
                        if($bmi_status == "Underweight" || $bmi_status == "Overweight")
                        $bmi_class = "text-red-500";
                        elseif($bmi_status == "Normal")
                        $bmi_class = "text-green-600";
                        else
                            $bmi_class = "text-primary-500";
                    @endphp
                    <span class="stat-value {{$bmi_class}}">{{ session('user-info')['bmi_overview'] ?? 'N/A' }}</span>
                </div>
                <div class="bg-surface-base p-5">
                    <span class="stat-label text-text-muted block mb-2">Body Fat Percentage</span>
                    <span
                        class="stat-value text-primary-500">{{ session('user-info')['body_fat'] ? number_format(session('user-info')['body_fat'], 1) . '%' : '25%' }}</span>
                </div>
            </div>
        </div>

        <!-- Macronutrient Breakdown Card -->
        <div class="stat-card rounded-2xl bg-white shadow p-8 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold font-display text-text-primary mb-2">
                        Energy Requirements
                    </h2>
                    <p class="text-sm text-text-muted">Daily caloric needs</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-accent-green/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-surface-base rounded-xl p-5">
                    <span class="stat-label text-text-muted block mb-2">BMR (Basal Metabolic Rate)</span>
                    <span class="stat-value text-accent-green">{{ session('user-info')['bmr'] ?? '0' }}</span>
                    <span class="text-sm text-text-muted">calories/day</span>
                </div>
                <div class="bg-surface-base rounded-xl p-5">
                    <span class="stat-label text-text-muted block mb-2">TDEE (Total Daily Energy)</span>
                    <span class="stat-value text-accent-green">{{ session('user-info')['tdee'] ?? '0' }}</span>
                    <span class="text-sm text-text-muted">calories/day</span>
                </div>
                @if (session('goal_decision'))
                    <div class="bg-surface-base rounded-xl p-5">
                        <span class="stat-label text-text-muted block mb-2">Fitness Goal</span>
                        <span class="stat-value text-accent-yellow capitalize">{{ session('goal_decision') }}</span>
                        <span class="text-sm text-text-muted">weight</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Current Status -->
    @if (session('current_day', 1) > 1)
        <div class="mb-8 card-modern-dark p-6 border-l-4 border-accent-blue animate-scale-up bg-[#69CEBE]/20 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-12 h-12 rounded-xl bg-accent-blue/20 flex items-center justify-center">
                        <svg class="h-6 w-6 text-accent-blue" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Current Progress</h3>
                    <p class="text-text-secondary font-medium">
                        Day <span class="text-accent-blue font-bold text-xl">{{ session('current_day', 1) - 1 }}</span>
                        of <span class="text-accent-blue font-bold text-xl">{{ session('plan_period', 7) }}</span> completed
                    </p>
                    @if (session('goal_explanation'))
                        <p class="text-sm text-text-muted mt-3">{{ session('goal_explanation') }}</p>
                    @endif
                </div>
                <div class="ml-6 text-center">
                    <div class="text-4xl font-bold text-accent-blue">
                        {{ round(((session('current_day', 1) - 1) / session('plan_period', 7)) * 100) }}%
                    </div>
                    <span class="text-xs text-text-muted uppercase tracking-wider">Complete</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Control Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-end mb-8">
        @if (session('current_day', 1) > 1)
            <button onclick="resetMealPlan()"
                    class="btn-secondary group relative overflow-hidden p-2 rounded-lg hover:text-white cursor-pointer">
                <span class="relative z-10">Reset Plan</span>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-gray-600 to-gray-700 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        @endif

        <a href="{{ route('approve-meal') }}"
           class="approve-meal btn-primary bg-[#69CEBE] border-accent-green p-2 rounded text-white flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Approve Meal
        </a>

        <select name="meal_type" id="meal-type"
                class="border border-gray-300 py-3 px-5 rounded">
            <option value="ai-generated-meals">AI Generated Meals</option>
            <option value="our-meals">Curated Meals</option>
        </select>

        <button id="generateMealBtn" data-regenerate="false" onclick="startMealGeneration()"
                class="group relative overflow-hidden min-w-[200px] bg-[#69CEBE] py-3 px-5 rounded text-gray-100 cursor-pointer shadow hover:shadow-md">
            <span class="relative z-10 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                {{ session('current_day', 1) > 1 ? 'Continue Generating' : 'Generate Meal Plan' }}
            </span>
        </button>
    </div>

    <!-- Progress Bar -->
    <div id="progressContainer" class="hidden mb-8 animate-scale-up">
        <div class="card-glow p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-text-primary">Generating Your Personalized Meal Plan</h3>
                <span id="progressPercentage" class="text-lg font-bold text-primary-500">0%</span>
            </div>

            <div class="relative h-4 bg-dark-800/50 rounded-full overflow-hidden mb-6">
                <div id="progressBar"
                     class="absolute inset-y-0 left-0 bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 rounded-full progress-bar transition-all duration-500 ease-out"
                     style="width: 0%">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-6">
                <div class="robot-loader animate-bounce-slow">
                    <img src="https://www.svgrepo.com/show/521818/robot.svg" style="width: 50px; height: 50px">
                </div>
                <div class="text-left">
                    <p id="progressText" class="text-lg font-medium text-text-primary">Initializing AI meal
                        generator...</p>
                    <p id="currentDayText" class="text-sm text-text-muted mt-1">Preparing your personalized nutrition
                        plan</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Loading State (Fallback) -->
{{--    <div id="loader"--}}
{{--         class="hidden card-glow p-12 animate-scale-up">--}}
{{--        <div class="flex flex-col items-center justify-center gap-6">--}}
{{--            <div class="loader"></div>--}}
{{--            <h3 class="text-xl font-semibold text-text-primary">AI is crafting your perfect meal plan...</h3>--}}
{{--            <p class="text-text-secondary text-center max-w-md">Our AI is analyzing your fitness profile to create a--}}
{{--                personalized nutrition plan tailored to your goals.</p>--}}
{{--        </div>--}}
{{--    </div>--}}

    <!-- Error State -->
    <div id="error"
         class="hidden mb-8 card-modern border-accent-red/30 bg-accent-red/10 p-12 animate-scale-up">
        <div class="text-center">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-accent-red/20 flex items-center justify-center">
                <svg class="h-10 w-10 text-accent-red" viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-text-primary mb-2">Oops! Something went wrong</h3>
            <p id="errorMessage" class="text-text-secondary mb-6 max-w-md mx-auto">We encountered an error while
                generating your meal plan. Please try again.</p>
            <button onclick="location.reload()"
                    class="btn-secondary border-accent-red text-accent-red hover:bg-accent-red hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Try Again
            </button>
        </div>
    </div>

    <!-- Results Container -->
    <div id="ai-response" class="mt-8">
        @if (isset($jsonData) && !empty($jsonData['meal_plan']))
            @include('website.pages.response', ['jsonData' => $jsonData])
        @endif

    </div>
    <!-- Download Button -->
    <div class="flex justify-end mt-8">
        <button id="downloadPDF" class="btn-primary flex cursor-pointer bg-[#69CEBE] text-white p-2 rounded-lg" style="display:none">
{{--            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>--}}
{{--            </svg>--}}
            <span>Download PDF</span>
        </button>
    </div>
    @push('script')
        <script>
            let isGenerating = false;
            let currentDay = {{ session('current_day', 1) }};
            const maxDays = {{ session('plan_period', 7) }};

            function startMealGeneration() {
                if (isGenerating) return;

                isGenerating = true;

                // Hide error and show progress
                $('#error').addClass('hidden');
                $('#progressContainer').removeClass('hidden');

                // Update button state
                const btn = document.getElementById('generateMealBtn');
                btn.disabled = true;
                btn.textContent = 'Generating...';

                generateSingleDay();
            }

            function generateSingleDay() {
                const progressText = document.getElementById('progressText');
                const currentDayText = document.getElementById('currentDayText');
                const progressPercentage = document.getElementById('progressPercentage');

                // Update current day display
                if (currentDayText) {
                    currentDayText.textContent = `Preparing Day ${currentDay}`;
                }

                fetch('{{ route('generate-ai-meal') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        "meal-type": $('#meal-type').val(),
                        "regenerate": $('#generateMealBtn').data('regenerate')
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);

                        if (data.error) {
                            throw new Error(data.message || 'Something went wrong');
                        }

                        if (data.completed) {
                            // All days completed
                            $('#ai-response').html(data.html);

                            $('#generateMealBtn').attr('regenerate', false);
                            updateProgress(100, 'Meal plan completed!', 'All {{ session('plan_period', 7) }} days generated');
                            finishGeneration(true);
                            return;
                        }

                        // Update progress
                        // const progress = data.progress || ((data.day_completed / maxDays) * 100);
                        const progress = Math.min(100, (data.day_completed / maxDays) * 100);
                        updateProgress(
                            progress,
                            `Day ${data.day_completed} completed`,
                            `${data.day_completed} of ${maxDays} days`
                        );

                        // Update the meal plan display
                        $('#ai-response').html(data.html);

                        // Update current day for next iteration
                        currentDay = data.day_completed + 1;

                        // Continue with next day after a short delay
                        setTimeout(() => {
                            if (currentDay <= maxDays) {
                                generateSingleDay();
                            } else {
                                finishGeneration(true);
                            }
                        }, 1500); // 1.5 second delay between days

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError(error.message);
                        finishGeneration(false);
                    });
            }

            function updateProgress(percentage, text, subText) {
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                const currentDayText = document.getElementById('currentDayText');
                const progressPercentage = document.getElementById('progressPercentage');

                if (progressBar) {
                    progressBar.style.width = percentage + '%';
                }

                if (progressPercentage) {
                    progressPercentage.textContent = Math.round(percentage) + '%';
                }

                if (progressText) {
                    progressText.textContent = text;
                }

                if (currentDayText && subText) {
                    currentDayText.textContent = subText;
                }
            }

            function finishGeneration(success) {
                isGenerating = false;

                const btn = document.getElementById('generateMealBtn');
                btn.disabled = false;

                if (success) {
                    $("#downloadPDF").css('display', 'inline');
                    btn.textContent = 'Re-Generate Meal Plan';
                    $('#generateMealBtn').data('regenerate', true);
                    $('.approve-meal').removeClass('hidden');

                    // Hide progress after 2 seconds
                    setTimeout(() => {
                        $('#progressContainer').addClass('hidden');
                    }, 2000);
                } else {
                    btn.textContent = currentDay > 1 ? 'Continue Generating' : 'Generate Meal Plan';
                    $('#progressContainer').addClass('hidden');
                }
            }

            function showError(message) {
                $('#error').removeClass('hidden');
                $('#errorMessage').text(message);
                $('#progressContainer').addClass('hidden');
            }

            function resetMealPlan() {
                if (confirm('Are you sure you want to reset the meal plan? This will clear all progress.')) {
                    fetch('{{ route('reset-meal-plan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error resetting meal plan');
                        });
                }
            }

            function continueFromDay(day) {
                fetch('{{ route('continue-from-day') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        day: day
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentDay = data.current_day;
                            alert(`Will continue from day ${data.current_day}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Legacy support for old button click (remove if not needed)
            $('.generate-ai-meal').click(function (e) {
                e.preventDefault();
                startMealGeneration();
            });
        </script>

        <script>
            $(document).ready(function () {
                $("#downloadPDF").on('click', function () {
                    window.location.href = "{{route('download.pdf')}}"
                })
            })
        </script>
    @endpush
@endsection
