@extends('layouts.app')

@section('title', 'Methodology - UNZA Carbon Calculator')

@section('content')
<div class="methodology-container py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bold mb-3">Calculation Methodology</h1>
                <p class="lead text-muted">Verra VM0042 Methodology for Improved Cookstoves</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Overview</h2>
                        <p>Our carbon calculations follow the internationally recognized <strong>Verra VM0042 Methodology</strong> for Improved Cookstoves, which provides a standardized approach to quantify greenhouse gas emission reductions from clean cooking interventions.</p>
                        <p>The methodology has been adapted to the Zambian context with local emission factors and cooking patterns.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Key Formulas</h2>
                        <div class="formula-card mb-4 p-3 bg-light rounded">
                            <h5>1. Baseline Emissions</h5>
                            <p class="mb-1">E<sub>baseline</sub> = (F<sub>baseline</sub> × EF<sub>fuel</sub>) / η<sub>baseline</sub></p>
                            <p class="text-muted small">Where:</p>
                            <ul class="text-muted small">
                                <li>F<sub>baseline</sub> = Fuel consumption before intervention</li>
                                <li>EF<sub>fuel</sub> = Emission factor for the fuel type</li>
                                <li>η<sub>baseline</sub> = Efficiency of baseline stove</li>
                            </ul>
                        </div>

                        <div class="formula-card mb-4 p-3 bg-light rounded">
                            <h5>2. Project Emissions</h5>
                            <p class="mb-1">E<sub>project</sub> = (F<sub>project</sub> × EF<sub>fuel</sub>) / η<sub>project</sub></p>
                            <p class="text-muted small">Where:</p>
                            <ul class="text-muted small">
                                <li>F<sub>project</sub> = Fuel consumption after intervention</li>
                                <li>η<sub>project</sub> = Efficiency of improved stove</li>
                            </ul>
                        </div>

                        <div class="formula-card p-3 bg-light rounded">
                            <h5>3. Emission Reduction</h5>
                            <p class="mb-1">ER = E<sub>baseline</sub> - E<sub>project</sub></p>
                            <p class="text-muted small">This represents the carbon credits earned</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Data Collection</h2>
                        <p>We collect the following data points for accurate calculations:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mt-3">Baseline Data</h5>
                                <ul>
                                    <li>Stove type (3-stone, charcoal brazier, etc.)</li>
                                    <li>Fuel type (wood, charcoal, LPG, etc.)</li>
                                    <li>Daily fuel consumption</li>
                                    <li>Cooking hours per day</li>
                                    <li>Household size</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mt-3">Project Data</h5>
                                <ul>
                                    <li>Improved stove type</li>
                                    <li>New fuel type (if changed)</li>
                                    <li>Observed fuel consumption</li>
                                    <li>Stove efficiency measurements</li>
                                    <li>Start date of intervention</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Need More Details?</h2>
                        <p>For the complete methodology documentation, please visit:</p>
                        <a href="https://verra.org/methodologies/vm0042" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i> Verra VM0042 Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .methodology-container {
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .formula-card {
        border-left: 4px solid #22c55e;
    }
</style>
@endsection
