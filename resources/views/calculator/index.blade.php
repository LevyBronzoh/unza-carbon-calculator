@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #22c55e;
        --primary-dark: #16a34a;
        --secondary: #06b6d4;
        --accent: #f59e0b;
        --light: #f8fafc;
        --light-gray: #f1f5f9;
        --medium-gray: #64748b;
        --dark: #0f172a;
        --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        --gradient-secondary: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
        --gradient-hero: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .calculator-container {
        background: var(--gradient-hero);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        min-height: calc(100vh - 200px);
    }

    .card {
        border-radius: 16px;
        border: none;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1.5rem;
        position: relative;
        background: white;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
        z-index: 1;
    }

    .card-header {
        background: var(--gradient-primary);
        color: white;
        font-weight: 600;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        position: relative;
    }

    .btn-primary {
        background: var(--gradient-primary);
        border: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
    }

    .btn-primary:hover {
        background: var(--gradient-primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.1);
    }

    .btn-outline-primary {
        border: 2px solid var(--primary);
        color: var(--primary);
        background: transparent;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-1px);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.1);
    }

    .progress {
        height: 28px;
        border-radius: 12px;
        background-color: var(--light-gray);
        overflow: hidden;
    }

    .progress-bar {
        background: var(--gradient-primary);
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border-left: 4px solid var(--primary);
    }

    .alert-info {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #075985;
        border-left: 4px solid var(--secondary);
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-left: 4px solid var(--accent);
    }

    .toggle-container {
        display: flex;
        justify-content: center;
        margin: 2rem 0;
    }

    .toggle-btn {
        padding: 0.875rem 2rem;
        border: 2px solid var(--primary);
        background: white;
        color: var(--primary);
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .toggle-btn.active {
        background: var(--primary);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .toggle-btn:first-child {
        border-radius: 12px 0 0 12px;
        border-right: 1px solid var(--primary);
    }

    .toggle-btn:last-child {
        border-radius: 0 12px 12px 0;
        border-left: 1px solid var(--primary);
    }

    .emission-badge {
        font-size: 1.125rem;
        font-weight: 700;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        display: inline-block;
        margin: 0;
    }

    .badge-primary {
        background: rgba(34, 197, 94, 0.15);
        color: var(--primary-dark);
        border: 2px solid rgba(34, 197, 94, 0.3);
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #065f46;
        border: 2px solid rgba(16, 185, 129, 0.3);
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.15);
        outline: none;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.15);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .tooltip-icon {
        color: var(--medium-gray);
        cursor: help;
        margin-left: 0.5rem;
        transition: color 0.3s ease;
    }

    .tooltip-icon:hover {
        color: var(--primary);
    }

    .credit-display {
        font-size: 3rem;
        font-weight: 800;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        line-height: 1.1;
    }

    .stats-card {
        background: var(--light);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .animate-fade-in {
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 0;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .bg-gradient {
        background: var(--gradient-hero);
    }

    .tip-item {
        display: flex;
        align-items: start;
        margin-bottom: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .tip-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .tip-icon {
        color: var(--accent);
        margin-right: 0.75rem;
        margin-top: 0.25rem;
        font-size: 1.1rem;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .calculator-container {
            padding: 1rem;
        }

        .credit-display {
            font-size: 2.5rem;
        }

        .toggle-btn {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
        }
    }
</style>

<div class="container py-4 calculator-container animate-fade-in">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold mb-3 text-dark">UNZA Carbon Calculator</h1>
            <p class="lead text-muted">Track your cooking emissions and earn carbon credits</p>
        </div>
    </div>

    <!-- Toggle View -->
    <div class="toggle-container">
        <button id="monthlyBtn" class="toggle-btn active">Monthly View</button>
        <button id="annualBtn" class="toggle-btn">Annual View</button>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Emissions Summary Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-fire me-2"></i> My Cooking Emissions Summary
                </div>
                <div class="card-body">
                    @if($baselineData && $projectData)
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="alert alert-info h-100">
                                    <h5 class="fw-bold mb-3">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Baseline Emissions
                                    </h5>
                                    <p id="baseline-monthly" class="mb-1">
                                        <i class="fas fa-calendar-day me-2"></i>
                                        Monthly: <strong>{{ number_format($baselineData->monthly_emissions, 4) }} tCO₂e</strong>
                                    </p>
                                    <p id="baseline-annual" style="display: none;" class="mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Annual: <strong>{{ number_format($baselineData->annual_emissions, 4) }} tCO₂e</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success h-100">
                                    <h5 class="fw-bold mb-3">
                                        <i class="fas fa-leaf me-2"></i>
                                        Current Emissions
                                    </h5>
                                    <p id="current-monthly" class="mb-1">
                                        <i class="fas fa-calendar-day me-2"></i>
                                        Monthly: <strong>{{ number_format($projectData->monthly_emissions, 4) }} tCO₂e</strong>
                                    </p>
                                    <p id="current-annual" style="display: none;" class="mb-1">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Annual: <strong>{{ number_format($projectData->annual_emissions, 4) }} tCO₂e</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="emission-comparison">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-seedling me-2"></i>
                                Your Carbon Savings
                            </h5>
                            <div class="progress mb-4">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ min($currentEmissions['percentage_reduction'] ?? 0, 100) }}%"
                                     aria-valuenow="{{ $currentEmissions['percentage_reduction'] ?? 0 }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($currentEmissions['percentage_reduction'] ?? 0, 1) }}% Reduction
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="stats-card">
                                        <p class="mb-2 text-muted fw-semibold">Monthly Savings</p>
                                        <p id="savings-monthly" class="emission-badge badge-primary">
                                            {{ number_format($currentEmissions['monthly_reduction'] ?? 0, 4) }} tCO₂e
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="stats-card">
                                        <p class="mb-2 text-muted fw-semibold">Annual Savings</p>
                                        <p id="savings-annual" style="display: none;" class="emission-badge badge-primary">
                                            {{ number_format($currentEmissions['annual_reduction'] ?? 0, 4) }} tCO₂e
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-card mb-3">
                                <p class="mb-2 text-muted fw-semibold">
                                    <i class="fas fa-award me-2"></i>
                                    Total Credits Earned
                                </p>
                                <p class="emission-badge badge-success">
                                    {{ number_format($projectData->total_credits ?? 0, 2) }} tCO₂e
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            @if(!$baselineData)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-2 fw-bold">Setup Required</h5>
                                        <p class="mb-0">You haven't set up your baseline cooking data yet.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('calculator.create') }}?type=baseline" class="btn btn-primary">
                                        <i class="fas fa-fire me-2"></i> Set Baseline Data
                                    </a>
                                </div>
                            @elseif(!$projectData)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-2 fw-bold">Setup Required</h5>
                                        <p class="mb-0">You haven't entered your clean cooking project data.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('calculator.create') }}?type=project" class="btn btn-success">
                                        <i class="fas fa-leaf me-2"></i> Add Project Data
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(!$baselineData)
            <!-- Baseline Data Capture -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-fire me-2"></i> Baseline Cooking Data
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calculator.store') }}" id="baselineForm">
                        @csrf
                        <input type="hidden" name="calculation_type" value="baseline">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="stove_type" class="form-label">Current Stove Type</label>
                                <select class="form-select" id="stove_type" name="stove_type" required>
                                    <option value="">Select stove type</option>
                                    <option value="3-Stone Fire">3-Stone Fire</option>
                                    <option value="Charcoal Brazier">Charcoal Brazier</option>
                                    <option value="Kerosene Stove">Kerosene Stove</option>
                                    <option value="LPG Stove">LPG Stove</option>
                                    <option value="Electric Stove">Electric Stove</option>
                                    <option value="Improved Biomass Stove">Improved Biomass Stove</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fuel_type" class="form-label">Fuel Type</label>
                                <select class="form-select" id="fuel_type" name="fuel_type" required>
                                    <option value="">Select fuel type</option>
                                    <option value="Wood">Wood</option>
                                    <option value="Charcoal">Charcoal</option>
                                    <option value="LPG">LPG</option>
                                    <option value="Electricity">Electricity</option>
                                    <option value="Ethanol">Ethanol</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="daily_cooking_hours" class="form-label">Daily Cooking Time (hours)</label>
                                <input type="number" step="0.1" min="0" max="24" class="form-control"
                                       id="daily_cooking_hours" name="daily_cooking_hours" required>
                            </div>
                            <div class="col-md-4">
                                <label for="daily_fuel_use" class="form-label">Daily Fuel Use (kg or liters)</label>
                                <input type="number" step="0.1" min="0" class="form-control"
                                       id="daily_fuel_use" name="daily_fuel_use" required>
                            </div>
                            <div class="col-md-4">
                                <label for="efficiency" class="form-label">
                                    Stove Efficiency (%)
                                    <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                       title="Leave blank to use default efficiency for your stove type"></i>
                                </label>
                                <input type="number" step="0.1" min="1" max="100" class="form-control"
                                       id="efficiency" name="efficiency">
                            </div>
                            <div class="col-md-6">
                                <label for="household_size" class="form-label">Household Size</label>
                                <input type="number" min="1" max="20" class="form-control"
                                       id="household_size" name="household_size" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <i class="fas fa-calculator me-2"></i> Calculate Baseline Emissions
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($baselineData && !$projectData)
            <!-- Project Intervention Data -->
            <div class="card">
                <div class="card-header" style="background: var(--gradient-secondary);">
                    <i class="fas fa-leaf me-2"></i> Cleaner Cooking Intervention
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calculator.store') }}" id="projectForm">
                        @csrf
                        <input type="hidden" name="calculation_type" value="project">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="new_stove_type" class="form-label">New Stove Type</label>
                                <select class="form-select" id="new_stove_type" name="new_stove_type" required>
                                    <option value="">Select new stove type</option>
                                    <option value="Improved Biomass Stove">Improved Biomass Stove</option>
                                    <option value="LPG Stove">LPG Stove</option>
                                    <option value="Electric Stove">Electric Stove</option>
                                    <option value="Ethanol Stove">Ethanol Stove</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="new_fuel_type" class="form-label">New Fuel Type</label>
                                <select class="form-select" id="new_fuel_type" name="new_fuel_type" required>
                                    <option value="">Select new fuel type</option>
                                    <option value="LPG">LPG</option>
                                    <option value="Electricity">Electricity</option>
                                    <option value="Ethanol">Ethanol</option>
                                    <option value="Wood">Wood (Improved)</option>
                                    <option value="Charcoal">Charcoal (Improved)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fuel_use_project" class="form-label">Daily Fuel Use (kg or liters)</label>
                                <input type="number" step="0.1" min="0" class="form-control"
                                       id="fuel_use_project" name="fuel_use_project" required>
                            </div>
                            <div class="col-md-4">
                                <label for="new_efficiency" class="form-label">New Stove Efficiency (%)</label>
                                <input type="number" step="0.1" min="1" max="100" class="form-control"
                                       id="new_efficiency" name="new_efficiency">
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date of Clean Cooking</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-success w-100 py-3">
                                    <i class="fas fa-calculator me-2"></i> Calculate Emissions Saved
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Weekly Update Card -->
            @if($projectData)
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-calendar-week me-2"></i> Weekly Update
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4"></i>
                            <div>
                                <h5 class="mb-1 fw-bold">Tracking Reminder</h5>
                                <p class="mb-0">Update your weekly cooking data for accurate credit tracking</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('calculator.weekly.update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="actual_fuel_use" class="form-label">This Week's Fuel Use (kg/liters)</label>
                                <input type="number" step="0.01" min="0" class="form-control"
                                       id="actual_fuel_use" name="actual_fuel_use" required>
                            </div>
                            <div class="col-md-4">
                                <label for="cooking_hours" class="form-label">Cooking Hours This Week</label>
                                <input type="number" step="0.1" min="0" class="form-control"
                                       id="cooking_hours" name="cooking_hours" required>
                            </div>
                            <div class="col-md-4">
                                <label for="week_start_date" class="form-label">Week Starting</label>
                                <input type="date" class="form-control" id="week_start_date"
                                       name="week_start_date" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <i class="fas fa-upload me-2"></i> Update My Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        @if(!$baselineData)
                            <a href="{{ route('calculator.create') }}?type=baseline"
                               class="btn btn-outline-primary text-start py-3">
                                <i class="fas fa-fire me-3"></i> Add Baseline Data
                            </a>
                        @endif
                        @if($baselineData && !$projectData)
                            <a href="{{ route('calculator.create') }}?type=project"
                               class="btn btn-outline-success text-start py-3">
                                <i class="fas fa-leaf me-3"></i> Add Project Data
                            </a>
                        @endif
                        <a href="#" class="btn btn-outline-secondary text-start py-3">
                            <i class="fas fa-file-export me-3"></i> Export My Data
                        </a>
                        <a href="#" class="btn btn-outline-info text-start py-3">
                            <i class="fas fa-chart-line me-3"></i> View Progress Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Your Carbon Credits Card -->
            @if($projectData)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-award me-2"></i> Your Carbon Credits
                </div>
                <div class="card-body text-center">
                    <div class="credit-display">
                        {{ number_format($projectData->total_credits ?? 0, 2) }}
                    </div>
                    <p class="mb-1 fw-semibold">tonnes CO₂e saved</p>
                    <p class="small text-muted">Since: {{ $projectData->start_date ? $projectData->start_date->format('M Y') : 'N/A' }}</p>
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-primary py-2">
                            <i class ="fas fa-certificate me-2"></i> Verify Credits
                        </button>
                        <button class="btn btn-outline-success py-2">
                            <i class="fas fa-share-alt me-2"></i> Share Achievement
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tips & Insights Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lightbulb me-2"></i> Tips & Insights
                </div>
                <div class="card-body">
                    <div class="tip-item">
                        <i class="fas fa-leaf tip-icon"></i>
                        <div>
                            <p class="mb-1 fw-semibold">Maximize Your Impact</p>
                            <p class="small text-muted mb-0">Using your clean cooking stove consistently can increase your carbon credits by up to 40%</p>
                        </div>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-clock tip-icon"></i>
                        <div>
                            <p class="mb-1 fw-semibold">Track Regularly</p>
                            <p class="small text-muted mb-0">Weekly updates help ensure accurate credit calculations and better monitoring</p>
                        </div>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-users tip-icon"></i>
                        <div>
                            <p class="mb-1 fw-semibold">Household Participation</p>
                            <p class="small text-muted mb-0">Involving all household members in clean cooking practices maximizes emissions reduction</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environmental Impact Card -->
            @if($projectData)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-globe-africa me-2"></i> Environmental Impact
                </div>
                <div class="card-body">
                    <div class="stats-card mb-3">
                        <p class="mb-2 text-muted fw-semibold">Trees Equivalent</p>
                        <p class="emission-badge badge-success">
                            {{ number_format(($projectData->total_credits ?? 0) * 40, 0) }} trees
                        </p>
                        <p class="small text-muted">Trees that would absorb the same CO₂</p>
                    </div>
                    <div class="stats-card">
                        <p class="mb-2 text-muted fw-semibold">Car Miles Offset</p>
                        <p class="emission-badge badge-primary">
                            {{ number_format(($projectData->total_credits ?? 0) * 2204, 0) }} miles
                        </p>
                        <p class="small text-muted">Equivalent car emissions avoided</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between monthly and annual view
    const monthlyBtn = document.getElementById('monthlyBtn');
    const annualBtn = document.getElementById('annualBtn');

    monthlyBtn.addEventListener('click', function() {
        toggleView('monthly');
    });

    annualBtn.addEventListener('click', function() {
        toggleView('annual');
    });

    function toggleView(view) {
        // Update button states
        if (view === 'monthly') {
            monthlyBtn.classList.add('active');
            annualBtn.classList.remove('active');

            // Show monthly data, hide annual
            document.querySelectorAll('[id*="-monthly"]').forEach(el => el.style.display = 'block');
            document.querySelectorAll('[id*="-annual"]').forEach(el => el.style.display = 'none');
        } else {
            annualBtn.classList.add('active');
            monthlyBtn.classList.remove('active');

            // Show annual data, hide monthly
            document.querySelectorAll('[id*="-annual"]').forEach(el => el.style.display = 'block');
            document.querySelectorAll('[id*="-monthly"]').forEach(el => el.style.display = 'none');
        }
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Ripple effect for buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Add loading state to submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            }
        });
    });

    // Auto-set week start date to current week
    const weekStartInput = document.getElementById('week_start_date');
    if (weekStartInput) {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - dayOfWeek);
        weekStartInput.value = startOfWeek.toISOString().split('T')[0];
    }
});
</script>

@endsection
