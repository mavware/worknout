<?php

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\WorkoutController;

Route::get('/', function () {
    return view('dashboard');
})->name('home');

Route::prefix('workout')
    ->name('workout.')
    ->group(function () {
    Route::get('/create', [WorkoutController::class, 'create'])->name('create');
    Volt::route('/{workout}', 'workout')->name('edit');
});

Route::post('/templates', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    Template::create($validated);

    return redirect()->route('dashboard');
})->name('template.store');



//Route::name('filament.admin.auth.')->group(function () {
//    Route::get('filament/login', fn () => redirect()->route('login'))->name('login');
//    Route::post('filament/logout', fn () => redirect()->route('logout', 307))->name('logout');
//    Route::get('filament/register', fn () => redirect()->route('register'))->name('register');
//    Route::get('filament/password-reset/request', fn () => redirect()->route('password.request'))->name('password-reset.request');
//    Route::get('filament/password-reset/reset/{token}', fn ($token) => redirect()->route('password.reset', ['token' => $token]))->name('password-reset.reset');
//    Route::get('filament/email-verification/prompt', fn () => redirect()->route('verification.notice'))->name('email-verification.prompt');
//    Route::get('filament/email-verification/verify/{id}/{hash}', fn ($id, $hash) => redirect()->route('verification.verify', ['id' => $id, 'hash' => $hash]))->name('email-verification.verify');
//});

Route::view('dashboard', 'dashboard')
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
});
