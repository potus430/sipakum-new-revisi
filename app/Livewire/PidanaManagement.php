<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Modules\Exports\PidanaExport as ExportsPidanaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PidanaManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Properti Filter & Pencarian
    public $search = '';
    public $filterJenis = '';
    public $tglMulai = '';
    public $tglSelesai = '';

    // Reset pagination saat filter berubah
    public function updated($property)
    {
        if (in_array($property, ['search', 'filterJenis', 'tglMulai', 'tglSelesai'])) {
            $this->resetPage();
        }
    }

    /**
     * Base Query untuk mempermudah sinkronisasi antara tabel dan ekspor
     */
    public function getPidanaQuery()
    {
        return Berkas::query()
            ->where('modul', 'pidana')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                        ->orWhere('subjek', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterJenis, function ($q) {
                $q->where('metadata->jenis_perkara', $this->filterJenis);
            })
            ->when($this->tglMulai, function ($q) {
                $q->whereDate('tanggal_kejadian', '>=', $this->tglMulai);
            })
            ->when($this->tglSelesai, function ($q) {
                $q->whereDate('tanggal_kejadian', '<=', $this->tglSelesai);
            })
            ->with(['files', 'creator'])
            ->latest();
    }

    /**
     * Fungsi Ekspor Excel
     */
    public function exportExcel()
    {
        // Memanggil class export dengan mengirimkan parameter tanggal filter
        return Excel::download(
            new ExportsPidanaExport($this->tglMulai, $this->tglSelesai),
            'Register_Pidana_' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    /**
     * Fungsi Ekspor PDF
     */
    public function exportPdf()
    {
        // 1. Replikasi Logika Query dari PerdataManagement
        $query = Berkas::where('modul', 'pidana')
            ->where(function ($q) {
                $q->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                    ->orWhere('subjek', 'like', '%' . $this->search . '%');
            });

        if ($this->filterJenis) {
            $query->where('metadata->jenis_perkara', $this->filterJenis);
        }

        if ($this->tglMulai) {
            $query->whereDate('tanggal_kejadian', '>=', $this->tglMulai);
        }

        if ($this->tglSelesai) {
            $query->whereDate('tanggal_kejadian', '<=', $this->tglSelesai);
        }

        $data = $query->latest()->get();

        // 2. Load View (Buat file view ini di langkah selanjutnya)
        $pdf = Pdf::loadView('pdf.pidana', [
            'data' => $data,
            'start' => $this->tglMulai,
            'end' => $this->tglSelesai
        ]);

        // 3. Menggunakan streamDownload agar sesuai dengan perilaku Livewire
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_pidana_' . now()->format('Y-m-d') . '.pdf');
    }

    public function delete($id)
    {
        $berkas = Berkas::with('files')->findOrFail($id);

        // 1. Hapus file fisik dari storage
        foreach ($berkas->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        // 2. Hapus data (files akan ikut terhapus jika ada cascade atau manual delete)
        $berkas->files()->delete();
        $berkas->delete();

        // 3. Notifikasi Toast
        $this->dispatch(
            'notify',
            variant: 'error',
            heading: 'Data Dihapus',
            message: 'Perkara dan seluruh file terkait telah dihapus.'
        );
    }

    public function render()
    {
        return view('livewire.pidana-management', [
            'listPidana' => $this->getPidanaQuery()->paginate(10)
        ]);
    }
}
