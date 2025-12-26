<?php

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\WorkoutController;

Volt::route('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

// Route for template store is now handled within the Volt component.



//Route::name('filament.admin.auth.')->group(function () {
//    Route::get('filament/login', fn () => redirect()->route('login'))->name('login');
//    Route::post('filament/logout', fn () => redirect()->route('logout', 307))->name('logout');
//    Route::get('filament/register', fn () => redirect()->route('register'))->name('register');
//    Route::get('filament/password-reset/request', fn () => redirect()->route('password.request'))->name('password-reset.request');
//    Route::get('filament/password-reset/reset/{token}', fn ($token) => redirect()->route('password.reset', ['token' => $token]))->name('password-reset.reset');
//    Route::get('filament/email-verification/prompt', fn () => redirect()->route('verification.notice'))->name('email-verification.prompt');
//    Route::get('filament/email-verification/verify/{id}/{hash}', fn ($id, $hash) => redirect()->route('verification.verify', ['id' => $id, 'hash' => $hash]))->name('email-verification.verify');
//});

Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::prefix('workout')
        ->name('workout.')
        ->group(function () {
            Route::get('/create', [WorkoutController::class, 'create'])->name('create');
            Volt::route('/{workout}', 'workout')->name('edit');
        });
});
