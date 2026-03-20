<?php

namespace App\Livewire;

use App\Models\Waarmerking;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // Pastikan library terinstall
use Barryvdh\DomPDF\Facade\Pdf;      // Pastikan library terinstall

class WaarmerkingManager extends Component
{
    use WithPagination;

    public $nomor_register, $nama_pemohon, $tanggal_legalisasi, $catatan;

    // Properti Filter (Baru)
    public $search = '';
    public $startDate = '';
    public $endDate = '';

    public $selectedId;
    public $isEditing = false;
    public $selectedRegister = null;

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

    // Helper Query agar seragam antara Table & Ekspor
    protected function getQuery()
    {
        return Waarmerking::query()
            ->when($this->search, function ($q) {
                $q->where('nomor_register', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_pemohon', 'like', '%' . $this->search . '%');
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('tanggal_legalisasi', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('tanggal_legalisasi', '<=', $this->endDate);
            })
            ->latest();
    }

    // Fungsi Ekspor PDF
    public function exportPdf()
    {
        // Mengambil data menggunakan query yang sama dengan tabel
        $data = $this->getQuery()->get();

        // Load view dan kirimkan data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.waarmerking', [
            'data' => $data,
            'start' => $this->startDate,
            'end' => $this->endDate
        ]);

        // Mengatur kertas ke A4 (Portrait atau Landscape sesuai kebutuhan)
        $pdf->setPaper('a4', 'portrait');

        // Download file
        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'laporan_waarmerking_' . now()->format('YmdHis') . '.pdf'
        );
    }

    // Fungsi Ekspor Excel
    public function exportExcel()
    {
        return Excel::download(
            new \App\Exports\Modules\WaarmerkingExport($this->startDate, $this->endDate, $this->search),
            'rekap_waarmerking_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function save()
    {
        // Proteksi Logic: Pastikan hanya Admin/Superadmin yang bisa mengeksekusi
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        $this->validate([
            'nomor_register' => 'required|unique:waarmerking,nomor_register,' . $this->selectedId,
            'nama_pemohon' => 'required',
            'tanggal_legalisasi' => 'required|date',
        ]);

        Waarmerking::updateOrCreate(
            ['id' => $this->selectedId],
            [
                'nomor_register' => $this->nomor_register,
                'nama_pemohon' => $this->nama_pemohon,
                'tanggal_legalisasi' => $this->tanggal_legalisasi,
                'catatan' => $this->catatan,
                'user_id' => Auth::id(),
            ]
        );

        session()->flash('success', $this->isEditing ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        // Proteksi Logic: Pastikan hanya Admin/Superadmin yang bisa mengeksekusi
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        $item = Waarmerking::findOrFail($id);
        $this->selectedId = $item->id;
        $this->nomor_register = $item->nomor_register;
        $this->nama_pemohon = $item->nama_pemohon;
        $this->tanggal_legalisasi = $item->tanggal_legalisasi->format('Y-m-d');
        $this->catatan = $item->catatan;
        $this->isEditing = true;
    }

    public function showDetail($id)
    {
        $this->selectedRegister = Waarmerking::find($id);

        // Memicu modal menggunakan nama modal yang didefinisikan di blade
        $this->dispatch('modal-show', name: 'detail-waarmerking');
    }

    public function delete($id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        Waarmerking::findOrFail($id)->delete();
        session()->flash('success', 'Data berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['nomor_register', 'nama_pemohon', 'tanggal_legalisasi', 'catatan', 'selectedId', 'isEditing']);
    }

    public function render()
    {
        return view('livewire.waarmerking-manager', [
            'registers' => $this->getQuery()->paginate(10)
        ]);
    }
}
