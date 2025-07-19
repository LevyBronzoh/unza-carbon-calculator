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

// ======================
// PUBLIC ROUTES
// ======================

// Home and static pages
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/methodology', [HomeController::class, 'methodology'])->name('methodology');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// Authentication routes
Auth::routes(['register' => true]); // Enable registration if needed

// Success page (accessible without auth)
Route::get('/success', [BaselineDataController::class, 'showSuccess'])->name('success.view');

// ======================
// AUTH-PROTECTED ROUTES
// ======================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

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

        // Legacy routes (if needed for backward compatibility)
        Route::get('/form', [BaselineDataController::class, 'showForm'])->name('baseline.form');
        Route::post('/submit', [BaselineDataController::class, 'submitData'])->name('baseline.submit');
    });

    // Project Data (requires baseline)
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
});
// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Dashboard Route (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
// In web.php or routes file
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Admin dashboard routes (for staff only)
Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/admin/export', [DashboardController::class, 'exportUserData'])->name('admin.export');
