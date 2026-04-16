<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect home to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// ===========================
// AUTHENTICATION ROUTES
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ===========================
// USER TASK ROUTES
// ===========================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('dashboard');
    
    // Tasks management
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::get('/{task}/submit-completion', [TaskController::class, 'showCompletionForm'])->name('completion-form');
        Route::post('/{task}/submit-completion', [TaskController::class, 'submitCompletion'])->name('submit-completion');
    });
});

// ===========================
// ADMIN ROUTES
// ===========================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Task requests management
    Route::prefix('task-requests')->name('task-requests.')->group(function () {
        Route::get('/', [AdminController::class, 'taskRequests'])->name('index');
        Route::get('/pending', [AdminController::class, 'pendingRequests'])->name('pending');
        Route::post('/{taskRequest}/approve', [AdminController::class, 'approveRequest'])->name('approve');
        Route::post('/{taskRequest}/reject', [AdminController::class, 'rejectRequest'])->name('reject');
    });
    
    // Task completions verification
    Route::prefix('completions')->name('completions.')->group(function () {
        Route::get('/', [AdminController::class, 'completions'])->name('index');
        Route::get('/pending', [AdminController::class, 'pendingCompletions'])->name('pending');
        Route::post('/{completion}/verify', [AdminController::class, 'verifyCompletion'])->name('verify');
        Route::post('/{completion}/reject', [AdminController::class, 'rejectCompletion'])->name('reject');
    });
    
    // Tasks filtering
    Route::get('/tasks/filter', [AdminController::class, 'filterTasks'])->name('tasks.filter');
});
