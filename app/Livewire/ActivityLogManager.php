<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedLog = null; // Menampung data log yang akan ditampilkan di modal

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->where('module', 'like', '%' . $this->search . '%')
            ->orWhere('action', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(15);

        return view('livewire.activity-log-manager', [
            'logs' => $logs
        ]);
    }

    public function showDiff($id)
    {
        // Ambil data log beserta user-nya
        $this->selectedLog = ActivityLog::find($id);

        if ($this->selectedLog) {
            // Membuka modal menggunakan Flux JS API
            $this->js('$flux.modal("modal-detail-log").show()');
        }
    }

    public function restore($id)
    {
        $log = \App\Models\ActivityLog::findOrFail($id);

        // Cek apakah ini log DELETE dan memiliki data lama
        if ($log->action !== 'DELETE' || !$log->old_data) {
            $this->dispatch('notify', variant: 'error', message: 'Hanya data yang dihapus yang dapat dipulihkan.');
            return;
        }

        try {
            // Rekonstruksi Nama Class Model (contoh: App\Models\Waarmerking)
            $modelClass = "App\\Models\\" . $log->module;

            if (class_exists($modelClass)) {
                // Masukkan kembali data dari old_data ke database
                $modelClass::create($log->old_data);

                $this->dispatch('notify', variant: 'success', message: "Data {$log->module} berhasil dipulihkan!");
                $this->js('$flux.modal("modal-detail-log").close()');
            } else {
                throw new \Exception("Model {$log->module} tidak ditemukan.");
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', variant: 'error', message: 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        // Cek apakah user yang login adalah superadmin
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Anda tidak memiliki akses ke halaman audit log.');
        }
    }
}
