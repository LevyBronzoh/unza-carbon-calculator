<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BaselineDataController;
use App\Http\Controllers\TipsController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ProjectDataController;
use App\Http\Controllers\WeeklyUpdateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalculatorController;

// ======================
// PUBLIC ROUTES
// ======================



// Calculator route for storing data
Route::post('/calculator', [CalculatorController::class, 'store'])
    ->name('calculator.store');
// Home page should be the default root
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/methodology', [HomeController::class, 'methodology'])->name('methodology');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// Authentication routes
Auth::routes(['register' => true]); // Enable registration

// Custom login/logout/register routes (optional override)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Success page (accessible without auth)
Route::get('/success', [BaselineDataController::class, 'showSuccess'])->name('success.view');

// ======================
// AUTH-PROTECTED ROUTES
// ======================
Route::middleware(['auth'])->group(function () {

    // Home / Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Admin-specific profile views
        Route::get('/{user}', [ProfileController::class, 'show'])->name('profile.user.show');
        Route::get('/{user}/edit', [ProfileController::class, 'edit'])->name('profile.user.edit');
        Route::patch('/{user}', [ProfileController::class, 'update'])->name('profile.user.update');
    });

    // Baseline Data
    Route::prefix('baseline')->group(function () {
        Route::get('/create', [BaselineDataController::class, 'create'])->name('baseline.create');
        Route::post('/', [BaselineDataController::class, 'store'])->name('baseline.store');
        Route::get('/{id}/edit', [BaselineDataController::class, 'edit'])->name('baseline.edit');
        Route::put('/{id}', [BaselineDataController::class, 'update'])->name('baseline.update');

        Route::get('/form', [BaselineDataController::class, 'showForm'])->name('baseline.form');
        Route::post('/submit', [BaselineDataController::class, 'submitData'])->name('baseline.submit');
    });

    // Project Data
    Route::prefix('project')->middleware('has.baseline')->group(function () {
        Route::get('/create', [ProjectDataController::class, 'create'])->name('project.create');
        Route::post('/', [ProjectDataController::class, 'store'])->name('project.store');
        Route::get('/{id}', [ProjectDataController::class, 'show'])->name('project.show');
        Route::get('/{id}/edit', [ProjectDataController::class, 'edit'])->name('project.edit');
        Route::put('/{id}', [ProjectDataController::class, 'update'])->name('project.update');
    });

    // Weekly Updates
    Route::prefix('weekly')->group(function () {
        Route::get('/update', [WeeklyUpdateController::class, 'create'])->name('weekly.update');
        Route::post('/store', [WeeklyUpdateController::class, 'store'])->name('weekly.store');
    });

    // Results and Tips
    Route::get('/results', [ResultsController::class, 'show'])->name('results.show');
    Route::get('/tips', [TipsController::class, 'index'])->name('tips.index');

    // Calculator
    Route::prefix('calculator')->group(function () {
        Route::get('/', [CalculatorController::class, 'index'])->name('calculator.index');
        Route::post('/', [CalculatorController::class, 'store'])->name('calculator.store');
        Route::get('/create', [CalculatorController::class, 'create'])->name('calculator.create');
        Route::get('/{id}', [CalculatorController::class, 'show'])->name('calculator.show');
        Route::put('/{id}', [CalculatorController::class, 'update'])->name('calculator.update');
        Route::delete('/{id}', [CalculatorController::class, 'destroy'])->name('calculator.destroy');
        Route::post('/weekly-update', [CalculatorController::class, 'weeklyUpdate'])
            ->name('calculator.weekly.update');
        Route::post('/calculator/quick-calculate', [CalculatorController::class, 'quickCalculate'])
        ->name('calculator.quick.calculate');
    });

    // Admin-only Routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/export', [DashboardController::class, 'exportUserData'])->name('admin.export');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics.index');
    });
});
