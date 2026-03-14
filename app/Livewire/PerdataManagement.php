<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use App\Traits\HandlesFiles;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PerdataManagement extends Component
{
    use WithPagination, HandlesFiles;

    public $search = '';
    public $filterJenis = '';

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

    public function render()
    {
        $query = Berkas::modul('perdata')
            ->where(function ($q) {
                $q->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                    ->orWhere('subjek', 'like', '%' . $this->search . '%');
            });

        // Menambahkan filter berdasarkan metadata JSON
        if (!empty($this->filterJenis)) {
            $query->where('metadata->jenis_perkara', $this->filterJenis);
        }

        $perdata = $query->latest()->paginate(10);

        return view('livewire.perdata-management', compact('perdata'));
    }
}
