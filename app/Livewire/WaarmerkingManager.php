<?php

namespace App\Livewire;

use App\Models\Waarmerking;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WaarmerkingManager extends Component
{
    use WithFileUploads, WithPagination;

    // Properti Form
    public $nomor_register, $nama_pemohon, $jenis_dokumen, $tanggal_legalisasi, $catatan;
    public $file_dokumen;
    public $search = '';
    public $selectedId;
    public $isEditing = false;

    // Reset halaman saat mencari
    public function updatingSearch() { $this->resetPage(); }

    public function save()
    {
        $this->validate([
            'nomor_register' => 'required|unique:waarmerking,nomor_register,' . $this->selectedId,
            'nama_pemohon' => 'required',
            'jenis_dokumen' => 'required',
            'tanggal_legalisasi' => 'required|date',
            'file_dokumen' => $this->isEditing ? 'nullable|mimes:pdf|max:10240' : 'required|mimes:pdf|max:10240',
        ]);

        $path = null;
        if ($this->file_dokumen) {
            $path = $this->file_dokumen->store('waarmerking', 'public');
        }

        Waarmerking::updateOrCreate(
            ['id' => $this->selectedId],
            [
                'nomor_register' => $this->nomor_register,
                'nama_pemohon' => $this->nama_pemohon,
                'jenis_dokumen' => $this->jenis_dokumen,
                'tanggal_legalisasi' => $this->tanggal_legalisasi,
                'catatan' => $this->catatan,
                'file_path' => $path ?? Waarmerking::find($this->selectedId)?->file_path,
                'user_id' => Auth::id(), // Melacak siapa yang menginput/mengupdate
            ]
        );

        session()->flash('success', $this->isEditing ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.');
        $this->resetForm();
        $this->js('$flux.modal("modal-tambah").close()');
    }

    public function edit($id)
    {
        $item = Waarmerking::findOrFail($id);
        $this->selectedId = $item->id;
        $this->nomor_register = $item->nomor_register;
        $this->nama_pemohon = $item->nama_pemohon;
        $this->jenis_dokumen = $item->jenis_dokumen;
        $this->tanggal_legalisasi = $item->tanggal_legalisasi->format('Y-m-d');
        $this->catatan = $item->catatan;
        $this->isEditing = true;
        $this->js('$flux.modal("modal-tambah").show()');
    }

    public function delete($id)
    {
        $item = Waarmerking::findOrFail($id);
        if ($item->file_path) Storage::disk('public')->delete($item->file_path);
        $item->delete();
        session()->flash('success', 'Data berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['nomor_register', 'nama_pemohon', 'jenis_dokumen', 'tanggal_legalisasi', 'catatan', 'file_dokumen', 'selectedId', 'isEditing']);
    }

    public function render()
    {
        $registers = Waarmerking::with('user')
            ->where('nomor_register', 'like', '%' . $this->search . '%')
            ->orWhere('nama_pemohon', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.waarmerking-manager', [
            'registers' => $registers,
        ]);
    }
}