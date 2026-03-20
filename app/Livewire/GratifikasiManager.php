<?php

namespace App\Livewire;

use App\Models\Gratifikasi;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GratifikasiManager extends Component
{
    use WithFileUploads, WithPagination;

    public $pelapor, $pemberi, $bentuk_gratifikasi, $estimasi_nilai, $tanggal_penerimaan, $kronologi, $status = 'Pending';
    public $file_bukti;
    public $search = '', $selectedId, $isEditing = false;
    public $selectedGratifikasi = null;

    protected $rules = [
        'pelapor' => 'required',
        'pemberi' => 'required',
        'bentuk_gratifikasi' => 'required',
        'tanggal_penerimaan' => 'required|date',
        'kronologi' => 'required',
    ];

    public function save()
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }

        $this->validate();

        $path = null;
        if ($this->file_bukti) {
            $path = $this->file_bukti->store('gratifikasi', 'public');
        }

        Gratifikasi::updateOrCreate(['id' => $this->selectedId], [
            'pelapor' => $this->pelapor,
            'pemberi' => $this->pemberi,
            'bentuk_gratifikasi' => $this->bentuk_gratifikasi,
            'estimasi_nilai' => $this->estimasi_nilai,
            'tanggal_penerimaan' => $this->tanggal_penerimaan,
            'kronologi' => $this->kronologi,
            'status' => $this->status,
            'file_bukti' => $path ?? Gratifikasi::find($this->selectedId)?->file_bukti,
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', 'Laporan gratifikasi berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        $g = Gratifikasi::findOrFail($id);
        $this->selectedId = $g->id;
        $this->pelapor = $g->pelapor;
        $this->pemberi = $g->pemberi;
        $this->bentuk_gratifikasi = $g->bentuk_gratifikasi;
        $this->estimasi_nilai = $g->estimasi_nilai;
        $this->tanggal_penerimaan = $g->tanggal_penerimaan->format('Y-m-d');
        $this->kronologi = $g->kronologi;
        $this->status = $g->status;
        $this->isEditing = true;
    }

    public function showDetail($id)
    {
        $this->selectedGratifikasi = Gratifikasi::find($id);

        // Memicu modal menggunakan nama modal yang didefinisikan di blade
        $this->dispatch('modal-show', name: 'detail-gratifikasi');
    }

    public function resetForm()
    {
        $this->reset(['pelapor', 'pemberi', 'bentuk_gratifikasi', 'estimasi_nilai', 'tanggal_penerimaan', 'kronologi', 'file_bukti', 'selectedId', 'isEditing']);
    }

    public function delete($id)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
        }
        $item = Gratifikasi::findOrFail($id);

        // Hapus file fisik jika ada
        if ($item->file_bukti) {
            Storage::disk('public')->delete($item->file_bukti);
        }

        $item->delete();

        session()->flash('success', 'Data laporan gratifikasi berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.gratifikasi-manager', [
            'gratifikasis' => Gratifikasi::where('pelapor', 'like', "%{$this->search}%")
                ->latest()->paginate(10)
        ]);
    }
}
