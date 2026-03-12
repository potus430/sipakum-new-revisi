<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([UserSeeder::class]);
    }
}


// Route::view('/', 'welcome')->name('home');

// // Middleware auth untuk semua yang login
// Route::middleware(['auth', 'verified'])->group(function () {
    
//     Route::view('dashboard', 'dashboard')->name('dashboard');

//     // Khusus Admin & Superadmin
//     Route::middleware(['role:superadmin,admin'])->group(function () {
//         Route::view('arsip', 'arsip.index')->name('arsip.index');
//     });

//     // Khusus Superadmin saja (Manajemen User)
//     Route::middleware(['role:superadmin'])->group(function () {
//         Route::view('users', 'users.index')->name('users.index');
//     });
// });

// require __DIR__.'/settings.php';