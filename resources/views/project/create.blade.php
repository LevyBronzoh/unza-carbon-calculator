@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Enter Your Clean Cooking Intervention</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('project.store') }}">
                        @csrf

                        <!-- New Stove Type -->
                        <div class="mb-3">
                            <label for="new_stove_type" class="form-label">New Stove Type</label>
                            <select class="form-select" id="new_stove_type" name="new_stove_type" required>
                                <option value="">Select Stove Type</option>
                                <option value="3-Stone Fire">3-Stone Fire</option>
                                <option value="Charcoal Brazier">Charcoal Brazier</option>
                                <option value="Kerosene Stove">Kerosene Stove</option>
                                <option value="LPG Stove">LPG Stove</option>
                                <option value="Electric Stove">Electric Stove</option>
                                <option value="Improved Biomass Stove">Improved Biomass Stove</option>
                            </select>
                        </div>

                        <!-- New Fuel Type -->
                        <div class="mb-3">
                            <label for="new_fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="new_fuel_type" name="new_fuel_type" required>
                                <option value="">Select Fuel Type</option>
                                <option value="Wood">Wood</option>
                                <option value="Charcoal">Charcoal</option>
                                <option value="LPG">LPG</option>
                                <option value="Electricity">Electricity</option>
                                <option value="Ethanol">Ethanol</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Daily Fuel Use -->
                        <div class="mb-3">
                            <label for="fuel_use_project" class="form-label">Estimated Daily Fuel Use (kg or liters)</label>
                            <input type="number" step="0.01" class="form-control" id="fuel_use_project" name="fuel_use_project" required>
                        </div>

                        <!-- Stove Efficiency -->
                        <div class="mb-3">
                            <label for="new_efficiency" class="form-label">Stove Efficiency (optional)</label>
                            <input type="number" step="0.01" class="form-control" id="new_efficiency" name="new_efficiency">
                            <div class="form-text">Leave blank to use default value</div>
                        </div>

                        <!-- Start Date -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date of Cleaner Cooking</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <!-- Observed Reduction -->
                        <div class="mb-3">
                            <label for="observed_reduction" class="form-label">Observed % Reduction (if known)</label>
                            <input type="number" step="0.1" class="form-control" id="observed_reduction" name="observed_reduction">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Project Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
