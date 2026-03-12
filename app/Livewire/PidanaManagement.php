<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class PidanaManagement extends Component
{
    use WithFileUploads, WithPagination;

    public $jenis, $no_perkara, $pihak, $pasal, $tgl_putus, $tgl_penyerahan;
    public $editId, $isEditMode = false;
    public $oldFiles = [], $files = [];
    public $search = '', $filterJenis = '';

    // Menggunakan satu fungsi untuk simpan (Create atau Update)
    public function save()
    {
        $this->validate([
            'jenis' => 'required',
            'no_perkara' => 'required|unique:berkas,nomor_registrasi,' . ($this->editId ?? 'NULL'),
            'pihak' => 'required',
            'tgl_putus' => 'required|date',
            'files.*' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
        ]);

        if ($this->isEditMode) {
            $berkas = Berkas::findOrFail($this->editId);
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
            $message = 'Data berhasil diperbarui.';
        } else {
            $berkas = Berkas::create([
                'modul' => 'pidana',
                'nomor_registrasi' => $this->no_perkara,
                'subjek' => $this->pihak,
                'tanggal_kejadian' => $this->tgl_putus,
                'metadata' => [
                    'jenis_perkara' => $this->jenis,
                    'pasal' => $this->pasal,
                    'tgl_penyerahan' => $this->tgl_penyerahan,
                ],
            ]);
            $message = 'Data berhasil disimpan.';
        }

        // Simpan File
        if ($this->files) {
            foreach ($this->files as $file) {
                $path = $file->store('dokumen/pidana', 'public');
                $berkas->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);
            }
        }

        session()->flash('success', $message);
        $this->cancelEdit();
    }

    public function edit($id)
    {
        $this->isEditMode = true;
        $berkas = Berkas::with('files')->findOrFail($id);

        $this->editId = $berkas->id;
        $this->no_perkara = $berkas->nomor_registrasi;
        $this->pihak = $berkas->subjek;
        $this->tgl_putus = $berkas->tanggal_kejadian->format('Y-m-d');
        $this->jenis = $berkas->metadata['jenis_perkara'] ?? '';
        $this->pasal = $berkas->metadata['pasal'] ?? '';
        $this->tgl_penyerahan = $berkas->metadata['tgl_penyerahan'] ?? '';
        $this->oldFiles = $berkas->files;
    }

    public function cancelEdit()
    {
        $this->reset();
        $this->isEditMode = false;
    }

    public function render()
    {
        return view('livewire.pidana-management', [
            'listPidana' => Berkas::where('modul', 'pidana')
                ->with(['files', 'creator'])
                ->when($this->search, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                            ->orWhere('subjek', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filterJenis, function ($q) {
                    $q->where('metadata->jenis_perkara', $this->filterJenis);
                })
                ->latest()
                ->paginate(10)
        ]);
    }

    public function delete($id)
    {
        $berkas = Berkas::with('files')->findOrFail($id);

        // 1. Hapus file fisik dari storage
        foreach ($berkas->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        // 2. Hapus data di database
        // (Relasi 'files' akan ikut terhapus jika Anda menggunakan 'onDelete Cascade' di migrasi)
        $berkas->files()->delete();
        $berkas->delete();

        // 3. Beri notifikasi sukses
        session()->flash('success', 'Data perkara berhasil dihapus.');
    }
}
