@extends('layouts.app')

@section('title', 'About UNZA Carbon Calculator')

@section('content')
<div class="about-container py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bold mb-3">About the UNZA Carbon Calculator</h1>
                <p class="lead text-muted">Understanding our mission and methodology</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold mb-4">Our Mission</h2>
                <p>The UNZA Carbon Calculator empowers students and staff at the University of Zambia to track and reduce their cooking-related carbon emissions through sustainable practices.</p>
                <p>By providing accurate measurements and incentives, we aim to:</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent">Reduce deforestation from fuelwood collection</li>
                    <li class="list-group-item bg-transparent">Improve indoor air quality on campus</li>
                    <li class="list-group-item bg-transparent">Create carbon credit opportunities for the UNZA community</li>
                    <li class="list-group-item bg-transparent">Promote adoption of clean cooking technologies</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="fw-bold mb-4">Emission Factors</h3>
                        <p class="text-muted mb-4">We use scientifically validated emission factors for different fuel types:</p>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fuel Type</th>
                                    <th>Emission Factor (tCO₂e/kg or tCO₂e/l)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emissionFactors as $fuel => $factor)
                                <tr>
                                    <td>{{ $fuel }}</td>
                                    <td>{{ number_format($factor, 6) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">The Team</h2>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <img src="{{ asset('images/team/fred-kisela.jpg') }}" alt="Fred Kisela" class="rounded-circle mb-3" width="120">
                                    <h5>Fred Kisela</h5>
                                    <p class="text-muted">Founder & Lead Developer</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <img src="{{ asset('images/team/levy-bronzoh.jpg') }}" alt="Levy Bronzoh" class="rounded-circle mb-3" width="120">
                                    <h5>Levy Bronzoh</h5>
                                    <p class="text-muted">Emissions Specialist</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <img src="{{ asset('images/team/unza-team.jpg') }}" alt="UNZA Team" class="rounded-circle mb-3" width="120">
                                    <h5>UNZA Research Team</h5>
                                    <p class="text-muted">Field Researchers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .about-container {
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .list-group-item {
        padding-left: 0;
        border-left: 0;
    }
</style>
@endsection
