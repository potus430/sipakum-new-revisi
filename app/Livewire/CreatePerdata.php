<?php

namespace App\Livewire;

use App\Models\Berkas;
use App\Models\BerkasFile;
use Livewire\Component;
use Livewire\WithFileUploads;
class CreatePerdata extends Component
{
    use WithFileUploads;

    // Tambahkan properti baru
    // Properti Form
    public $no_perkara, $tgl_register, $penggugat, $tergugat, $jenis_perkara, $pasal, $isi_gugatan;

    // Properti Upload Dinamis
    public $files = [];
    public $fileInputs = [0];

    // Fungsi untuk menambah baris input
    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    // Fungsi untuk menghapus baris input
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
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    public function mount()
    {
        $this->jenis_perkara = 'Gugatan'; // Default agar tidak NULL saat form dimuat
    }

    public function store()
    {
        //dd($this->jenis_perkara);
        $this->validate([
            'no_perkara' => 'required',
            'penggugat' => 'required',
            'tergugat' => 'required',
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $berkas = Berkas::create([
            'modul' => 'perdata',
            'nomor_registrasi' => $this->no_perkara,
            'subjek' => "{$this->penggugat} vs {$this->tergugat}",
            'tanggal_kejadian' => $this->tgl_register,
            'user_id' => auth()->id(),
            'metadata' => [
                'penggugat' => $this->penggugat,
                'tergugat' => $this->tergugat,
                'jenis_perkara' => $this->jenis_perkara,
                'pasal' => $this->pasal,
                'isi_gugatan' => $this->isi_gugatan,
            ],
        ]);

        // Simpan file dinamis
        foreach ($this->files as $file) {
            if ($file) {
                $path = $file->store('dokumen/perdata', 'public');
                $berkas->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path
                ]);
            }
        }

        session()->flash('success', 'Data perkara perdata berhasil disimpan.');
        return redirect()->route('perdata.index');
    }

    public function render()
    {
        return view('livewire.perdata-create');
    }
}
