<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditPerdata extends Component
{
    use WithFileUploads;

    public $berkas;
    public $no_perkara, $tgl_register, $penggugat_pemohon, $tergugat, $jenis_perkara, $isi_gugatan;
    public $tgl_putusan, $tgl_penyerahan_berkas; // Properti baru sesuai revisi

    public $existingFiles = []; // Diseragamkan dengan view
    public $files = [];
    public $fileInputs = [0];

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    public function mount($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $this->berkas = Berkas::with('files')->findOrFail($id);

        $this->no_perkara = $this->berkas->nomor_registrasi;
        $this->tgl_register = $this->berkas->tanggal_kejadian->format('Y-m-d');

        // Ambil data dari metadata JSON
        $this->penggugat_pemohon = $this->berkas->metadata['penggugat_pemohon'] ?? ($this->berkas->metadata['penggugat'] ?? '');
        $this->tergugat = $this->berkas->metadata['tergugat'] ?? '';
        $this->jenis_perkara = $this->berkas->metadata['jenis_perkara'] ?? '';
        $this->isi_gugatan = $this->berkas->metadata['isi_gugatan'] ?? '';
        $this->tgl_putusan = $this->berkas->metadata['tgl_putusan'] ?? '';
        $this->tgl_penyerahan_berkas = $this->berkas->metadata['tgl_penyerahan_berkas'] ?? '';

        $this->existingFiles = $this->berkas->files;
    }

    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    public function removeFileInput($index)
    {
        unset($this->fileInputs[$index]);
        unset($this->files[$index]);
    }

    public function deleteFile($fileId)
    {
        $file = BerkasFile::findOrFail($fileId);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        $this->existingFiles = $this->berkas->fresh()->files;
        $this->dispatch('notify', message: 'File berhasil dihapus.');
    }

    public function update()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }
        $this->validate([
            'no_perkara' => 'required',
            'penggugat_pemohon' => 'required',
            'tergugat' => 'required',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);


        $this->berkas->update([
            'nomor_registrasi' => $this->no_perkara,
            'tanggal_kejadian' => $this->tgl_register,
            'subjek' => "{$this->penggugat_pemohon} vs {$this->tergugat}",
            'metadata' => [
                'penggugat_pemohon' => $this->penggugat_pemohon,
                'tergugat' => $this->tergugat,
                'jenis_perkara' => $this->jenis_perkara,
                'tgl_putusan' => $this->tgl_putusan,
                'tgl_penyerahan_berkas' => $this->tgl_penyerahan_berkas,
                'isi_gugatan' => $this->isi_gugatan,
                // 'pasal' dihapus
            ],
        ]);

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                if ($file) {
                    $path = $file->store('berkas/perdata', 'public');
                    $this->berkas->files()->create([
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
            message: 'Data perdata telah diperbarui di sistem.'
        );
        //$this->dispatch('notify', message: 'Data Perdata berhasil diperbarui!');
        return redirect()->route('perdata.index');
    }

    public function render()
    {
        return view('livewire.perdata-edit');
    }
}
