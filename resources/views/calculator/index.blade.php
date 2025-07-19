@extends('layouts.app')

@section('content')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border: none;
    }
    .card-header {
        background-color: #2c7873;
        color: white;
        border-radius: 10px 10px 0 0 !important;
        font-weight: 600;
    }
    .btn-primary {
        background-color: #2c7873;
        border-color: #2c7873;
    }
    .btn-primary:hover {
        background-color: #235f5b;
        border-color: #235f5b;
    }
    .emission-result {
        background-color: #e8f4f3;
        border-left: 4px solid #2c7873;
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
    }
    .progress {
        height: 25px;
        border-radius: 5px;
    }
    .progress-bar {
        background-color: #2c7873;
    }
    .tooltip-icon {
        color: #6c757d;
        cursor: pointer;
    }
    .toggle-container {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .toggle-btn {
        padding: 8px 20px;
        border: 1px solid #2c7873;
        background: white;
        color: #2c7873;
        cursor: pointer;
    }
    .toggle-btn.active {
        background: #2c7873;
        color: white;
    }
    .toggle-btn:first-child {
        border-radius: 5px 0 0 5px;
    }
    .toggle-btn:last-child {
        border-radius: 0 5px 5px 0;
    }
    .tip-card {
        border-left: 4px solid #ffc107;
    }
    .emission-comparison {
        margin-top: 20px;
    }
</style>

<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4">UNZA Carbon Calculator</h1>
            <p class="lead">Track your cooking emissions and carbon credits</p>
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
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-fire"></i> My Cooking Emissions Summary
                </div>
                <div class="card-body">
                    @if($baselineData && $projectData)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h5>Baseline Emissions</h5>
                                    <p id="baseline-monthly">Monthly: {{ number_format($baselineData->monthly_emissions, 4) }} tCO₂e</p>
                                    <p id="baseline-annual" style="display: none;">Annual: {{ number_format($baselineData->annual_emissions, 4) }} tCO₂e</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <h5>Current Emissions</h5>
                                    <p id="current-monthly">Monthly: {{ number_format($projectData->monthly_emissions, 4) }} tCO₂e</p>
                                    <p id="current-annual" style="display: none;">Annual: {{ number_format($projectData->annual_emissions, 4) }} tCO₂e</p>
                                </div>
                            </div>
                        </div>

                        <div class="emission-comparison">
                            <h5>Your Carbon Savings</h5>
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $currentEmissions['percentage_reduction'] ?? 0 }}%"
                                     aria-valuenow="{{ $currentEmissions['percentage_reduction'] ?? 0 }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($currentEmissions['percentage_reduction'] ?? 0, 1) }}% Reduction
                                </div>
                            </div>
                            <p id="savings-monthly">Monthly Savings: {{ number_format($currentEmissions['monthly_reduction'] ?? 0, 4) }} tCO₂e</p>
                            <p id="savings-annual" style="display: none;">Annual Savings: {{ number_format($currentEmissions['annual_reduction'] ?? 0, 4) }} tCO₂e</p>
                            <p>Total Credits Earned: <strong>{{ number_format($projectData->total_credits ?? 0, 2) }} tCO₂e</strong></p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            @if(!$baselineData)
                                <p><i class="bi bi-exclamation-triangle"></i> You haven't set up your baseline cooking data yet.</p>
                                <a href="{{ route('calculator.create') }}?type=baseline" class="btn btn-primary">
                                    <i class="bi bi-house-door"></i> Set Baseline Data
                                </a>
                            @elseif(!$projectData)
                                <p><i class="bi bi-exclamation-triangle"></i> You haven't entered your clean cooking project data.</p>
                                <a href="{{ route('calculator.create') }}?type=project" class="btn btn-success">
                                    <i class="bi bi-tree"></i> Add Project Data
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(!$baselineData)
            <!-- Baseline Data Capture -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-house-door"></i> Baseline Cooking Data
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calculator.store') }}" id="baselineForm">
                        @csrf
                        <input type="hidden" name="type" value="baseline">
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
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="daily_hours" class="form-label">Daily Cooking Time (hours)</label>
                                <input type="number" step="0.1" class="form-control" id="daily_hours" name="daily_hours" required>
                            </div>
                            <div class="col-md-4">
                                <label for="daily_fuel_use" class="form-label">Daily Fuel Use (kg or liters)</label>
                                <input type="number" step="0.1" class="form-control" id="daily_fuel_use" name="daily_fuel_use" required>
                            </div>
                            <div class="col-md-4">
                                <label for="efficiency" class="form-label">
                                    Stove Efficiency (%)
                                    <i class="bi bi-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                       title="Leave blank to use default efficiency for your stove type"></i>
                                </label>
                                <input type="number" step="0.1" class="form-control" id="efficiency" name="efficiency">
                            </div>
                            <div class="col-md-6">
                                <label for="household_size" class="form-label">Household Size</label>
                                <input type="number" class="form-control" id="household_size" name="household_size" required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calculator"></i> Calculate Baseline Emissions
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($baselineData && !$projectData)
            <!-- Project Intervention Data -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-tree"></i> Cleaner Cooking Intervention
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calculator.store') }}" id="projectForm">
                        @csrf
                        <input type="hidden" name="type" value="project">
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
                                <label for="fuel_use_project" class="form-label">Estimated Daily Fuel Use (kg or liters)</label>
                                <input type="number" step="0.1" class="form-control" id="fuel_use_project" name="fuel_use_project" required>
                            </div>
                            <div class="col-md-4">
                                <label for="new_efficiency" class="form-label">New Stove Efficiency (%)</label>
                                <input type="number" step="0.1" class="form-control" id="new_efficiency" name="new_efficiency">
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date of Cleaner Cooking</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-calculator"></i> Calculate Emissions Saved
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Weekly Update Card -->
            @if($projectData)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar-week"></i> Weekly Update
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Remember to update your weekly cooking data for accurate credit tracking.
                    </div>

                    <form method="POST" action="{{ route('calculator.weekly.update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="actual_fuel_use" class="form-label">This Week's Fuel Use (kg/liters)</label>
                                <input type="number" step="0.01" class="form-control" id="actual_fuel_use"
                                       name="actual_fuel_use" required>
                            </div>
                            <div class="col-md-4">
                                <label for="cooking_hours" class="form-label">Cooking Hours This Week</label>
                                <input type="number" step="0.1" class="form-control" id="cooking_hours"
                                       name="cooking_hours" required>
                            </div>
                            <div class="col-md-4">
                                <label for="week_start_date" class="form-label">Week Starting</label>
                                <input type="date" class="form-control" id="week_start_date"
                                       name="week_start_date" required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Update My Data
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
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$baselineData)
                            <a href="{{ route('calculator.create') }}?type=baseline"
                               class="btn btn-outline-primary btn-sm mb-2">
                                <i class="bi bi-house-door"></i> Add Baseline Data
                            </a>
                        @endif
                        @if($baselineData && !$projectData)
                            <a href="{{ route('calculator.create') }}?type=project"
                               class="btn btn-outline-success btn-sm mb-2">
                                <i class="bi bi-tree"></i> Add Project Data
                            </a>
                        @endif
                        <a href="#" class="btn btn-outline-info btn-sm mb-2">
                            <i class="bi bi-file-earmark-arrow-down"></i> Export My Data
                        </a>
                        <a href="#" class="btn btn-outline-warning btn-sm mb-2">
                            <i class="bi bi-graph-up"></i> View Progress Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Your Carbon Credits Card -->
            @if($projectData)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-award"></i> Your Carbon Credits
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4">{{ number_format($projectData->total_credits ?? 0, 2) }}</h2>
                    <p class="mb-1">tonnes CO₂e saved</p>
                    <p class="small text-muted">Since: {{ $projectData->start_date ?? 'N/A' }}</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-file-text"></i> View Credit Report
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Clean Cooking Tips Card -->
            <div class="card tip-card mb-4">
                <div class="card-header">
                    <i class="bi bi-lightbulb"></i> Clean Cooking Tips
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="bi bi-gear"></i> Improve Your Stove Efficiency</h6>
                        <p class="small">Using an improved biomass stove can reduce fuel use by 30-60% compared to traditional stoves.</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="bi bi-lightning"></i> Consider LPG or Electric</h6>
                        <p class="small">LPG and electric stoves produce significantly fewer emissions and are better for indoor air quality.</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="bi bi-people"></i> Community Benefits</h6>
                        <p class="small">Every tonne of CO₂e reduced helps UNZA contribute to Zambia's climate goals.</p>
                    </div>
                    <hr>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-book"></i> More Tips & Resources
                    </a>
                </div>
            </div>

            <!-- Recent Calculations -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($calculations ?? [] as $calculation)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ ucfirst($calculation->type ?? 'Unknown') }}</strong>
                                <br>
                                <small class="text-muted">{{ $calculation->created_at->format('M d, Y') ?? 'Unknown date' }}</small>
                            </div>
                            <span class="badge bg-{{ $calculation->type === 'baseline' ? 'primary' : 'success' }}">
                                {{ ucfirst($calculation->type ?? 'Unknown') }}
                            </span>
                        </li>
                        @empty
                        <li class="list-group-item">
                            <i class="bi bi-info-circle"></i> No calculations yet. Get started with your baseline data!
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize date picker for week starting date
    const weekStartDate = document.getElementById('week_start_date');
    if (weekStartDate) {
        weekStartDate.valueAsDate = new Date();
    }

    // Toggle between monthly and annual view
    document.getElementById('monthlyBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('annualBtn').classList.remove('active');

        // Show monthly data
        const monthlyElements = document.querySelectorAll('[id*="-monthly"], [id*="savings-monthly"]');
        const annualElements = document.querySelectorAll('[id*="-annual"], [id*="savings-annual"]');

        monthlyElements.forEach(el => el.style.display = 'block');
        annualElements.forEach(el => el.style.display = 'none');
    });

    document.getElementById('annualBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('monthlyBtn').classList.remove('active');

        // Show annual data
        const monthlyElements = document.querySelectorAll('[id*="-monthly"], [id*="savings-monthly"]');
        const annualElements = document.querySelectorAll('[id*="-annual"], [id*="savings-annual"]');

        monthlyElements.forEach(el => el.style.display = 'none');
        annualElements.forEach(el => el.style.display = 'block');
    });

    // AJAX for quick calculations (optional)
    const quickCalcForm = document.getElementById('quickCalcForm');
    if (quickCalcForm) {
        quickCalcForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch("{{ route('calculator.quick.calculate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    fuel_type: document.getElementById('fuel_type').value,
                    daily_fuel_use: document.getElementById('daily_fuel_use').value
                })
            })
            .then(response => response.json())
            .then(data => {
                const quickResult = document.getElementById('quickResult');
                if (quickResult) {
                    quickResult.innerHTML = `
                        <p>Estimated Monthly: ${data.monthly.toFixed(4)} tCO₂e</p>
                        <p>Estimated Annual: ${data.annual.toFixed(4)} tCO₂e</p>
                    `;
                    quickResult.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    // Form validation and user feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endsection
