@extends('website.layouts.app')
@section('content')
    <style>
        .loader {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 8px solid #d1914b;
            box-sizing: border-box;
            --c: no-repeat radial-gradient(farthest-side, #d64123 94%, #0000);
            --b: no-repeat radial-gradient(farthest-side, #000 94%, #0000);
            background:
                var(--c) 11px 15px,
                var(--b) 6px 15px,
                var(--c) 35px 23px,
                var(--b) 29px 15px,
                var(--c) 11px 46px,
                var(--b) 11px 34px,
                var(--c) 36px 0px,
                var(--b) 50px 31px,
                var(--c) 47px 43px,
                var(--b) 31px 48px,
                #f6d353;
            background-size: 15px 15px, 6px 6px;
            animation: l4 3s infinite;
        }

        @keyframes l4 {
            0% { -webkit-mask: conic-gradient(#0000 360deg, #000 0) }
            16.67% { -webkit-mask: conic-gradient(#0000 300deg, #000 0) }
            33.33% { -webkit-mask: conic-gradient(#0000 240deg, #000 0) }
            50% { -webkit-mask: conic-gradient(#0000 180deg, #000 0) }
            66.67% { -webkit-mask: conic-gradient(#0000 120deg, #000 0) }
            83.33% { -webkit-mask: conic-gradient(#0000 60deg, #000 0) }
            100% { -webkit-mask: conic-gradient(#0000 0, #000 0) }
        }

        .progress-bar {
            transition: width 0.3s ease-in-out;
        }

        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .connected {
            background-color: #10b981;
            color: white;
        }

        .disconnected {
            background-color: #ef4444;
            color: white;
        }

        .connecting {
            background-color: #f59e0b;
            color: white;
        }

        .conversation-log {
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-family: monospace;
            font-size: 12px;
        }

        .log-entry {
            margin-bottom: 8px;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .log-system { background-color: #e3f2fd; }
        .log-user { background-color: #f3e5f5; }
        .log-assistant { background-color: #e8f5e8; }
        .log-error { background-color: #ffebee; }
    </style>

    <!-- Connection Status Indicator -->
    <div id="connectionStatus" class="connection-status disconnected">
        ● Disconnected
    </div>

    <h1 class="text-2xl sm:text-3xl md:text-4xl text-center font-semibold mb-7">
        AI Meal Planner - WebSocket Edition
    </h1>

    <section class="grid xl:grid-cols-2 gap-10">
        <div class="border-2 border-[#7F5AF0]/70 rounded-xl p-5 md:p-10" style="box-shadow: 0 0 10px rgb(127, 90, 240);">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold text-[#7F5AF0]">
                Fitness Calculations
            </h2>
            <div class="mt-3">
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>BMI</span>
                    <span>{{ $userInfo['bmi_overview'] ?? '' }}</span>
                </div>
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>Body Fat</span>
                    <span>{{ $userInfo['body_fat'] ? number_format($userInfo['body_fat'], 2) . '%' : '25%' }}</span>
                </div>
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>BMR</span>
                    <span>{{ $userInfo['bmr'] ?? '' }} cal</span>
                </div>
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>TDEE</span>
                    <span>{{ $userInfo['tdee'] ?? '' }} cal</span>
                </div>
            </div>
        </div>
        
        <div class="border-2 border-[#7F5AF0]/70 rounded-xl p-5 md:p-10" style="box-shadow: 0 0 10px rgb(127, 90, 240);">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold text-[#7F5AF0]">
                WebSocket Connection Info
            </h2>
            <div class="mt-3">
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>Client ID</span>
                    <span id="clientId">Not Connected</span>
                </div>
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>Conversation Memory</span>
                    <span id="messageCount">0 messages</span>
                </div>
                <div class="border-b border-[#2f254a] flex justify-between text-lg font-medium py-3">
                    <span>Current Day</span>
                    <span id="currentDayDisplay">Ready to Start</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Control Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end mt-8">
        <button onclick="connectWebSocket()" id="connectBtn"
            class="w-full sm:w-auto py-2 px-6 text-center font-semibold bg-blue-500 border border-blue-500 rounded-lg hover:bg-transparent hover:text-blue-500 text-white transition-colors">
            Connect to AI
        </button>
        
        <button onclick="resetConversation()" id="resetBtn" disabled
            class="w-full sm:w-auto py-2 px-6 text-center font-semibold bg-gray-500 border border-gray-500 rounded-lg hover:bg-transparent hover:text-gray-500 text-white transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
            Reset Conversation
        </button>
        
        <button onclick="startMealGeneration()" id="generateBtn" disabled
            class="w-full sm:w-auto py-2 px-6 text-center font-semibold bg-[#7F5AF0] border border-[#7F5AF0] rounded-lg hover:bg-transparent hover:text-[#7F5AF0] text-white transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
            Start 30-Day Generation
        </button>
        
        <a href="{{ route('approve-meal') }}" id="approveBtn" 
            class="w-full sm:w-auto hidden py-2 px-6 text-center font-semibold bg-green-500 border border-green-500 rounded-lg hover:bg-transparent hover:text-green-500 text-white transition-colors">
            Approve & Complete
        </a>
    </div>

    <!-- Progress Section -->
    <div id="progressContainer" class="hidden mt-8">
        <div class="bg-white border-2 border-[#7F5AF0]/70 rounded-xl p-6" style="box-shadow: 0 0 10px rgb(127, 90, 240);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[#7F5AF0]">Generating 30-Day Meal Plan</h3>
                <span id="progressPercentage" class="text-sm font-medium text-gray-700">0%</span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                <div id="progressBar" class="bg-gradient-to-r from-[#7F5AF0] to-purple-600 h-4 rounded-full progress-bar" style="width: 0%"></div>
            </div>
            
            <div class="flex items-center justify-center gap-4">
                <div class="loader"></div>
                <div>
                    <p id="progressText" class="text-sm font-medium text-gray-700">Waiting to start...</p>
                    <p id="currentDayText" class="text-xs text-gray-500">AI maintaining conversation memory</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Goal Decision Display -->
    <div id="goalContainer" class="hidden mt-8">
        <div class="bg-green-50 border-2 border-green-500/70 rounded-xl p-6" style="box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);">
            <h3 class="text-lg font-semibold text-green-600 mb-2 flex items-center gap-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                AI's Goal Decision
            </h3>
            <p id="goalDecision" class="font-medium text-gray-800"></p>
            <p id="goalExplanation" class="text-sm text-gray-600 mt-2"></p>
        </div>
    </div>

    <!-- Conversation Log (for debugging) -->
    <div class="mt-8">
        <div class="bg-white border-2 border-gray-300 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Live Conversation Log</h3>
                <button onclick="toggleConversationLog()" class="text-sm text-blue-600 hover:text-blue-800">
                    <span id="logToggleText">Show</span> Log
                </button>
            </div>
            <div id="conversationLog" class="conversation-log hidden"></div>
        </div>
    </div>

    <!-- Results Container -->
    <div id="mealPlanContainer" class="mt-8 hidden">
        <div id="mealPlanContent"></div>
    </div>

    @push('script')
        <script>
            let websocket = null;
            let isConnected = false;
            let conversationMessages = [];
            let currentDay = 1;
            let totalDays = 30;
            let mealPlan = [];
            let goalDecision = null;
            let goalExplanation = null;

            // WebSocket connection
            function connectWebSocket() {
                if (websocket && websocket.readyState === WebSocket.OPEN) {
                    return;
                }

                updateConnectionStatus('connecting');
                addToLog('system', 'Connecting to WebSocket server...');

                websocket = new WebSocket('ws://localhost:8080/meal-planner');

                websocket.onopen = function(event) {
                    isConnected = true;
                    updateConnectionStatus('connected');
                    addToLog('system', 'Connected to AI meal planner');
                    
                    document.getElementById('connectBtn').textContent = 'Connected';
                    document.getElementById('connectBtn').disabled = true;
                    document.getElementById('generateBtn').disabled = false;
                    document.getElementById('resetBtn').disabled = false;
                };

                websocket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    handleWebSocketMessage(data);
                };

                websocket.onerror = function(error) {
                    addToLog('error', 'WebSocket error: ' + error);
                    updateConnectionStatus('disconnected');
                };

                websocket.onclose = function(event) {
                    isConnected = false;
                    updateConnectionStatus('disconnected');
                    addToLog('system', 'Connection closed');
                    
                    document.getElementById('connectBtn').textContent = 'Connect to AI';
                    document.getElementById('connectBtn').disabled = false;
                    document.getElementById('generateBtn').disabled = true;
                    document.getElementById('resetBtn').disabled = true;
                };
            }

            // Handle WebSocket messages
            function handleWebSocketMessage(data) {
                console.log('Received:', data);
                
                switch(data.type) {
                    case 'connected':
                        document.getElementById('clientId').textContent = data.client_id;
                        addToLog('system', 'Client ID: ' + data.client_id);
                        break;
                        
                    case 'generation_started':
                        document.getElementById('progressContainer').classList.remove('hidden');
                        document.getElementById('progressText').textContent = data.message;
                        addToLog('system', data.message);
                        break;
                        
                    case 'progress_update':
                        updateProgress(data.progress, data.message, `Day ${data.current_day} of ${data.total_days || 30}`);
                        addToLog('system', `Progress: ${data.progress}% - ${data.message}`);
                        break;
                        
                    case 'day_completed':
                        handleDayCompleted(data);
                        break;
                        
                    case 'generation_completed':
                        handleGenerationCompleted(data);
                        break;
                        
                    case 'day_error':
                        addToLog('error', `Day ${data.day} error: ${data.message}`);
                        break;
                        
                    case 'error':
                        addToLog('error', data.message);
                        break;
                        
                    case 'conversation_reset':
                        handleConversationReset();
                        break;
                }
            }

            // Handle day completion
            function handleDayCompleted(data) {
                currentDay = data.day;
                
                // Store goal info (first day)
                if (data.goal_decision && !goalDecision) {
                    goalDecision = data.goal_decision;
                    goalExplanation = data.goal_explanation;
                    showGoalDecision();
                }
                
                // Add to meal plan
                if (data.meal_plan) {
                    mealPlan.push(data.meal_plan);
                    updateMealPlanDisplay();
                }
                
                // Update progress
                updateProgress(data.progress, `Day ${data.day} completed`, `${data.day} of 30 days`);
                
                // Update conversation count
                conversationMessages.push({
                    role: 'user',
                    day: data.day,
                    timestamp: new Date().toLocaleTimeString()
                });
                conversationMessages.push({
                    role: 'assistant', 
                    day: data.day,
                    timestamp: new Date().toLocaleTimeString()
                });
                
                updateMessageCount();
                
                addToLog('assistant', `Day ${data.day} meal plan created`);
                
                // Update current day display
                document.getElementById('currentDayDisplay').textContent = `Day ${data.day + 1}`;
            }

            // Handle generation completion
            function handleGenerationCompleted(data) {
                updateProgress(100, 'All 30 days completed!', 'Generation finished');
                addToLog('system', 'All 30 days completed successfully!');
                
                document.getElementById('approveBtn').classList.remove('hidden');
                document.getElementById('generateBtn').textContent = 'Regenerate Plan';
                document.getElementById('generateBtn').disabled = false;
                
                // Hide progress after delay
                setTimeout(() => {
                    document.getElementById('progressContainer').classList.add('hidden');
                }, 3000);
            }

            // Show goal decision
            function showGoalDecision() {
                document.getElementById('goalContainer').classList.remove('hidden');
                document.getElementById('goalDecision').textContent = `Goal: ${goalDecision.toUpperCase()} weight`;
                document.getElementById('goalExplanation').textContent = goalExplanation;
            }

            // Update meal plan display
            function updateMealPlanDisplay() {
                if (mealPlan.length === 0) return;
                
                document.getElementById('mealPlanContainer').classList.remove('hidden');
                
                const jsonData = {
                    goal_decision: goalDecision,
                    goal_explanation: goalExplanation,
                    meal_plan: mealPlan
                };
                
                // Here you would render the meal plan using your existing template
                // For now, just show basic info
                document.getElementById('mealPlanContent').innerHTML = `
                    <div class="bg-white border-2 border-[#7F5AF0]/70 rounded-xl p-6" style="box-shadow: 0 0 10px rgb(127, 90, 240);">
                        <h3 class="text-xl font-semibold text-[#7F5AF0] mb-4">Generated Meal Plan</h3>
                        <p class="text-gray-700">Days completed: ${mealPlan.length}/30</p>
                        <p class="text-gray-700">Goal: ${goalDecision || 'Not set'}</p>
                        <div class="mt-4 max-h-64 overflow-y-auto">
                            ${mealPlan.map(day => `
                                <div class="border-b py-2">
                                    <strong>Day ${day.day}:</strong> ${day.meals.length} meals planned
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Start meal generation
            function startMealGeneration() {
                if (!isConnected) {
                    alert('Please connect to WebSocket first');
                    return;
                }

                const userInfo = @json($userInfo);
                
                const message = {
                    type: 'start_meal_generation',
                    user_info: userInfo
                };

                websocket.send(JSON.stringify(message));
                
                document.getElementById('generateBtn').disabled = true;
                document.getElementById('generateBtn').textContent = 'Generating...';
                
                addToLog('user', 'Started meal generation with user profile');
            }

            // Reset conversation
            function resetConversation() {
                if (!isConnected) return;

                if (confirm('Reset the entire conversation? This will clear all progress.')) {
                    websocket.send(JSON.stringify({type: 'reset_conversation'}));
                }
            }

            // Handle conversation reset
            function handleConversationReset() {
                conversationMessages = [];
                currentDay = 1;
                mealPlan = [];
                goalDecision = null;
                goalExplanation = null;
                
                updateMessageCount();
                document.getElementById('currentDayDisplay').textContent = 'Ready to Start';
                document.getElementById('goalContainer').classList.add('hidden');
                document.getElementById('mealPlanContainer').classList.add('hidden');
                document.getElementById('progressContainer').classList.add('hidden');
                document.getElementById('approveBtn').classList.add('hidden');
                
                document.getElementById('generateBtn').disabled = false;
                document.getElementById('generateBtn').textContent = 'Start 30-Day Generation';
                
                addToLog('system', 'Conversation reset - memory cleared');
            }

            // Update connection status
            function updateConnectionStatus(status) {
                const statusEl = document.getElementById('connectionStatus');
                statusEl.className = `connection-status ${status}`;
                
                switch(status) {
                    case 'connected':
                        statusEl.textContent = '● Connected';
                        break;
                    case 'connecting':
                        statusEl.textContent = '● Connecting...';
                        break;
                    case 'disconnected':
                        statusEl.textContent = '● Disconnected';
                        break;
                }
            }

            // Update progress
            function updateProgress(percentage, text, subText) {
                document.getElementById('progressBar').style.width = percentage + '%';
                document.getElementById('progressPercentage').textContent = Math.round(percentage) + '%';
                document.getElementById('progressText').textContent = text;
                document.getElementById('currentDayText').textContent = subText;
            }

            // Update message count
            function updateMessageCount() {
                document.getElementById('messageCount').textContent = `${conversationMessages.length} messages`;
            }

            // Add to conversation log
            function addToLog(type, message) {
                const log = document.getElementById('conversationLog');
                const timestamp = new Date().toLocaleTimeString();
                
                const entry = document.createElement('div');
                entry.className = `log-entry log-${type}`;
                entry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
                
                log.appendChild(entry);
                log.scrollTop = log.scrollHeight;
            }

            // Toggle conversation log
            function toggleConversationLog() {
                const log = document.getElementById('conversationLog');
                const toggleText = document.getElementById('logToggleText');
                
                if (log.classList.contains('hidden')) {
                    log.classList.remove('hidden');
                    toggleText.textContent = 'Hide';
                } else {
                    log.classList.add('hidden');
                    toggleText.textContent = 'Show';
                }
            }

            // Auto-connect when page loads
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-connect after 1 second
                setTimeout(() => {
                    connectWebSocket();
                }, 1000);
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (websocket) {
                    websocket.close();
                }
            });
        </script>
    @endpush
@endsection