<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditPerdata extends Component
{
    use WithFileUploads;

    public $berkas, $no_perkara, $tgl_register, $penggugat, $tergugat, $jenis_perkara, $pasal, $isi_gugatan;
    public $oldFiles = []; // Menyimpan file yang sudah ada
    public $files = [];    // Menyimpan file baru
    public $fileInputs = [0];

    // Tambahkan protected messages untuk pesan error
    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];
    public function mount($id)
    {
        $this->berkas = Berkas::with('files')->findOrFail($id);
        $this->no_perkara = $this->berkas->nomor_registrasi;
        $this->tgl_register = $this->berkas->tanggal_kejadian->format('Y-m-d');

        $this->penggugat = $this->berkas->metadata['penggugat'] ?? '';
        $this->tergugat = $this->berkas->metadata['tergugat'] ?? '';
        $this->jenis_perkara = $this->berkas->metadata['jenis_perkara'] ?? '';
        $this->pasal = $this->berkas->metadata['pasal'] ?? '';
        $this->isi_gugatan = $this->berkas->metadata['isi_gugatan'] ?? '';

        $this->oldFiles = $this->berkas->files; // Ambil file relasi
    }

    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    public function removeFileInput($index)
    {
        unset($this->fileInputs[$index]);
        $this->fileInputs = array_values($this->fileInputs);
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function deleteOldFile($fileId)
    {
        $file = BerkasFile::findOrFail($fileId);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        $this->oldFiles = $this->berkas->fresh()->files; // Refresh daftar
    }

    public function update()
    {
        // 1. Validasi input dasar dan file baru
        $this->validate([
            'no_perkara' => 'required',
            'penggugat' => 'required',
            'tergugat' => 'required',
            // Validasi tipe file untuk setiap file baru yang diunggah
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // 2. Update data metadata
        $this->berkas->update([
            'nomor_registrasi' => $this->no_perkara,
            'tanggal_kejadian' => $this->tgl_register,
            'metadata' => [
                'penggugat' => $this->penggugat,
                'tergugat' => $this->tergugat,
                'jenis_perkara' => $this->jenis_perkara,
                'pasal' => $this->pasal,
                'isi_gugatan' => $this->isi_gugatan,
            ],
        ]);

        // 3. Simpan file baru dengan validasi
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                if ($file) {
                    $path = $file->store('dokumen/perdata', 'public');
                    $this->berkas->files()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path
                    ]);
                }
            }
        }

        session()->flash('success', 'Data berhasil diperbarui.');
        return redirect()->route('perdata.index');
    }

    public function render()
    {
        return view('livewire.perdata-edit');
    }
}
