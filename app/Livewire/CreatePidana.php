<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class CreatePidana extends Component
{
    use WithFileUploads;

    public $jenis, $no_perkara, $pihak, $pasal, $tgl_putus, $tgl_penyerahan, $isi_putusan;

    // Properti untuk menangani upload dinamis
    public $files = [];
    public $fileInputs = [0];

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

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

    public function store()
    {
        $this->validate([
            'no_perkara' => 'required|string',
            'pihak' => 'required|string',
            'jenis' => 'required',
            'tgl_putus' => 'required|date',
            'isi_putusan' => 'required|string', // Validasi baru
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $berkas = Berkas::create([
                'modul' => 'pidana',
                'nomor_registrasi' => $this->no_perkara,
                'subjek' => $this->pihak,
                'tanggal_kejadian' => $this->tgl_putus,
                'user_id' => Auth::id(),
                'created_by' => Auth::id(), // Sesuai migrasi Anda
                'metadata' => [
                    'jenis_perkara' => $this->jenis,
                    'pasal' => $this->pasal,
                    'tgl_penyerahan' => $this->tgl_penyerahan,
                    'isi_putusan' => $this->isi_putusan, // Masuk ke JSON
                ],
            ]);

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

            // Memicu notifikasi pojok kanan atas yang baru kita buat
            $this->dispatch(
                'notify',
                variant: 'success',
                heading: 'Berhasil',
                message: 'Register Pidana berhasil disimpan.'
            );

            return redirect()->route('pidana.index');

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                variant: 'error',
                heading: 'Gagal',
                message: 'Terjadi kesalahan saat menyimpan data.'
            );
        }
    }

    public function render()
    {
        return view('livewire.pidana-create');
    }
}
