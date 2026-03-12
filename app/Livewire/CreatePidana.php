<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePidana extends Component
{
    use WithFileUploads;

    public $jenis, $no_perkara, $pihak, $pasal, $tgl_putus, $tgl_penyerahan;
    // Properti untuk menangani upload dinamis
    public $files = []; // Array untuk menyimpan file yang diupload
    public $fileInputs = [0]; // Array untuk melacak jumlah baris input yang muncul

    protected $messages = [
        'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
        'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
    ];

    // Fungsi untuk menambah baris input baru
    public function addFileInput()
    {
        $this->fileInputs[] = count($this->fileInputs);
    }

    public function removeFileInput($index)
    {
        // Pastikan minimal ada 1 kolom tersisa
        if (count($this->fileInputs) > 1) {
            unset($this->fileInputs[$index]);
            // Hapus juga file yang mungkin sudah terpilih di indeks tersebut
            unset($this->files[$index]);
            // Re-index array agar tidak error saat loop
            $this->fileInputs = array_values($this->fileInputs);
            $this->files = array_values($this->files);
        }
    }

    protected function rules()
    {
        return [
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10MB
        ];
    }
    public function store()
    {
        $this->validate([
            'jenis' => 'required|in:PID.B,PID.SUS,ANAK,PRAPERADILAN',
            'no_perkara' => 'required|unique:berkas,nomor_registrasi',
            'pihak' => 'required|string|max:255',
            'pasal' => 'required|string',
            'tgl_putus' => 'required|date',
            'tgl_penyerahan' => 'required|date',
            'files' => 'required|array|min:1',
            // Tambahkan 'nullable' agar jika tidak ada file, tidak error
            'files.*' => 'file|mimes:pdf,jpg,png|max:10240',
        ], [
            // Pesan error kustom
            'files.required' => 'Anda wajib mengunggah minimal satu dokumen perkara.',
            'files.*.mimes' => 'Format file harus berupa PDF, JPG, atau PNG.',
            'files.*.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
        ]);

        // Jika validasi lolos, ini akan dijalankan
        try {
            // Simpan data utama ke tabel 'berkas'
            $berkas = Berkas::create([
                'modul' => 'pidana',             // Penanda bahwa ini data modul pidana
                'nomor_registrasi' => $this->no_perkara,    // Mapping ke field nomor_registrasi
                'subjek' => $this->pihak,         // Mapping ke field subjek
                'tanggal_kejadian' => $this->tgl_putus,     // Mapping ke field tanggal_kejadian
                'user_id' => auth()->id(), // Simpan ID pengguna yang membuat data
                'metadata' => [                     // Data fleksibel dalam format JSON
                    'jenis_perkara' => $this->jenis,
                    'pasal' => $this->pasal,
                    'tgl_penyerahan' => $this->tgl_penyerahan,
                ],
            ]);


            // 3. Simpan File Dinamis
            if (!empty($this->files)) {
                foreach ($this->files as $file) {
                    if ($file) {
                        $path = $file->store('dokumen/pidana', 'public');
                        $berkas->files()->create([
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Tangani error jika terjadi
            dd($e->getMessage());
        }

        session()->flash('success', 'Data dan dokumen berhasil disimpan.');
        return redirect()->route('pidana.index'); // Kembali ke daftar pidana
    }

    public function render()
    {
        return view('livewire.pidana-create');
    }
}
