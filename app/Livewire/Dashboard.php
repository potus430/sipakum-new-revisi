<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use App\Models\Pengaduan; // Pastikan model ini ada
use App\Models\Gratifikasi; // Pastikan model ini ada
use Livewire\Component;
use App\Exports\LaporanSIPAKUMExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class Dashboard extends Component
{

    public $startDate, $endDate;
    public function render()
    {
        // Statistik Pengaduan untuk Dashboard Ringkasan
        $totalPengaduan = Pengaduan::count();
        $pengaduanSelesai = Pengaduan::where('status', 'Selesai')->count();
        $pengaduanProses = Pengaduan::whereIn('status', ['Terima', 'Verifikasi', 'Investigasi'])->count();

        // Hitung Persentase Penyelesaian
        $persentaseSelesai = $totalPengaduan > 0
            ? round(($pengaduanSelesai / $totalPengaduan) * 100)
            : 0;

        return view('dashboard', [
            // Statistik Perkara Utama
            'totalPidana' => Berkas::where('modul', 'pidana')->count(),
            'totalPerdata' => Berkas::where('modul', 'perdata')->count(),
            'totalDokumen' => BerkasFile::count(),

            // Statistik Modul Pengaduan (Detail untuk Ringkasan)
            'pengaduanStats' => [
                'total' => $totalPengaduan,
                'selesai' => $pengaduanSelesai,
                'proses' => $pengaduanProses,
                'persen_selesai' => $persentaseSelesai,
            ],

            // Statistik Ringkas (Tetap dipertahankan untuk kompatibilitas view lama jika ada)
            'stats' => [
                'pengaduan_pending' => Pengaduan::where('status', 'Terima')->count(),
                'pengaduan_proses' => $pengaduanProses,
                'gratifikasi_total' => Gratifikasi::count(),
            ],

            // Aktivitas Terbaru
            'recentBerkas' => Berkas::latest()->take(5)->get(),
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new LaporanSIPAKUMExport($this->startDate, $this->endDate), 'laporan_sipakum.xlsx');
    }

    public function exportPDF()
    {
        $data = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'data' => [
                'pidana' => Berkas::where('modul', 'pidana')->whereBetween('created_at', [$this->startDate, $this->endDate])->count(),
                'perdata' => Berkas::where('modul', 'perdata')->whereBetween('created_at', [$this->startDate, $this->endDate])->count(),
                'pengaduan' => Pengaduan::whereBetween('created_at', [$this->startDate, $this->endDate])->count(),
                'gratifikasi' => Gratifikasi::whereBetween('created_at', [$this->startDate, $this->endDate])->count(),
            ]
        ];

        $pdf = Pdf::loadView('pdf.laporan', $data);
        return response()->streamDownload(fn() => print ($pdf->output()), 'laporan_sipakum.pdf');
    }
}
