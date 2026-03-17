<?php

namespace App\Livewire;

use App\Models\Pengaduan;
use App\Models\PengaduanLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\Modules\PengaduanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PengaduanManager extends Component
{
    use WithFileUploads, WithPagination;

    // Properti sesuai revisi
    public $judul, $pelapor, $terlapor, $jenis_pengaduan, $sarana_pengaduan;
    public $isi_pengaduan, $tindak_lanjut, $keterangan, $status = 'Terima';
    public $is_anonim = false;
    public $file_pendukung, $selectedId;


    // Properti Filter
    public $search = '';
    public $startDate, $endDate;

    public $selectedPengaduan;

    // Reset halaman saat filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate([
            'judul' => 'required|min:5',
            'pelapor' => 'required',
            'terlapor' => 'required',
            'jenis_pengaduan' => 'required',
            'sarana_pengaduan' => 'required',
            'isi_pengaduan' => 'required',
        ]);

        $pengaduanLama = Pengaduan::find($this->selectedId);

        // Handle File Upload
        $path = $this->file_pendukung
            ? $this->file_pendukung->store('pengaduan', 'public')
            : ($pengaduanLama?->file_pendukung);

        $pengaduan = Pengaduan::updateOrCreate(['id' => $this->selectedId], [
            'judul' => $this->judul,
            'pelapor' => $this->pelapor,
            'terlapor' => $this->terlapor,
            'jenis_pengaduan' => $this->jenis_pengaduan,
            'sarana_pengaduan' => $this->sarana_pengaduan,
            'isi_pengaduan' => $this->isi_pengaduan,
            'tindak_lanjut' => $this->tindak_lanjut,
            'keterangan' => $this->keterangan,
            'anonim' => $this->is_anonim ? 'Ya' : 'Tidak',
            'status' => $this->status,
            'file_pendukung' => $path,
            'user_id' => Auth::id(),
        ]);

        // Catat Log jika status berubah
        if ($pengaduanLama && $pengaduanLama->status !== $this->status) {
            PengaduanLog::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id' => Auth::id(),
                'status_lama' => $pengaduanLama->status,
                'status_baru' => $this->status,
            ]);
        }

        session()->flash('success', 'Data Pengaduan berhasil diproses.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $this->selectedId = $pengaduan->id;
        $this->judul = $pengaduan->judul;
        $this->pelapor = $pengaduan->pelapor;
        $this->terlapor = $pengaduan->terlapor;
        $this->jenis_pengaduan = $pengaduan->jenis_pengaduan;
        $this->sarana_pengaduan = $pengaduan->sarana_pengaduan;
        $this->isi_pengaduan = $pengaduan->isi_pengaduan;
        $this->tindak_lanjut = $pengaduan->tindak_lanjut;
        $this->keterangan = $pengaduan->keterangan;
        $this->status = $pengaduan->status;
        $this->is_anonim = $pengaduan->anonim === 'Ya';

        $this->js('$flux.modal("modal-update-status").show()');
    }

    public function resetForm()
    {
        $this->reset([
            'judul',
            'pelapor',
            'terlapor',
            'jenis_pengaduan',
            'sarana_pengaduan',
            'isi_pengaduan',
            'tindak_lanjut',
            'keterangan',
            'status',
            'selectedId',
            'is_anonim',
            'file_pendukung'
        ]);
    }

    public function render()
    {
        $pengaduans = Pengaduan::with('user')
            ->when($this->search, function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('pelapor', 'like', '%' . $this->search . '%')
                    ->orWhere('terlapor', 'like', '%' . $this->search . '%');
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.pengaduan-manager', [
            'pengaduans' => $pengaduans
        ]);
    }

    // Fungsi tambahan untuk download file
    public function downloadFile($id)
    {
        $item = Pengaduan::findOrFail($id);
        if ($item->file_pendukung && Storage::disk('public')->exists($item->file_pendukung)) {
            return Storage::disk('public')->download($item->file_pendukung);
        }
        session()->flash('error', 'File tidak ditemukan.');
    }

    public function viewDetail($id)
    {
        // Mengambil data detail pengaduan berdasarkan ID
        $this->selectedPengaduan = Pengaduan::with('user')->find($id);

        // Memicu modal detail untuk muncul di browser
        if ($this->selectedPengaduan) {
            $this->js('$flux.modal("modal-detail-pengaduan").show()');
        }
    }

    public function exportExcel()
    {
        return Excel::download(
            new PengaduanExport($this->startDate, $this->endDate, $this->search),
            'register_pengaduan_' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        // Menggunakan query yang sama dengan tabel
        $data = Pengaduan::query()
            ->when($this->search, function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('pelapor', 'like', '%' . $this->search . '%');
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->get();

        $pdf = Pdf::loadView('pdf.pengaduan', [
            'data' => $data,
            'start' => $this->startDate,
            'end' => $this->endDate
        ])->setPaper('a4', 'landscape'); // Landscape karena kolomnya banyak

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'laporan_pengaduan_' . now()->format('Ymd') . '.pdf'
        );
    }
}
