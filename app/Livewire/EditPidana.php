<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BerkasFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditPidana extends Component
{
    use WithFileUploads;

    public $berkasId, $jenis, $no_perkara, $pihak, $pasal, $tgl_putus, $tgl_penyerahan, $isi_putusan;
    public $files = [], $fileInputs = [0];
    public $existingFiles = []; // Diseragamkan penamaannya agar sinkron dengan view

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    public function mount($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $berkas = Berkas::with('files')->findOrFail($id);
        $this->berkasId = $berkas->id;
        $this->no_perkara = $berkas->nomor_registrasi;
        $this->pihak = $berkas->subjek;

        // Memastikan format tanggal benar untuk input type="date"
        $this->tgl_putus = $berkas->tanggal_kejadian ? $berkas->tanggal_kejadian->format('Y-m-d') : null;

        // Ambil data dari JSON Metadata
        $this->jenis = $berkas->metadata['jenis_perkara'] ?? '';
        $this->pasal = $berkas->metadata['pasal'] ?? '';
        $this->tgl_penyerahan = $berkas->metadata['tgl_penyerahan'] ?? '';
        $this->isi_putusan = $berkas->metadata['isi_putusan'] ?? '';

        $this->existingFiles = $berkas->files;
    }

    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    public function removeFileInput($index)
    {
        if (count($this->fileInputs) > 1) {
            unset($this->fileInputs[$index]);
            unset($this->files[$index]);
            $this->fileInputs = array_values($this->fileInputs);
            $this->files = array_values($this->files);
        }
    }

    public function deleteFile($fileId)
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $file = BerkasFile::findOrFail($fileId);

        // Hapus file fisik dari storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        // Refresh data file lama
        $this->existingFiles = Berkas::find($this->berkasId)->files;

        // Kirim notifikasi merah untuk penghapusan
        $this->dispatch(
            'notify',
            variant: 'error',
            heading: 'File Dihapus',
            message: 'Dokumen berhasil dihapus dari server.'
        );
    }

    public function update()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $this->validate([
            'no_perkara' => 'required',
            'pihak' => 'required',
            'tgl_putus' => 'required|date',
            'isi_putusan' => 'required',
            'files.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);
        // Proteksi Logic: Pastikan hanya Admin/Superadmin yang bisa mengeksekusi
        if (!Auth::user() || !in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }


        $berkas = Berkas::findOrFail($this->berkasId);
        $berkas->update([
            'nomor_registrasi' => $this->no_perkara,
            'subjek' => $this->pihak,
            'tanggal_kejadian' => $this->tgl_putus,
            'metadata' => [
                'jenis_perkara' => $this->jenis,
                'pasal' => $this->pasal,
                'tgl_penyerahan' => $this->tgl_penyerahan,
                'isi_putusan' => $this->isi_putusan, // Update isi putusan ke JSON
            ],
        ]);

        // Upload file baru jika ada
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                if ($file) {
                    $path = $file->store('berkas/pidana', 'public');
                    $berkas->files()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path
                    ]);
                }
            }
        }

        $this->dispatch(
            'notify',
            variant: 'success',
            heading: 'Berhasil',
            message: 'Data pidana telah diperbarui.'
        );

        return redirect()->route('pidana.index');
    }

    public function render()
    {
        return view('livewire.edit-pidana');
    }
}
