@extends('layouts.app')

@section('title', 'UNZA Carbon Calculator Dashboard')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    .highlight-section {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 30px 20px;
        margin-top: 40px;
        margin-bottom: 20px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    .highlight-section h3 {
        font-weight: 700;
        color: #2c3e50;
    }
    .highlight-section p {
        color: #7f8c8d;
        margin: 10px 0;
    }
    .floating-words {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: hidden;
        z-index: 0;
    }
    .floating-words span {
        position: absolute;
        font-size: 1.2rem;
        font-weight: bold;
        color: rgba(0, 0, 0, 0.05);
        animation: floatWords 20s linear infinite;
    }
    @keyframes floatWords {
        0% {
            transform: translateY(100%);
            opacity: 0;
        }
        25% {
            opacity: 1;
        }
        100% {
            transform: translateY(-150%);
            opacity: 0;
        }
    }
    .dashboard-header {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f59e0b;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }
    .logo-section {
        display: flex;
        align-items: center;
    }
    .logo {
        font-size: 2rem;
        margin-right: 15px;
        color: white;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <!-- HEADER -->
    <div class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="title-section">
                        <h1 class="h4 mb-0">UNZA Carbon Calculator</h1>
                        <p class="mb-0 small">Cooking Emissions Tracker • unza.climateyanga.com</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <div>
                        <div><strong>{{ Auth::user()->name }}</strong></div>
                        <div class="small">{{ Auth::user()->user_type }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">My Carbon Dashboard</h2>
            </div>
        </div>

        <!-- Baseline & Project Data -->
        <div class="row">
            <!-- Baseline Data Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Baseline Cooking Data</h5>
                    </div>
                    <div class="card-body">
                        @if($baselineData)
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Stove Type:</span>
                                    <span class="fw-bold">{{ $baselineData->stove_type ?? 'Not specified' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Fuel Type:</span>
                                    <span class="fw-bold">{{ $baselineData->fuel_type ?? 'Not specified' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Daily Cooking Time:</span>
                                    <span class="fw-bold">{{ $baselineData->daily_hours ?? 0 }} hours</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Daily Fuel Use:</span>
                                    <span class="fw-bold">{{ $baselineData->daily_fuel_use ?? 0 }} kg/liters</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Estimated Monthly Emissions:</span>
                                    <span class="fw-bold text-danger">{{ number_format($baselineData->emission_total ?? 0, 3) }} tCO₂e</span>
                                </li>
                            </ul>
                            <div class="mt-3">
                                <a href="{{ route('baseline.edit', $baselineData->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit Baseline
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                You haven't entered your baseline cooking data yet.
                            </div>
                            <a href="{{ route('baseline.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Add Baseline Data
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Project Data Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Clean Cooking Intervention</h5>
                    </div>
                    <div class="card-body">
                        @if($projectData)
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>New Stove Type:</span>
                                    <span class="fw-bold">{{ $projectData->new_stove_type ?? 'Not specified' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>New Fuel Type:</span>
                                    <span class="fw-bold">{{ $projectData->new_fuel_type ?? 'Not specified' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Estimated Monthly Emissions:</span>
                                    <span class="fw-bold text-danger">{{ number_format($projectData->emissions_after ?? 0, 3) }} tCO₂e</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Monthly Emission Reduction:</span>
                                    <span class="fw-bold text-success">{{ number_format($projectData->credits_earned ?? 0, 3) }} tCO₂e</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Since:</span>
                                    <span class="fw-bold">{{ optional($projectData->start_date)->format('M d, Y') ?? 'Not specified' }}</span>
                                </li>
                            </ul>
                            <div class="mt-3">
                                <a href="{{ route('project.edit', $projectData->id) }}" class="btn btn-outline-success">
                                    <i class="fas fa-edit me-1"></i> Edit Project
                                </a>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                You haven't entered your clean cooking intervention data yet.
                            </div>
                            @if($baselineData)
                                <a href="{{ route('project.create') }}" class="btn btn-success">
                                    <i class="fas fa-leaf me-1"></i> Add Clean Cooking Data
                                </a>
                            @else
                                <button class="btn btn-success" disabled title="Please enter baseline data first">
                                    <i class="fas fa-leaf me-1"></i> Add Clean Cooking Data
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Metrics Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Carbon Reduction Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Monthly Reduction</h6>
                                        <h3 class="text-success">{{ number_format($dashboardMetrics['monthly_credits_earned'] ?? 0, 3) }} tCO₂e</h3>
                                        <p class="small text-muted">Credits earned this month</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Reduction</h6>
                                        <h3 class="text-primary">{{ number_format($dashboardMetrics['cumulative_credits'] ?? 0, 3) }} tCO₂e</h3>
                                        <p class="small text-muted">Since project started</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Efficiency Gain</h6>
                                        <h3 class="text-warning">{{ $dashboardMetrics['percentage_reduction'] ?? 0 }}%</h3>
                                        <p class="small text-muted">Compared to baseline</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="highlight-section">
            <div class="floating-words">
                <span style="left: 10%; animation-delay: 0s;">Carbon Calculator</span>
                <span style="left: 30%; animation-delay: 5s;">Progress Tracking</span>
                <span style="left: 50%; animation-delay: 10s;">Earn Credits</span>
                <span style="left: 70%; animation-delay: 15s;">Carbon Calculator</span>
                <span style="left: 90%; animation-delay: 20s;">Progress Tracking</span>
            </div>
            <h3>Carbon Calculator</h3>
            <p>Track your daily emissions and discover ways to reduce your carbon footprint.</p>

            <h3 class="mt-4">Progress Tracking</h3>
            <p>Monitor your sustainability journey with detailed analytics and insights.</p>

            <h3 class="mt-4">Earn Credits</h3>
            <p>Get rewarded with carbon credits for reducing your emissions effectively.</p>
        </div>

        <!-- Weekly Update -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Weekly Update</h5>
                    </div>
                    <div class="card-body">
                        @if($projectData)
                            <form method="POST" action="{{ route('project.update') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="current_fuel_use" class="form-label">Current Daily Fuel Use (kg or liters)</label>
                                        <input type="number" step="0.01" class="form-control" id="current_fuel_use" name="current_fuel_use" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="current_hours" class="form-label">Current Daily Cooking Time (hours)</label>
                                        <input type="number" step="0.1" class="form-control" id="current_hours" name="current_hours" required>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i> Submit Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Please enter your clean cooking intervention data first to start tracking weekly updates.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => card.classList.add('shadow-lg'));
            card.addEventListener('mouseleave', () => card.classList.remove('shadow-lg'));
        });

        // Real-time Simulation: Update credits earned every 10 seconds
        const creditDisplay = document.querySelector('.credits-stat');
        if (creditDisplay) {
            setInterval(() => {
                const value = parseFloat(creditDisplay.textContent) || 0;
                const increment = (Math.random() * 0.02).toFixed(2);
                creditDisplay.textContent = (value + parseFloat(increment)).toFixed(2);
            }, 10000);
        }
    });
</script>
@endsection
