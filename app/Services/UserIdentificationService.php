<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserIdentificationService
{
    /**
     * Find or create user based on physical metrics and device ID
     */
    public function findOrCreateUser(array $userData)
    {
        // Generate unique hash based on physical metrics
        $userHash = $this->generateUserHash($userData);

        // Try to find user by hash or device_id
        $user = $this->findExistingUser($userHash, $userData['device_id'] ?? null);

        if ($user) {
            // Update user metrics if they changed
            $user = $this->updateUserMetrics($user, $userData);
            Log::info("Existing user found and updated: {$user->id}");
        } else {
            // Create new user
            $user = $this->createNewUser($userData, $userHash);
            Log::info("New user created: {$user->id}");
        }

        return $user;
    }

    /**
     * Generate unique hash based on user's physical metrics
     */
    private function generateUserHash(array $userData)
    {
        // Create hash from key physical metrics
        $hashString = implode('|', [
            $userData['age'],
            round($userData['height'], 1),
            round($userData['weight'], 1),
            $userData['gender'],
            $userData['device_id'] ?? 'no-device'
        ]);

        return md5($hashString);
    }

    /**
     * Find existing user by hash or device ID
     */
    private function findExistingUser($userHash, $deviceId)
    {
        $query = User::where('user_hash', $userHash);

        if ($deviceId) {
            $query->orWhere('device_id', $deviceId);
        }

        return $query->first();
    }

    /**
     * Create new user with physical metrics
     */
    private function createNewUser(array $userData, $userHash)
    {
        // Calculate BMI
        $heightInMeters = $userData['height'] / 100;
        $bmi = $userData['weight'] / ($heightInMeters * $heightInMeters);
        $bmiOverview = $this->getBmiOverview($bmi);

        // Calculate BMR
        $bmr = $this->calculateBmr($userData);

        // Calculate TDEE
        $tdee = $this->calculateTdee($bmr, $userData['activity_level']);

        // Calculate Body Fat Percentage
        $bodyFat = $this->calculateBodyFat($userData);

        // Determine goal based on BMI
        $goal = $this->determineGoal($bmi, $bodyFat);

        // Create user
        $user = User::create([
            'name' => 'Mobile User ' . Str::random(6),
            'email' => 'mobile_' . $userHash . '@app.local',
            'email_verified_at' => now(),
            'password' => null, // No password for mobile users
            'device_id' => $userData['device_id'] ?? null,
            'user_hash' => $userHash,
            'age' => $userData['age'],
            'height' => $userData['height'],
            'weight' => $userData['weight'],
            'gender' => $userData['gender'],
            'activity_level' => $userData['activity_level'],
            'neck_circumference' => $userData['neck_circumference'],
            'waist_circumference' => $userData['waist_circumference'],
            'hip_circumference' => $userData['hip_circumference'] ?? null,
            'bmi' => round($bmi, 2),
            'bmi_overview' => $bmiOverview,
            'bmr' => round($bmr),
            'tdee' => round($tdee),
            'body_fat' => round($bodyFat, 2),
            'plan_period' => $userData['plan_period'] ?? 30,
            'goal' => $goal,
            'last_generation_at' => now(),
            'total_plans_generated' => 0
        ]);

        return $user;
    }

    /**
     * Update user metrics if they changed
     */
    private function updateUserMetrics(User $user, array $userData)
    {
        // Check if metrics changed significantly
        $metricsChanged = false;

        if (abs($user->weight - $userData['weight']) > 0.5) {
            $metricsChanged = true;
        }
        if (abs($user->height - $userData['height']) > 1) {
            $metricsChanged = true;
        }
        if ($user->age != $userData['age']) {
            $metricsChanged = true;
        }

        if ($metricsChanged) {
            // Recalculate metrics
            $heightInMeters = $userData['height'] / 100;
            $bmi = $userData['weight'] / ($heightInMeters * $heightInMeters);
            $bmiOverview = $this->getBmiOverview($bmi);
            $bmr = $this->calculateBmr($userData);
            $tdee = $this->calculateTdee($bmr, $userData['activity_level']);
            $bodyFat = $this->calculateBodyFat($userData);
            $goal = $this->determineGoal($bmi, $bodyFat);

            // Update user
            $user->update([
                'age' => $userData['age'],
                'height' => $userData['height'],
                'weight' => $userData['weight'],
                'activity_level' => $userData['activity_level'],
                'neck_circumference' => $userData['neck_circumference'],
                'waist_circumference' => $userData['waist_circumference'],
                'hip_circumference' => $userData['hip_circumference'] ?? $user->hip_circumference,
                'bmi' => round($bmi, 2),
                'bmi_overview' => $bmiOverview,
                'bmr' => round($bmr),
                'tdee' => round($tdee),
                'body_fat' => round($bodyFat, 2),
                'goal' => $goal,
                'device_id' => $userData['device_id'] ?? $user->device_id
            ]);
        }

        // Update device ID if provided
        if (isset($userData['device_id']) && $userData['device_id'] !== $user->device_id) {
            $user->update(['device_id' => $userData['device_id']]);
        }

        // Update last generation time
        $user->update(['last_generation_at' => now()]);

        return $user;
    }

    /**
     * Get BMI overview
     */
    private function getBmiOverview($bmi)
    {
        return match (true) {
            $bmi < 18.5 => 'Underweight',
            $bmi < 24.9 => 'Normal',
            $bmi < 29.9 => 'Overweight',
            default => 'Obese',
        };
    }

    /**
     * Calculate BMR (Basal Metabolic Rate)
     */
    private function calculateBmr(array $userData)
    {
        $weight = $userData['weight'];
        $height = $userData['height'];
        $age = $userData['age'];

        if ($userData['gender'] === 'male') {
            // Mifflin-St Jeor Equation for males
            return 10 * $weight + 6.25 * $height - 5 * $age + 5;
        } else {
            // Mifflin-St Jeor Equation for females
            return 10 * $weight + 6.25 * $height - 5 * $age - 161;
        }
    }

    /**
     * Calculate TDEE (Total Daily Energy Expenditure)
     */
    private function calculateTdee($bmr, $activityLevel)
    {
        $activityFactors = [
            "Sedentary (little or no exercise)" => 1.2,
            "Lightly active (1–3 days/week)" => 1.375,
            "Moderately active (3–5 days/week)" => 1.55,
            "Very active (6–7 days/week)" => 1.725,
            "Super active (twice/day or physical job)" => 1.9,
        ];

        $factor = $activityFactors[$activityLevel] ?? 1.2;

        return $bmr * $factor;
    }

    /**
     * Calculate body fat percentage
     */
    private function calculateBodyFat(array $userData)
    {
        $waist = $userData['waist_circumference'];
        $neck = $userData['neck_circumference'];
        $height = $userData['height'];
        $heightInInches = $height * 0.393701; // Convert cm to inches

        if ($userData['gender'] === 'male') {
            // US Navy Method for males
            return 86.010 * log10($waist - $neck) - 70.041 * log10($heightInInches) + 36.76;
        } else {
            // US Navy Method for females
            $hip = $userData['hip_circumference'] ?? $waist; // Use waist if hip not provided
            return 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($heightInInches) - 78.387;
        }
    }

    /**
     * Determine goal based on BMI and body fat
     */
    private function determineGoal($bmi, $bodyFat)
    {
        // Logic to determine if user should lose, gain, or maintain weight
        if ($bmi < 18.5) {
            return 'gain';
        } elseif ($bmi > 25) {
            return 'lose';
        } else {
            // Normal BMI - check body fat percentage
            if ($bodyFat > 25) { // High body fat
                return 'lose';
            } elseif ($bodyFat < 15) { // Low body fat
                return 'gain';
            } else {
                return 'maintain';
            }
        }
    }
}