<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\UserMenuPermissionController;
use App\Http\Controllers\UserProgramController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::match(['GET', 'POST'], '/login', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Change password
Route::get('/change-password', [LoginController::class, 'changePasswordForm'])->name('change.password.form');
Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change.password');

// Dashboard
Route::middleware('token_all')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Super Admin
Route::middleware('token_super_admin')
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        // Users
        Route::get('/users/datatable', [UsersController::class, 'datatable'])->name('users.datatable');
        Route::post('/users/clear-filter', [UsersController::class, 'clearFilter'])->name('users.clearFilter');
        Route::resource('users', UsersController::class);

        // Programs
        Route::get('/programs/datatable', [ProgramController::class, 'datatable'])->name('programs.datatable');
        Route::resource('programs', ProgramController::class);

        // Menus
        Route::get('/menus/datatable', [MenuController::class, 'datatable'])->name('menus.datatable');
        Route::resource('menus', MenuController::class);

        // User Menu Permissions
        Route::get('/user-menu-permissions', [UserMenuPermissionController::class, 'index'])
            ->name('user-menu-permissions.index');
        Route::get('/user-menu-permissions/{user_id}/edit', [UserMenuPermissionController::class, 'edit'])
            ->name('user-menu-permissions.edit');
        Route::put('/user-menu-permissions/{user_id}', [UserMenuPermissionController::class, 'update'])
            ->name('user-menu-permissions.update');

        // User Program Permissions
        Route::get('/user-program-permissions', [UserProgramController::class, 'index'])
            ->name('user-program-permissions.index');
        Route::get('/user-program-permissions/{user_id}/edit', [UserProgramController::class, 'edit'])
            ->name('user-program-permissions.edit');
        Route::put('/user-program-permissions/{user_id}', [UserProgramController::class, 'update'])
            ->name('user-program-permissions.update');
    });
