<div class="p-6" x-data @notify.window="flux.toast.success($event.detail.message)">
    <flux:heading size="xl">Manajemen User</flux:heading>
    
    {{-- @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
            {{ session('success') }}
        </div>
    @endif --}}

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>Username</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column align="end">Aksi</flux:table.column>
        </flux:table.columns>
        
        <flux:table.rows>
            @foreach($users as $user)
            <flux:table.row>
                <flux:table.cell>{{ $user->username }}</flux:table.cell>
                
                <flux:table.cell>
                    <div class="w-40">
                        <flux:select wire:model="userRoles.{{ $user->id }}" wire:change="updateRole({{ $user->id }})" size="sm">
                            <flux:select.option value="superadmin">Superadmin</flux:select.option>
                            <flux:select.option value="admin">Admin</flux:select.option>
                            <flux:select.option value="user">User Biasa</flux:select.option>
                        </flux:select>
                    </div>
                </flux:table.cell>
                
                <flux:table.cell>
                    <flux:badge color="{{ $user->is_active ? 'green' : 'red' }}">
                        {{ $user->is_active ? 'Aktif' : 'Menunggu' }}
                    </flux:badge>
                </flux:table.cell>
                
                <flux:table.cell align="end">
                    {{-- Tombol Toggle Aktif/Nonaktif --}}
                    <flux:button 
                        wire:click="toggleStatus({{ $user->id }})" 
                        variant="{{ $user->is_active ? 'danger' : 'primary' }}" 
                        size="sm"
                    >
                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </flux:button>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>