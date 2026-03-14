<?php

namespace App\Livewire;

use App\Models\SuratKuasa;
use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithFileUploads;

class SuratKuasaManager extends Component
{
    use WithFileUploads;

    public $berkas_id, $nomor_surat, $tanggal_surat, $penerima, $pemberi;
    public $file_kuasa;

    public $jenis = 'Khusus';

    public $isEditing = false;
    public $selectedId;

    public $search = '';

    public $detail = null;

    public function save()
    {
        $this->validate([
            'berkas_id' => 'required',
            'nomor_surat' => 'required|unique:surat_kuasa,nomor_surat_kuasa',
            'tanggal_surat' => 'required|date',
            'pemberi' => 'required',
            'penerima' => 'required',
            'jenis' => 'required|in:Khusus,Substitusi,Umum',
            'file_kuasa' => 'nullable|mimes:pdf|max:10240',
        ]);

        $path = $this->file_kuasa ? $this->file_kuasa->store('surat-kuasa', 'public') : null;

        SuratKuasa::create([
            'berkas_id' => $this->berkas_id,
            'nomor_surat_kuasa' => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat,
            'penerima_kuasa' => $this->penerima,
            'pemberi_kuasa' => $this->pemberi,
            'jenis_kuasa' => $this->jenis,
            'file_path' => $path,
        ]);

        session()->flash('success', 'Surat Kuasa berhasil didaftarkan.');
        $this->reset();
    }

    public function edit($id)
    {
        $kuasa = SuratKuasa::findOrFail($id);
        $this->selectedId = $id;
        $this->berkas_id = $kuasa->berkas_id;
        $this->nomor_surat = $kuasa->nomor_surat_kuasa;
        $this->tanggal_surat = $kuasa->tanggal_surat->format('Y-m-d');
        $this->penerima = $kuasa->penerima_kuasa;
        $this->pemberi = $kuasa->pemberi_kuasa;
        $this->jenis = $kuasa->jenis_kuasa;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        $kuasa = SuratKuasa::findOrFail($id);
        // Hapus file jika ada
        if ($kuasa->file_path) \Storage::disk('public')->delete($kuasa->file_path);
        $kuasa->delete();

        session()->flash('success', 'Surat Kuasa berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['berkas_id', 'nomor_surat', 'tanggal_surat', 'penerima', 'pemberi', 'jenis', 'selectedId', 'isEditing']);
    }

    public function render()
    {
        $daftarKuasa = SuratKuasa::with('berkas')
            ->where('nomor_surat_kuasa', 'like', '%' . $this->search . '%')
            ->orWhere('pemberi_kuasa', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.surat-kuasa-manager', [
            'daftarBerkas' => Berkas::all(),
            'daftarKuasa' => $daftarKuasa,
        ]);
        // return view('livewire.surat-kuasa-manager', [
        //     'daftarBerkas' => Berkas::all(),
        //     'daftarKuasa' => SuratKuasa::with('berkas')->latest()->get(),
        // ]);
    }
    public function viewDetail($id)
    {
        $this->detail = SuratKuasa::with('berkas')->findOrFail($id);
        //Livewire event untuk membuka modal
        $this->js('$flux.modal("modal-detail").show()');
    }
}
