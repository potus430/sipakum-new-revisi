<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserManagement extends Component
{
    public $userRoles = [];

    public function mount()
    {
        $this->userRoles = User::pluck('role', 'id')->toArray();
    }

    public function activate($userId)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        User::where('id', $userId)->update(['is_active' => true]);
        session()->flash('success', 'User telah diaktifkan.');
        $this->dispatch('notify', message: 'User berhasil diaktifkan!');
    }

    public function updateRole($userId)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($userId);
        $user->update(['role' => $this->userRoles[$userId]]);

        session()->flash('success', 'Role berhasil diperbarui ke '.$this->userRoles[$userId]);
        $this->dispatch('notify', message: 'Role berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::where('id', '!=', auth()->id())->get(),
        ]);
    }

    public function toggleStatus($userId)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user = User::findOrFail($userId);
        // Mengubah nilai boolean (true jadi false, false jadi true)
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', 'User berhasil '.$status);

        $this->dispatch('notify', message: 'Status user berhasil diubah!');
    }
}
