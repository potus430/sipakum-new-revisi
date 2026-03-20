<?php

namespace App\Livewire;

use App\Models\SuratKuasa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class SuratKuasaManager extends Component
{
    use WithFileUploads;

    // Ubah berkas_id menjadi kategori_perkara
    public $kategori_perkara = 'Pidana';
    public $nomor_surat, $tanggal_surat, $penerima, $pemberi;
    public $file_kuasa;
    public $jenis = 'Khusus';

    public $filterKategori = '';
    public $startDate = '';
    public $endDate = '';

    public $isEditing = false;
    public $selectedId;
    public $search = '';
    public $detail = null;

    public $old_file;

    public function save()
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }

        $this->validate([
            'kategori_perkara' => 'required|in:Pidana,Perdata',
            'nomor_surat' => 'required|unique:surat_kuasa,nomor_surat_kuasa,' . $this->selectedId,
            'tanggal_surat' => 'required|date',
            'pemberi' => 'required',
            'penerima' => 'required',
            'jenis' => 'required|in:Khusus,Substitusi,Insidentil',
            'file_kuasa' => $this->isEditing ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
        ]);

        $data = [
            'kategori_perkara' => $this->kategori_perkara,
            'nomor_surat_kuasa' => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat,
            'pemberi_kuasa' => $this->pemberi,
            'penerima_kuasa' => $this->penerima,
            'jenis_kuasa' => $this->jenis,
        ];

        if ($this->file_kuasa) {
            // Hapus file lama jika sedang edit
            if ($this->isEditing && $this->selectedId) {
                $old = SuratKuasa::find($this->selectedId);
                if ($old->file_path)
                    Storage::disk('public')->delete($old->file_path);
            }
            $data['file_path'] = $this->file_kuasa->store('surat-kuasa', 'public');
        }

        SuratKuasa::updateOrCreate(['id' => $this->selectedId], $data);

        $this->dispatch('notify', variant: 'success', message: 'Surat Kuasa berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        // Proteksi Logic: Pastikan hanya Admin/Superadmin yang bisa mengeksekusi
        if (!Auth::user() || !in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk memodifikasi data.');
        }
        $kuasa = SuratKuasa::findOrFail($id);
        $this->selectedId = $id;
        $this->kategori_perkara = $kuasa->kategori_perkara;
        $this->nomor_surat = $kuasa->nomor_surat_kuasa;
        $this->tanggal_surat = $kuasa->tanggal_surat->format('Y-m-d');
        $this->penerima = $kuasa->penerima_kuasa;
        $this->pemberi = $kuasa->pemberi_kuasa;
        $this->jenis = $kuasa->jenis_kuasa;

        // Simpan path file lama untuk ditampilkan di view
        $this->old_file = $kuasa->file_path;

        $this->isEditing = true;
    }

    public function delete($id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        try {
            $suratKuasa = SuratKuasa::findOrFail($id);

            // Hapus file fisik dari storage jika ada
            if ($suratKuasa->file_path) {
                Storage::disk('public')->delete($suratKuasa->file_path);
            }

            // Hapus data dari database
            $suratKuasa->delete();

            $this->dispatch(
                'notify',
                variant: 'success',
                heading: 'Berhasil',
                message: 'Data surat kuasa berhasil dihapus.'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                variant: 'error',
                heading: 'Gagal',
                message: 'Data tidak ditemukan atau gagal dihapus.'
            );
        }
    }

    public function showDetail($id)
    {
        $this->detail = SuratKuasa::find($id);
        $this->dispatch('modal-show', name: 'detail-kuasa'); // Sesuaikan dengan cara Flux membuka modal
    }

    public function resetForm()
    {
        $this->reset(['kategori_perkara', 'nomor_surat', 'tanggal_surat', 'penerima', 'pemberi', 'jenis', 'selectedId', 'isEditing', 'file_kuasa', 'old_file']);
        $this->kategori_perkara = 'Pidana';
    }

    public function render()
    {
        $daftarKuasa = SuratKuasa::query()
            ->where(function ($q) {
                $q->where('nomor_surat_kuasa', 'like', '%' . $this->search . '%')
                    ->orWhere('pemberi_kuasa', 'like', '%' . $this->search . '%')
                    ->orWhere('penerima_kuasa', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        return view('livewire.surat-kuasa-manager', [
            'daftarKuasa' => $daftarKuasa,
        ]);
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Modules\SuratKuasaExport($this->startDate, $this->endDate, $this->filterKategori),
            'laporan_surat_kuasa_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        // Mengambil data berdasarkan filter yang sedang aktif
        $data = SuratKuasa::query()
            ->when($this->search, function ($q) {
                $q->where('nomor_surat_kuasa', 'like', '%' . $this->search . '%')
                    ->orWhere('pemberi_kuasa', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterKategori, function ($q) {
                $q->where('kategori_perkara', $this->filterKategori);
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('tanggal_surat', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('tanggal_surat', '<=', $this->endDate);
            })
            ->latest()
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat-kuasa', [
            'data' => $data,
            'start' => $this->startDate,
            'end' => $this->endDate
        ]);

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'laporan_surat_kuasa_' . now()->format('Y-m-d') . '.pdf'
        );
    }
}
