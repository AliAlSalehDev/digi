<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan - {{ $generated_date }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #212529;
            background: #ffffff;
        }

        /* Header Styles */
        .header {
            background: #8b7ee0;
            color: white;
            padding: 30px 40px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(35deg);
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .brand-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .brand-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }

        .brand-logo img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: white;
            padding: 8px;
        }

        .brand-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 12px;
            opacity: 0.9;
            font-weight: 300;
        }

        .plan-title {
            font-size: 32px;
            font-weight: 700;
            margin: 20px 0 10px;
            letter-spacing: -1px;
        }

        .plan-meta {
            font-size: 14px;
            opacity: 0.9;
        }

        /* User Profile Card */
        .profile-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 40px;
            border: 1px solid #ccc;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #7F5AF0;
        }

        .profile-title {
            font-size: 18px;
            font-weight: 700;
            color: #7F5AF0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .profile-title::before {
            content: '';
            margin-right: 10px;
            font-size: 20px;
        }

        .stats-grid {
            display: table;
            width: 100%;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin: 0 5px;
            border: 1px solid #ccc;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #7c3aed;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Executive Summary */
        .summary-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 0 40px 30px;
            border: 1px solid #dee2e6;
        }

        .summary-title {
            font-size: 18px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .summary-title::before {
            content: '';
            margin-right: 10px;
            font-size: 20px;
        }

        .summary-stats {
            display: table;
            width: 100%;
        }

        .summary-stat {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }

        .summary-number {
            font-size: 16px;
            font-weight: 700;
            color: #7F5AF0;
            display: block;
        }

        .summary-desc {
            font-size: 10px;
            color: #6c757d;
            margin-top: 3px;
        }

        .cost-highlight {
            background: #7F5AF0;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Day Sections */
        .day-container {
            margin: 0 40px 25px;
            page-break-inside: avoid;
        }

        .day-header {
            background: #7F5AF0;
            color: white;
            padding: 12px 20px;
            border-radius: 12px 12px 0 0;
            font-size: 16px;
            font-weight: 700;
            position: relative;
            overflow: hidden;
        }

        .day-header::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .meals-wrapper {
            background: white;
            border: 2px solid #e9ecef;
            border-top: none;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }

        /* Meal Cards */
        .meal-card {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            transition: background 0.2s;
        }

        .meal-card:last-child {
            border-bottom: none;
        }

        .meal-card:nth-child(even) {
            background: #f8f9fa;
        }

        .our-meal {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .ai-meal {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
        }

        .meal-header {
            margin-bottom: 10px;
        }

        .meal-name {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .meal-badges {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .meal-type-badge {
            background: #8b7ee0;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meal-time {
            color: #6c757d;
            font-size: 10px;
            display: flex;
            align-items: center;
        }

        .meal-time::before {
            content: '';
            margin-right: 5px;
        }

        .meal-description {
            background: #f1f3f4;
            padding: 8px 12px;
            border-radius: 6px;
            font-style: italic;
            color: #495057;
            font-size: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #7F5AF0;
        }

        /* Ingredients & Sauces */
        .ingredients-section, .sauces-section {
            margin-bottom: 10px;
        }

        .section-header {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 11px;
            display: flex;
            align-items: center;
        }

        .ingredients-list {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px;
        }

        .ingredient-item, .sauce-item {
            display: table;
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px dotted #dee2e6;
            font-size: 11px;
        }

        .ingredient-item:last-child, .sauce-item:last-child {
            border-bottom: none;
        }

        .item-name {
            display: table-cell;
            font-weight: 500;
            color: #212529;
            width: 50%;
        }

        .item-amount {
            display: table-cell;
            color: #7F5AF0;
            font-weight: 600;
        }

        .item-details {
            display: table-cell;
            text-align: right;
            color: #6c757d;
            font-size: 9px;
        }

        /* Instructions */
        .instructions-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 3px solid #7F5AF0;
        }

        .instructions-box strong {
            color: #7F5AF0;
            display: block;
            margin-bottom: 5px;
        }

        /* Nutrition Summary */
        .nutrition-summary {
            background: #8b7ee0;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
        }

        .nutrition-grid {
            display: table;
            width: 100%;
        }

        .nutrition-item {
            display: table-cell;
            text-align: center;
            padding: 0 5px;
        }

        .nutrition-value {
            font-size: 14px;
            font-weight: 700;
            display: block;
        }

        .nutrition-label {
            font-size: 9px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Day Summary */
        .day-summary {
            background: #e3f2fd;
            padding: 15px;
            margin: 15px;
            border-radius: 8px;
            border: 1px solid #90caf9;
        }

        .day-summary-title {
            font-size: 12px;
            font-weight: 700;
            color: #1976d2;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            background: #f8f9fa;
            padding: 20px 40px;
            margin-top: 40px;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }

        .footer-content {
            color: #6c757d;
            font-size: 10px;
        }

        .footer-brand {
            font-weight: 600;
            color: #7F5AF0;
        }

        .qr-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            margin: 10px auto;
            background: #f8f9fa;
            border: 2px solid #7F5AF0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #6c757d;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }

        /* Page Breaks */
        .page-break {
            page-break-before: always;
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-data h3 {
            font-size: 18px;
            color: #495057;
            margin-bottom: 10px;
        }

        .no-data p {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-content">
            <div class="brand-section">
                <div class="brand-info">
                    <div class="brand-name">24 Digi</div>
                    <div class="brand-tagline">Your Personalized Nutrition Journey</div>
                </div>
            </div>

            <h1 class="plan-title">{{ $total_days ?? 30 }}-Day Fitness Meal Plan</h1>
            <div class="plan-meta">
                <p>Generated on {{ $generated_date }}</p>
                @if($goal_decision)
                    <p>Goal: {{ ucfirst($goal_decision) }} Weight</p>
                @endif
            </div>
        </div>
    </div>

    <!-- User Profile Section -->
    @if($user_info)
    <div class="profile-card">
        <h2 class="profile-title">Your Fitness Profile</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-value">{{ $user_info['bmi_overview'] ?? 'N/A' }}</span>
                <span class="stat-label">BMI Status</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ isset($user_info['body_fat']) ? number_format($user_info['body_fat'], 1) . '%' : '25%' }}</span>
                <span class="stat-label">Body Fat</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $user_info['bmr'] ?? 'N/A' }}</span>
                <span class="stat-label">BMR (cal)</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $user_info['tdee'] ?? 'N/A' }}</span>
                <span class="stat-label">TDEE (cal)</span>
            </div>
        </div>
        @if($goal_explanation)
            <p style="margin-top: 15px; font-style: italic; color: #6c757d; text-align: center; font-size: 11px; background: white; padding: 10px; border-radius: 6px;">
                💡 {{ $goal_explanation }}
            </p>
        @endif
    </div>
    @endif

    <!-- Executive Summary -->
    @if(isset($summary))
    <div class="summary-card">
        <h2 class="summary-title">Plan Overview & Analytics</h2>
        <div class="summary-stats">
            <div class="summary-stat">
                <span class="summary-number">{{ number_format($summary['avg_cal_per_day']) }}</span>
                <span class="summary-desc">Avg Calories/Day</span>
            </div>
            <div class="summary-stat">
                <span class="summary-number">{{ number_format($summary['avg_protein_per_day'], 1) }}g</span>
                <span class="summary-desc">Avg Protein/Day</span>
            </div>
            <div class="summary-stat">
                <span class="summary-number">{{ number_format($summary['avg_carbs_per_day'], 1) }}g</span>
                <span class="summary-desc">Avg Carbs/Day</span>
            </div>
            <div class="summary-stat">
                <span class="summary-number">{{ number_format($summary['avg_fat_per_day'], 1) }}g</span>
                <span class="summary-desc">Avg Fat/Day</span>
            </div>
            <div class="summary-stat">
                <span class="summary-number">AED {{ number_format($summary['avg_price_per_day'], 2) }}</span>
                <span class="summary-desc">Avg Cost/Day</span>
            </div>
        </div>
        <div class="cost-highlight">
            Total Investment: AED {{ number_format($summary['total_price'], 2) }} for {{ $summary['total_meals'] }} meals
        </div>
    </div>
    @endif

    <!-- Meal Plan Days -->
    @if($meal_plan && is_array($meal_plan))
        @foreach($meal_plan as $dayIndex => $meals)
            @if($dayIndex > 0 && $dayIndex % 4 == 0)
                <div class="page-break"></div>
            @endif

            <div class="day-container">
                <div class="day-header">
                    Day {{ $dayIndex + 1 }}
                </div>

                <div class="meals-wrapper">
                    @php
                        $dayTotalCal = 0;
                        $dayTotalProtein = 0;
                        $dayTotalCarbs = 0;
                        $dayTotalFat = 0;
                        $dayTotalPrice = 0;
                    @endphp

                    @if(is_array($meals) && count($meals) > 0)
                        @foreach($meals as $meal)
                            @if(is_array($meal))
                                @php
                                    $dayTotalCal += floatval($meal['total_cal'] ?? 0);
                                    $dayTotalProtein += floatval($meal['total_protein'] ?? 0);
                                    $dayTotalCarbs += floatval($meal['total_carbs'] ?? 0);
                                    $dayTotalFat += floatval($meal['total_fat'] ?? 0);
                                    $dayTotalPrice += floatval($meal['total_price'] ?? 0);

                                    $mealClass = isset($meal['ingredients']) ? 'ai-meal' : 'our-meal';
                                @endphp

                                <div class="meal-card {{ $mealClass }}">
                                    <div class="meal-header">
                                        <div class="meal-name">{{ $meal['name'] ?? 'Unnamed Meal' }}</div>
                                        <div class="meal-badges">
                                            <span class="meal-type-badge">{{ $meal['type'] ?? 'meal' }}</span>
                                            @if(isset($meal['time']))
                                                <span class="meal-time">{{ $meal['time'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(isset($meal['description']))
                                        <div class="meal-description">{{ $meal['description'] }}</div>
                                    @endif

                                    @if(isset($meal['ingredients']) && is_array($meal['ingredients']) && count($meal['ingredients']) > 0)
                                        <div class="ingredients-section">
                                            <div class="section-header">Ingredients</div>
                                            <div class="ingredients-list">
                                                @foreach($meal['ingredients'] as $ingredient)
                                                    @if(is_array($ingredient))
                                                        <div class="ingredient-item">
                                                            <span class="item-name">{{ $ingredient['name'] ?? 'Unknown' }}</span>
                                                            <span class="item-amount">{{ $ingredient['amount'] ?? '0g' }}</span>
                                                            <span class="item-details">
                                                                {{ $ingredient['cal'] ?? 0 }}cal • {{ $ingredient['protein'] ?? 0 }}g • AED {{ number_format($ingredient['price'] ?? 0, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($meal['sauces']) && is_array($meal['sauces']) && count($meal['sauces']) > 0)
                                        <div class="sauces-section">
                                            <div class="section-header">Sauces & Seasonings</div>
                                            <div class="ingredients-list">
                                                @foreach($meal['sauces'] as $sauce)
                                                    @if(is_array($sauce))
                                                        <div class="sauce-item">
                                                            <span class="item-name">{{ $sauce['name'] ?? 'Unknown' }}</span>
                                                            <span class="item-amount">{{ $sauce['amount'] ?? '1tbsp' }}</span>
                                                            <span class="item-details">{{ $sauce['cal'] ?? 0 }}cal • AED {{ number_format($sauce['price'] ?? 0, 2) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($meal['instructions']) && !empty($meal['instructions']))
                                        <div class="instructions-box">
                                            <strong>Instructions</strong>
                                            {{ $meal['instructions'] }}
                                        </div>
                                    @endif

                                    @if(isset($meal['quantity']) && !empty($meal['quantity']))
                                        <div class="instructions-box">
                                            <strong>Serving Size</strong>
                                            {{ $meal['quantity'] }}
                                        </div>
                                    @endif

                                    @if(isset($meal['portion_notes']) && !empty($meal['portion_notes']))
                                        <div class="instructions-box">
                                            <strong>Portion Notes</strong>
                                            {{ $meal['portion_notes'] }}
                                        </div>
                                    @endif

                                    <div class="nutrition-summary">
                                        <div class="nutrition-grid">
                                            <div class="nutrition-item">
                                                <span class="nutrition-value">{{ number_format($meal['total_cal'] ?? 0) }}</span>
                                                <span class="nutrition-label">Calories</span>
                                            </div>
                                            <div class="nutrition-item">
                                                <span class="nutrition-value">{{ number_format($meal['total_protein'] ?? 0, 1) }}g</span>
                                                <span class="nutrition-label">Protein</span>
                                            </div>
                                            <div class="nutrition-item">
                                                <span class="nutrition-value">{{ number_format($meal['total_carbs'] ?? 0, 1) }}g</span>
                                                <span class="nutrition-label">Carbs</span>
                                            </div>
                                            <div class="nutrition-item">
                                                <span class="nutrition-value">{{ number_format($meal['total_fat'] ?? 0, 1) }}g</span>
                                                <span class="nutrition-label">Fat</span>
                                            </div>
                                            <div class="nutrition-item">
                                                <span class="nutrition-value">AED {{ number_format($meal['total_price'] ?? 0, 2) }}</span>
                                                <span class="nutrition-label">Cost</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="no-data">
                            <h3>No meals available for Day {{ $dayIndex + 1 }}</h3>
                            <p>Please generate meals for this day.</p>
                        </div>
                    @endif

                    <!-- Day Summary -->
                    @if($dayTotalCal > 0)
                    <div class="day-summary">
                        <div class="day-summary-title">Day {{ $dayIndex + 1 }} Totals</div>
                        <div class="nutrition-grid">
                            <div class="nutrition-item">
                                <span class="nutrition-value" style="color: #1976d2;">{{ number_format($dayTotalCal) }}</span>
                                <span class="nutrition-label" style="color: #1976d2;">Calories</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-value" style="color: #1976d2;">{{ number_format($dayTotalProtein, 1) }}g</span>
                                <span class="nutrition-label" style="color: #1976d2;">Protein</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-value" style="color: #1976d2;">{{ number_format($dayTotalCarbs, 1) }}g</span>
                                <span class="nutrition-label" style="color: #1976d2;">Carbs</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-value" style="color: #1976d2;">{{ number_format($dayTotalFat, 1) }}g</span>
                                <span class="nutrition-label" style="color: #1976d2;">Fat</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-value" style="color: #1976d2;">AED {{ number_format($dayTotalPrice, 2) }}</span>
                                <span class="nutrition-label" style="color: #1976d2;">Daily Cost</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>No Meal Plan Data Available</h3>
            <p>Please generate a meal plan first before downloading the PDF.</p>
        </div>
    @endif

    <!-- Footer -->
{{--    <div class="footer">--}}
{{--        <div class="footer-content">--}}
{{--            <p><span class="footer-brand">24 Digi</span> | Generated on {{ $generated_date }}</p>--}}
{{--            <p>Your Personalized Nutrition Guide • Powered by AI</p>--}}

{{--            <div class="qr-section">--}}
{{--                <p style="margin-bottom: 10px;">Scan to access your digital meal plan:</p>--}}
{{--                <div class="qr-code">--}}
{{--                    [QR Code]--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</body>
</html>
