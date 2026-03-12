<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BerkasFile;
use Illuminate\Support\Facades\Storage;

class EditPidana extends Component
{
    use WithFileUploads;

    public $berkasId, $jenis, $no_perkara, $pihak, $pasal, $tgl_putus, $tgl_penyerahan;
    public $files = [], $fileInputs = [0]; // Input dinamis untuk file baru
    public $oldFiles = []; // Menyimpan daftar file lama

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    protected function rules()
    {
        return [
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10MB
        ];
    }
    public function mount($id)
    {
        $berkas = Berkas::with('files')->findOrFail($id);
        $this->berkasId = $berkas->id;
        $this->no_perkara = $berkas->nomor_registrasi;
        $this->pihak = $berkas->subjek;

        // Pastikan field di database Anda adalah 'tanggal_kejadian'
        $this->tgl_putus = \Carbon\Carbon::parse($berkas->tanggal_kejadian)->format('Y-m-d');

        // Jika data disimpan di metadata JSON
        $this->jenis = $berkas->metadata['jenis_perkara'] ?? '';
        $this->pasal = $berkas->metadata['pasal'] ?? '';
        $this->tgl_penyerahan = $berkas->metadata['tgl_penyerahan'] ?? '';

        $this->oldFiles = $berkas->files;
    }

    // Menambah input file baru
    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    // Menghapus input file baru
    public function removeFileInput($index)
    {
        unset($this->fileInputs[$index]);
        $this->fileInputs = array_values($this->fileInputs);
    }

    // Menghapus file lama (dari database & disk)
    public function deleteOldFile($fileId)
    {
        $berkas = Berkas::findOrFail($this->berkasId);

        // Cek apakah sisa file (lama + baru) > 1
        if (($berkas->files()->count() + count(array_filter($this->files))) > 1) {
            $file = BerkasFile::findOrFail($fileId);
            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            // Refresh daftar file
            $this->oldFiles = $berkas->fresh()->files;
        } else {
            session()->flash('error', 'Minimal harus ada 1 dokumen tersisa.');
        }
    }

    public function update()
    {
        $this->validate([
            'jenis' => 'required',
            'no_perkara' => 'required|unique:berkas,nomor_registrasi,' . $this->berkasId,
            'pihak' => 'required',
            'tgl_putus' => 'required|date',
            'files' => count($this->oldFiles) > 0 ? 'nullable' : 'required',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            // Pesan error kustom
            'files.required' => 'Karena belum ada dokumen, Anda wajib mengunggah minimal satu dokumen.',
            'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
            'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
        ]);

        $berkas = Berkas::findOrFail($this->berkasId);
        $berkas->update([
            'nomor_registrasi' => $this->no_perkara,
            'subjek' => $this->pihak,
            'tanggal_kejadian' => $this->tgl_putus,
            'metadata' => [
                'jenis_perkara' => $this->jenis,
                'pasal' => $this->pasal,
                'tgl_penyerahan' => $this->tgl_penyerahan,
            ],
        ]);

        // Upload file baru jika ada
        if ($this->files) {
            foreach ($this->files as $file) {
                $path = $file->store('dokumen/pidana', 'public');
                $berkas->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
        }

        session()->flash('success', 'Data berhasil diperbarui.');
        return redirect()->route('pidana.index');
    }

    public function render()
    {
        return view('livewire.edit-pidana');
    }
}
