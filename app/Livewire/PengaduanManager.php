<?php

namespace App\Livewire;

use App\Models\Pengaduan;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaduanManager extends Component
{
    use WithFileUploads, WithPagination;

    public $judul, $isi_pengaduan, $is_anonim = false, $status = 'Terima';
    public $file_pendukung, $selectedId, $search = '';



    public $selectedPengaduan;


    public function save()
    {
        $pengaduan = Pengaduan::find($this->selectedId);


        $this->validate([
            'judul' => 'required|min:5',
            'isi_pengaduan' => 'required',
        ]);

        // 1. Ambil data lama untuk pengecekan status
        $pengaduanLama = Pengaduan::find($this->selectedId);
        $statusLama = $pengaduanLama ? $pengaduanLama->status : 'Terima';

        // 2. Lakukan penyimpanan data (Update atau Create)
        $path = $this->file_pendukung ? $this->file_pendukung->store('pengaduan', 'public') : ($pengaduanLama?->file_pendukung);

        $pengaduan = Pengaduan::updateOrCreate(['id' => $this->selectedId], [
        'judul' => $this->judul,
        'isi_pengaduan' => $this->isi_pengaduan,
        'anonim' => $this->is_anonim ? 'Ya' : 'Tidak',
        'status' => $this->status,
        'file_pendukung' => $path,
        'user_id' => $this->is_anonim ? null : Auth::id(),
    ]);

    // 3. Simpan log hanya jika ada perubahan status pada data yang sudah ada
    if ($pengaduanLama && $statusLama !== $this->status) {
        \App\Models\PengaduanLog::create([
            'pengaduan_id' => $pengaduan->id, // Menggunakan variabel $pengaduan yang sudah valid
            'user_id' => Auth::id(),
            'status_lama' => $statusLama,
            'status_baru' => $this->status,
            'created_at' => now(),
        ]);
    }


        session()->flash('success', 'Pengaduan berhasil diproses.');
       $this->reset(['judul', 'isi_pengaduan', 'status', 'selectedId', 'is_anonim', 'file_pendukung']);
    }

    public function delete($id)
    {
        $item = Pengaduan::findOrFail($id);
        if ($item->file_pendukung) Storage::disk('public')->delete($item->file_pendukung);
        $item->delete();
    }

    public function edit($id)
{
    $pengaduan = Pengaduan::findOrFail($id);
    $this->selectedId = $pengaduan->id;
    $this->judul = $pengaduan->judul;
    $this->isi_pengaduan = $pengaduan->isi_pengaduan;
    $this->status = $pengaduan->status; // Memuat status saat ini
    $this->js('$flux.modal("modal-update-status").show()');
}

    public function viewDetail($id)
{
    $this->selectedPengaduan = Pengaduan::find($id);
    $this->js('$flux.modal("modal-detail-pengaduan").show()');
}

public function downloadFile($id)
{
    $item = Pengaduan::findOrFail($id);

    if ($item->file_pendukung && Storage::disk('public')->exists($item->file_pendukung)) {
        return Storage::disk('public')->download($item->file_pendukung);
    }

    session()->flash('error', 'File tidak ditemukan.');
}
    public function render()
    {
       $pengaduans = Pengaduan::where('judul', 'like', "%{$this->search}%")
        ->orWhere('isi_pengaduan', 'like', "%{$this->search}%") // Opsional: mencari juga di isi
        ->latest()
        ->paginate(10);

    return view('livewire.pengaduan-manager', [
        'pengaduans' => $pengaduans
    ]);
    }
}