<?php

use App\Livewire\CreatePerdata;
use App\Livewire\CreatePidana;
use App\Livewire\EditPerdata;
use App\Livewire\PerdataManagement;
use Illuminate\Support\Facades\Route;
use App\Livewire\PidanaManagement;
use App\Livewire\Dashboard;
use App\Livewire\WaarmerkingManager;
use App\Livewire\GratifikasiManager;
use App\Livewire\PengaduanManager;

//Route::view('/', 'welcome')->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::view('dashboard', 'dashboard')->name('dashboard');
// });

Route::get('/register', function () {
    return view('pages::auth.register'); // Sesuaikan dengan lokasi view register Anda
})->name('register');

Route::middleware(['auth', 'verified','check.status'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/user-management', \App\Livewire\UserManagement::class)
        ->name('user.management')
        ->middleware('can:is-superadmin'); // Pastikan memiliki gate/middleware ini

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

    //Surat Kuasa
    Route::get('/surat-kuasa', \App\Livewire\SuratKuasaManager::class)->name('surat-kuasa.index');

    // Rute Buku Register
    //Route::get('/register', \App\Livewire\RegisterIndex::class)->name('register.index');
    Route::get('/buku-register', \App\Livewire\RegisterIndex::class)->name('register.index');

    //Route Waarmerking
    Route::get('/waarmerking', WaarmerkingManager::class)->name('waarmerking.index');

    //Route Gratifikasi
    Route::get('/gratifikasi', GratifikasiManager::class)->name('gratifikasi.index');

    //Route Pengaduan
    Route::get('/pengaduan', PengaduanManager::class)->name('pengaduan.index');
});

require __DIR__ . '/settings.php';
