<?php

namespace App\Livewire;

use App\Exports\Modules\PerdataExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Berkas;
use App\Models\BerkasFile;
use App\Traits\HandlesFiles;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class PerdataManagement extends Component
{
    use WithPagination, HandlesFiles;
    use WithFileUploads;
    public $files = [];

    public $search = '';
    public $filterJenis = '';
    public $startDate = '';
    public $endDate = '';

    // Method untuk menghapus data perkara perdata
    public function delete($id)
    {
        $berkas = Berkas::findOrFail($id);

        // 1. Hapus file fisik dari storage
        foreach ($berkas->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        // 2. Hapus record file dari database (akan terhapus otomatis via cascade jika diatur,
        // tapi lebih aman dihapus eksplisit jika tidak)
        $berkas->files()->delete();

        // 3. Hapus record berkas
        $berkas->delete();

        // 4. Berikan notifikasi
        session()->flash('success', 'Data perkara dan semua dokumen terkait telah dihapus.');
    }

    // Method Ekspor
    public function exportExcel()
    {
        return Excel::download(
            new PerdataExport($this->startDate, $this->endDate),
            'laporan_perdata_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportPDF()
    {
        $query = Berkas::modul('perdata');
        if ($this->startDate) $query->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->whereDate('created_at', '<=', $this->endDate);

        $data = $query->get();
        $pdf = Pdf::loadView('pdf.perdata', ['data' => $data, 'start' => $this->startDate, 'end' => $this->endDate]);
        return response()->streamDownload(fn() => print($pdf->output()), 'perdata_report - ' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        // $query = Berkas::modul('perdata')
        //     ->where(function ($q) {
        //         $q->where('nomor_registrasi', 'like', '%' . $this->search . '%')
        //             ->orWhere('subjek', 'like', '%' . $this->search . '%');
        //     });

        $query = Berkas::modul('perdata')
            ->where(function ($q) {
                $q->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                    ->orWhere('metadata->penggugat', 'like', '%' . $this->search . '%');
            });

        if ($this->filterJenis) $query->where('metadata->jenis_perkara', $this->filterJenis);

        // Filter Tanggal
        if ($this->startDate) $query->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $query->whereDate('created_at', '<=', $this->endDate);

        // Menambahkan filter berdasarkan metadata JSON
        if (!empty($this->filterJenis)) {
            $query->where('metadata->jenis_perkara', $this->filterJenis);
        }

        $perdata = $query->latest()->paginate(20);

        return view('livewire.perdata-management', compact('perdata'));
    }
}
