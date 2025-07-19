<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baseline Data Submitted - UNZA Carbon Calculator</title>
    <!-- Developed by Levy Bronzoh, Climate Yanga. -->
    <!-- Include Tailwind CSS for styling. This is a CDN link for simplicity in development. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Apply the Inter font family to the entire body for consistent typography. */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6; /* Light gray background for the page. */
        }
        /* Custom styling for cards to ensure consistent appearance. */
        .card {
            background-color: #ffffff; /* White background for cards. */
            border-radius: 0.75rem; /* Rounded corners for cards. */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth. */
            padding: 2rem; /* Padding inside cards. */
            max-width: 500px; /* Maximum width for the card. */
            margin: auto; /* Center the card horizontally. */
        }
        /* Styling for buttons to give them a consistent look and feel. */
        .btn-primary {
            background-color: #059669; /* Green background for primary buttons. */
            color: #ffffff; /* White text color. */
            padding: 0.75rem 1.5rem; /* Padding for button text. */
            border-radius: 0.5rem; /* Rounded corners for buttons. */
            transition: background-color 0.3s ease; /* Smooth transition on hover. */
        }
        .btn-primary:hover {
            background-color: #047857; /* Darker green on hover. */
        }
        .btn-secondary {
            background-color: #6b7280; /* Gray background for secondary buttons. */
            color: #ffffff; /* White text color. */
            padding: 0.75rem 1.5rem; /* Padding for button text. */
            border-radius: 0.5rem; /* Rounded corners for buttons. */
            transition: background-color 0.3s ease; /* Smooth transition on hover. */
        }
        .btn-secondary:hover {
            background-color: #4b5563; /* Darker gray on hover. */
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Master Layout Template inclusion. This assumes you have a master layout file. -->
    @include('layouts.master') <!-- This line assumes you have a master.blade.php in resources/views/layouts/ -->

    <div class="flex-grow container mx-auto p-6 lg:p-10 flex items-center justify-center">
        <div class="card text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Baseline Data Submitted Successfully!</h1>
            <p class="text-lg text-gray-600 mb-6">
                Your initial cooking habits and emissions have been recorded.
                Now you can proceed to record your project intervention data to start tracking your carbon savings.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <!-- Link to the Project Data creation form. -->
                <a href="{{ route('project_data.create') }}" class="btn-primary inline-block text-center w-full sm:w-auto">
                    Record Project Data
                </a>
                <!-- Link back to the user's dashboard. -->
                <a href="{{ route('dashboard') }}" class="btn-secondary inline-block text-center w-full sm:w-auto">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
@if(session('emissions'))
<div class="alert alert-success">
    Success! Your {{ session('fuel_type') }} usage generates
    {{ session('emissions') }} tCO₂e monthly.
</div>
@endif
    <!-- Basic Footer (can be moved to master layout) -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-auto">
        <p>&copy; {{ date('Y') }} UNZA Carbon Calculator. All rights reserved.</p>
        <p>Developed by Levy Bronzoh, Climate Yanga.</p>
    </footer>
</body>
</html>
