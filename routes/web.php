<?php

use App\Livewire\CreatePerdata;
use App\Livewire\CreatePidana;
use App\Livewire\EditPerdata;
use App\Livewire\PerdataManagement;
use Illuminate\Support\Facades\Route;
use App\Livewire\PidanaManagement;
use App\Livewire\Dashboard;

//Route::view('/', 'welcome')->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::view('dashboard', 'dashboard')->name('dashboard');
// });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // Route Modul Register Pidana
    Route::get('/pidana', PidanaManagement::class)->name('pidana.index');
    Route::get('/pidana/create', CreatePidana::class)->name('pidana.create');
    Route::get('/pidana/{id}/edit', \App\Livewire\EditPidana::class)->name('pidana.edit');

    // Rute Modul Perdata
    Route::prefix('perdata')->name('perdata.')->group(function () {
        Route::get('/', PerdataManagement::class)->name('index');
        Route::get('/create', CreatePerdata::class)->name('create');
        Route::get('/{id}/edit', EditPerdata::class)->name('edit');
    });
});

require __DIR__ . '/settings.php';
