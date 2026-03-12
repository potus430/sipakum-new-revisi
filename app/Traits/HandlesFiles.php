<?php

namespace App\Traits;

use App\Models\BerkasFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

trait HandlesFiles
{
    use WithFileUploads;

    public $files = []; // Digunakan di Form (Livewire)

    /**
     * Menyimpan file ke storage dan mencatat record ke berkas_files
     */
    public function uploadFiles($berkas)
    {
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                // Simpan file ke folder berdasarkan tipe modul (pidana/perdata)
                $path = $file->store('dokumen/' . $berkas->modul, 'public');

                // Simpan ke tabel berkas_files
                $berkas->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
            // Reset array setelah upload sukses
            $this->files = [];
        }
    }

    /**
     * Menghapus file fisik dan record di database
     */
    public function deleteFile($fileId)
    {
        $file = BerkasFile::findOrFail($fileId);

        // Hapus file fisik dari storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Hapus record dari database
        $file->delete();

        session()->flash('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Validasi standar untuk file
     */
    public function validateFiles()
    {
        return [
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10MB
        ];
    }
}
