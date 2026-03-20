<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class CreatePerdata extends Component
{
    use WithFileUploads;

    // Properti Form sesuai Revisi SIPAKUM Part II
    public $no_perkara;
    public $tgl_register;
    public $penggugat_pemohon; // Perubahan label dari 'penggugat'
    public $tergugat;
    public $jenis_perkara;
    public $isi_gugatan; // Digunakan sebagai Isi Putusan
    public $tgl_putusan; // Kolom Baru
    public $tgl_penyerahan_berkas; // Kolom Baru

    // Properti Upload Dinamis
    public $files = [];
    public $fileInputs = [0];

    public function mount()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        $this->jenis_perkara = 'Gugatan';
        $this->tgl_register = now()->format('Y-m-d');
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

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    public function store()
    {
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $this->validate([
            'no_perkara' => 'required|string',
            'tgl_register' => 'required|date',
            'penggugat_pemohon' => 'required|string',
            'tergugat' => 'required|string',
            'jenis_perkara' => 'required',
            'tgl_putusan' => 'nullable|date',
            'tgl_penyerahan_berkas' => 'nullable|date',
            'isi_gugatan' => 'required|string',
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf|max:10240',
        ]);

        // Proteksi Logic: Pastikan hanya Admin/Superadmin yang bisa mengeksekusi
        if (!Auth::user() || !in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Simpan ke Tabel Berkas (Gunakan JSON untuk Metadata)
        $berkas = Berkas::create([
            'modul' => 'perdata',
            'nomor_registrasi' => $this->no_perkara,
            'subjek' => "{$this->penggugat_pemohon} vs {$this->tergugat}",
            'tanggal_kejadian' => $this->tgl_register,
            'user_id' => Auth::id(), // FK ke tabel users
            'created_by' => Auth::id(), // Sesuai file migrasi berkas_table
            'metadata' => [
                'penggugat_pemohon' => $this->penggugat_pemohon,
                'tergugat' => $this->tergugat,
                'jenis_perkara' => $this->jenis_perkara,
                'tgl_putusan' => $this->tgl_putusan,
                'tgl_penyerahan_berkas' => $this->tgl_penyerahan_berkas,
                'isi_gugatan' => $this->isi_gugatan,
                // 'pasal' dihapus sesuai instruksi revisi
            ],
        ]);

        // Simpan File ke berkas_files
        foreach ($this->files as $file) {
            if ($file) {
                $path = $file->store('berkas/perdata', 'public');
                $berkas->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
        }

        // Trigger Notifikasi Modern
        //$this->dispatch('notify', message: 'Register Perdata berhasil disimpan!');
        $this->dispatch(
            'notify',
            variant: 'success',
            heading: 'Berhasil',
            message: 'Data perdata telah diperbarui di sistem.'
        );
        return redirect()->route('perdata.index');
    }

    public function render()
    {

        return view('livewire.perdata-create');
    }
}
