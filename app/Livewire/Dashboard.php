<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Menghitung statistik untuk dashboard
        $totalPidana = Berkas::where('modul', 'pidana')->count();
        $totalPerdata = Berkas::where('modul', 'perdata')->count();
        $totalDokumen = BerkasFile::count();

        // Mengambil 5 aktivitas perkara terakhir untuk ditampilkan di tabel
        $recentBerkas = Berkas::latest()->take(5)->get();

        // Mengembalikan view 'dashboard' (sesuaikan jika path view Anda berbeda)
        return view('dashboard', [
            'totalPidana' => $totalPidana,
            'totalPerdata' => $totalPerdata,
            'totalDokumen' => $totalDokumen,
            'recentBerkas' => $recentBerkas,
        ]);
    }
}
