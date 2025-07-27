<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AssetController as AdminAssetController;
use App\Http\Controllers\Admin\AssetRequestController as AdminAssetRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\AssetController as ManagerAssetController;
use App\Http\Controllers\Manager\AssetRequestController as ManagerAssetRequestController;
use App\Http\Controllers\Surveyor\DashboardController as SurveyorDashboardController;
use App\Http\Controllers\Surveyor\AssetController as SurveyorAssetController;
use App\Http\Controllers\Surveyor\AssetRequestController as SurveyorAssetRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\PublicController::class, 'index'])->name('public.landing');

// Public Assets (for public users - no authentication required)
Route::get('/assets', [\App\Http\Controllers\PublicController::class, 'index'])->name('public.assets');
Route::get('/assets/{asset}', [\App\Http\Controllers\PublicController::class, 'show'])->name('public.asset.show');
Route::get('/api/search-assets', [\App\Http\Controllers\PublicController::class, 'search'])->name('public.assets.search');

// Session Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/check-session', [\App\Http\Controllers\SessionController::class, 'checkSession'])->name('session.check');
    Route::post('/extend-session', [\App\Http\Controllers\SessionController::class, 'extendSession'])->name('session.extend');
    Route::post('/clear-session', [\App\Http\Controllers\SessionController::class, 'clearSession'])->name('session.clear');
});

Route::get('/dashboard', function () {
    // Clear any stale session data and verify authentication
    if (!auth()->check()) {
        session()->flush();
        return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
    }

    $user = auth()->user();

    // Verify session integrity
    if (!session()->has('auth_verified') || session('user_role') !== $user->role) {
        session()->flush();
        auth()->logout();
        return redirect()->route('login')->with('error', 'Session expired. Please login again.');
    }

    // Only allow admin, manager, and surveyor roles
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'manager':
            return redirect()->route('manager.dashboard');
        case 'surveyor':
            return redirect()->route('surveyor.dashboard');
        default:
            // If user has invalid role, logout and redirect
            auth()->logout();
            session()->flush();
            return redirect()->route('login')->with('error', 'Invalid user role. Please contact administrator.');
    }
})->middleware(['auth', 'verified'])->name('dashboard');Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Asset Management
    Route::resource('assets', AdminAssetController::class);

    // Asset Document Management
    Route::prefix('assets/{asset}/documents')->name('assets.documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'store'])->name('store');
        Route::get('/{document}/edit', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [\App\Http\Controllers\Admin\AssetDocumentController::class, 'download'])->name('download');
    });

    // Asset Request Management
    Route::resource('asset-requests', AdminAssetRequestController::class);
    Route::patch('/asset-requests/{assetRequest}/approve', [AdminAssetRequestController::class, 'approve'])->name('asset-requests.approve');
    Route::patch('/asset-requests/{assetRequest}/reject', [AdminAssetRequestController::class, 'reject'])->name('asset-requests.reject');

    // User Management
    Route::resource('users', AdminUserController::class);
});

// Manager Routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

    // Asset Management
    Route::resource('assets', ManagerAssetController::class);

    // Asset Document Management
    Route::prefix('assets/{asset}/documents')->name('assets.documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'store'])->name('store');
        Route::get('/{document}/edit', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [\App\Http\Controllers\Manager\AssetDocumentController::class, 'download'])->name('download');
    });

    // Asset Request Management
    Route::resource('asset-requests', ManagerAssetRequestController::class);
    Route::patch('/asset-requests/{assetRequest}/approve', [ManagerAssetRequestController::class, 'approve'])->name('asset-requests.approve');
    Route::patch('/asset-requests/{assetRequest}/reject', [ManagerAssetRequestController::class, 'reject'])->name('asset-requests.reject');
});

// Surveyor Routes
Route::middleware(['auth', 'role:surveyor'])->prefix('surveyor')->name('surveyor.')->group(function () {
    Route::get('/dashboard', [SurveyorDashboardController::class, 'index'])->name('dashboard');

    // Asset Viewing
    Route::get('/assets', [SurveyorAssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [SurveyorAssetController::class, 'show'])->name('assets.show');

    // Asset Request Management
    Route::get('/requests', [SurveyorAssetRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [SurveyorAssetRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [SurveyorAssetRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{assetRequest}/edit', [SurveyorAssetRequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{assetRequest}', [SurveyorAssetRequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{assetRequest}', [SurveyorAssetRequestController::class, 'destroy'])->name('requests.destroy');
    Route::get('/requests/{assetRequest}', [SurveyorAssetRequestController::class, 'show'])->name('requests.show');
});

require __DIR__.'/auth.php';
