# Mobile API Documentation - AI Meal Generation with Streaming

## Overview
This API provides AI-powered meal plan generation with real-time streaming support for mobile applications. No authentication is required - users are automatically identified and tracked based on their physical metrics.

## Base URL
```
http://your-domain.com/api/v1/mobile
```

## Key Features
- **No Authentication Required**: Simple user identification based on physical metrics
- **Real-time Streaming**: Server-Sent Events (SSE) for live meal generation updates
- **Auto User Management**: Automatic user creation and tracking
- **Resume Support**: Sessions can be resumed if connection drops
- **PDF Export**: Download meal plans as PDF documents

---

## API Endpoints

### 1. Generate Meals (Main Endpoint)
**POST** `/generate-meals`

Starts AI meal generation for a user. Creates or finds user based on physical metrics.

#### Request Body
```json
{
    "device_id": "optional-unique-device-id",
    "age": 25,
    "height": 175,  // in cm
    "weight": 70,   // in kg
    "gender": "male",  // "male" or "female"
    "activity_level": "Moderately active (3–5 days/week)",
    "neck_circumference": 38,  // in cm
    "waist_circumference": 80,  // in cm
    "hip_circumference": 95,   // in cm (required for females)
    "plan_period": 30  // 7 or 30 days (optional, default: 30)
}
```

#### Activity Level Options
- `"Sedentary (little or no exercise)"`
- `"Lightly active (1–3 days/week)"`
- `"Moderately active (3–5 days/week)"`
- `"Very active (6–7 days/week)"`
- `"Super active (twice/day or physical job)"`

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "session_id": "550e8400-e29b-41d4-a716-446655440000",
        "stream_url": "/api/v1/mobile/stream/550e8400-e29b-41d4-a716-446655440000",
        "user_id": "usr_123",
        "is_existing_user": false,
        "user_metrics": {
            "bmi": 22.86,
            "bmi_overview": "Normal",
            "bmr": 1680,
            "tdee": 2604,
            "body_fat": 15.2,
            "goal": "maintain"
        }
    },
    "message": "Meal generation started successfully"
}
```

#### Error Response (422)
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "age": ["The age field is required."],
        "weight": ["The weight must be between 20 and 500."]
    }
}
```

---

### 2. Stream Meal Generation (SSE)
**GET** `/stream/{session_id}`

Connects to Server-Sent Events stream for real-time meal generation updates.

#### Stream Events

##### Event: `connected`
```
event: connected
data: {"message": "Connected to meal generation stream", "session_id": "550e8400..."}
```

##### Event: `status`
```
event: status
data: {"status": "processing", "message": "Generating your personalized meal plan...", "progress": 10.5}
```

##### Event: `day_progress`
```
event: day_progress
data: {"day": 3, "total_days": 30, "status": "generating", "progress": 10.0}
```

##### Event: `meal_data`
```
event: meal_data
data: {
    "day": 1,
    "meals": [
        {
            "type": "breakfast",
            "name": "Protein Pancakes",
            "time": "07:00",
            "ingredients": [
                {"name": "Oats", "amount": "100g", "cal": 389, "protein": 16.9, "carbs": 66.3, "fat": 6.9, "price": 2.5}
            ],
            "sauces": [
                {"name": "Honey", "amount": "1tbsp", "cal": 64, "protein": 0.1, "carbs": 17.3, "fat": 0, "price": 0.5}
            ],
            "instructions": "Mix oats with eggs and blend. Cook on medium heat.",
            "total_cal": 453,
            "total_protein": 17,
            "total_carbs": 83.6,
            "total_fat": 6.9,
            "total_price": 3.0
        }
    ],
    "daily_total": {
        "calories": 1850,
        "protein": 120,
        "carbs": 200,
        "fat": 65,
        "price": 25
    }
}
```

##### Event: `day_complete`
```
event: day_complete
data: {"day": 1, "progress": 3.33, "message": "Day 1 completed successfully"}
```

##### Event: `complete`
```
event: complete
data: {
    "status": "completed",
    "total_days": 30,
    "meal_plan_id": "550e8400...",
    "summary": {
        "total_calories": 55500,
        "total_protein": 3600,
        "total_carbs": 6000,
        "total_fat": 1950,
        "total_price": 750,
        "total_meals": 120
    },
    "message": "Meal generation completed successfully!"
}
```

##### Event: `error`
```
event: error
data: {"status": "failed", "message": "Error description", "session_id": "550e8400..."}
```

##### Event: `heartbeat`
```
event: heartbeat
data: {"timestamp": 1699123456, "status": "processing", "progress": 50.0}
```

#### Mobile Implementation Example (JavaScript/React Native)

```javascript
const streamMealGeneration = (sessionId) => {
    const eventSource = new EventSource(`${API_BASE_URL}/stream/${sessionId}`);
    
    eventSource.addEventListener('connected', (e) => {
        const data = JSON.parse(e.data);
        console.log('Connected:', data.message);
    });
    
    eventSource.addEventListener('meal_data', (e) => {
        const data = JSON.parse(e.data);
        // Update UI with meal data for the day
        updateMealDisplay(data.day, data.meals);
    });
    
    eventSource.addEventListener('day_complete', (e) => {
        const data = JSON.parse(e.data);
        // Update progress bar
        updateProgress(data.progress);
    });
    
    eventSource.addEventListener('complete', (e) => {
        const data = JSON.parse(e.data);
        // Handle completion
        onGenerationComplete(data);
        eventSource.close();
    });
    
    eventSource.addEventListener('error', (e) => {
        const data = JSON.parse(e.data);
        // Handle error
        showError(data.message);
        eventSource.close();
    });
    
    eventSource.onerror = (error) => {
        console.error('EventSource failed:', error);
        eventSource.close();
    };
    
    return eventSource;
};
```

---

### 3. Get Session Status
**GET** `/session/{session_id}/status`

Check the current status of a meal generation session.

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "session_id": "550e8400-e29b-41d4-a716-446655440000",
        "status": "processing",  // "pending", "processing", "completed", "failed"
        "current_day": 15,
        "total_days": 30,
        "progress": 50.0,
        "error_message": null,
        "started_at": "2024-01-15T10:30:00Z",
        "completed_at": null
    }
}
```

---

### 4. Get Existing Meal Plan
**GET** `/meal-plan/{user_identifier}`

Retrieve the latest completed meal plan for a user.

**Parameters:**
- `user_identifier`: Can be either `user_hash` or `device_id`

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "session_id": "550e8400...",
        "goal": "maintain",
        "goal_explanation": "Based on your BMI and body fat percentage...",
        "total_days": 30,
        "meal_plan": [
            // Array of daily meal plans (30 items)
            [
                {
                    "type": "breakfast",
                    "name": "Protein Pancakes",
                    // ... meal details
                }
            ]
        ],
        "daily_totals": [
            {
                "calories": 1850,
                "protein": 120,
                "carbs": 200,
                "fat": 65,
                "price": 25
            }
        ],
        "summary": {
            "total_calories": 55500,
            "total_protein": 3600,
            "total_carbs": 6000,
            "total_fat": 1950,
            "total_price": 750,
            "total_meals": 120
        },
        "generated_at": "2024-01-15T15:45:00Z"
    }
}
```

---

### 5. Download Meal Plan as PDF
**GET** `/meal-plan/{user_identifier}/pdf`

Download the meal plan as a PDF document.

**Parameters:**
- `user_identifier`: Can be either `user_hash` or `device_id`

**Response:**
- Content-Type: `application/pdf`
- Returns PDF file download

---

## Complete Integration Flow

### Step 1: Generate Meals
```javascript
async function startMealGeneration(userInfo) {
    const response = await fetch(`${API_BASE_URL}/generate-meals`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            device_id: getDeviceId(),
            age: userInfo.age,
            height: userInfo.height,
            weight: userInfo.weight,
            gender: userInfo.gender,
            activity_level: userInfo.activityLevel,
            neck_circumference: userInfo.neckCircumference,
            waist_circumference: userInfo.waistCircumference,
            hip_circumference: userInfo.hipCircumference,
            plan_period: 30
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Start streaming
        return data.data.session_id;
    } else {
        throw new Error(data.message);
    }
}
```

### Step 2: Connect to Stream
```javascript
function connectToStream(sessionId) {
    const eventSource = streamMealGeneration(sessionId);
    
    // Store reference for cleanup
    return eventSource;
}
```

### Step 3: Handle Updates
```javascript
function updateMealDisplay(day, meals) {
    // Update your UI with the meals for the current day
    setState(prevState => ({
        ...prevState,
        days: {
            ...prevState.days,
            [day]: meals
        }
    }));
}

function updateProgress(progress) {
    // Update progress bar
    setProgress(progress);
}

function onGenerationComplete(data) {
    // Handle completion
    saveMealPlan(data);
    showSuccessMessage('Meal plan generated successfully!');
}
```

---

## Error Handling

### Connection Errors
If the SSE connection drops, implement reconnection logic:

```javascript
function reconnectStream(sessionId, retries = 3) {
    let retryCount = 0;
    
    const connect = () => {
        const eventSource = streamMealGeneration(sessionId);
        
        eventSource.onerror = (error) => {
            eventSource.close();
            
            if (retryCount < retries) {
                retryCount++;
                setTimeout(() => {
                    console.log(`Reconnecting... (${retryCount}/${retries})`);
                    connect();
                }, 2000 * retryCount); // Exponential backoff
            } else {
                showError('Connection lost. Please check your internet connection.');
            }
        };
        
        return eventSource;
    };
    
    return connect();
}
```

### Session Recovery
If the app crashes or loses connection, you can check session status:

```javascript
async function recoverSession(sessionId) {
    const response = await fetch(`${API_BASE_URL}/session/${sessionId}/status`);
    const data = await response.json();
    
    if (data.success) {
        if (data.data.status === 'processing') {
            // Reconnect to stream
            return connectToStream(sessionId);
        } else if (data.data.status === 'completed') {
            // Fetch the complete meal plan
            return fetchMealPlan(userIdentifier);
        }
    }
}
```

---

## Tips for Mobile Developers

1. **Device ID**: Generate and store a unique device ID for better user tracking:
   ```javascript
   import AsyncStorage from '@react-native-async-storage/async-storage';
   import uuid from 'react-native-uuid';
   
   async function getDeviceId() {
       let deviceId = await AsyncStorage.getItem('device_id');
       if (!deviceId) {
           deviceId = uuid.v4();
           await AsyncStorage.setItem('device_id', deviceId);
       }
       return deviceId;
   }
   ```

2. **Offline Support**: Cache the completed meal plan locally:
   ```javascript
   async function cacheMealPlan(mealPlan) {
       await AsyncStorage.setItem('cached_meal_plan', JSON.stringify(mealPlan));
   }
   ```

3. **Progress Persistence**: Save progress to show if app restarts:
   ```javascript
   async function saveProgress(sessionId, progress) {
       await AsyncStorage.setItem('current_session', JSON.stringify({
           sessionId,
           progress,
           timestamp: Date.now()
       }));
   }
   ```

4. **Background Processing**: Keep the stream alive in background (iOS/Android specific implementations required)

5. **Data Optimization**: The meal plan can be large. Consider pagination or lazy loading for better performance.

---

## Rate Limiting
- Maximum 10 meal generations per user per day
- SSE connections timeout after 10 minutes of inactivity
- PDF downloads limited to 5 per hour per user

---

## Support
For issues or questions, contact: support@yourdomain.com

## Version
API Version: 1.0.0
Last Updated: 2025-08-09