<?php

namespace App\Livewire;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithPagination;

class RegisterIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterModul = '';

    // Reset halaman pencarian saat filter diubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterModul() { $this->resetPage(); }

    public function render()
    {
        $query = Berkas::query()
            ->with('files') // Eager load file untuk efisiensi
            ->when($this->filterModul, function($q) {
                $q->where('modul', $this->filterModul);
            })
            ->when($this->search, function($q) {
                $q->where(function($sub) {
                    $sub->where('nomor_registrasi', 'like', '%' . $this->search . '%')
                        // Mencari di dalam kolom JSON metadata
                        ->orWhere('metadata->pihak', 'like', '%' . $this->search . '%')
                        ->orWhere('metadata->penggugat', 'like', '%' . $this->search . '%')
                        ->orWhere('metadata->tergugat', 'like', '%' . $this->search . '%');
                });
            });

        return view('livewire.register-index', [
            'registers' => $query->latest()->paginate(10),
        ]);
    }
}