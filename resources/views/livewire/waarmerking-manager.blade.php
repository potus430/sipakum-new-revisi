<div class="p-6 space-y-8">
    <flux:heading size="xl">Manajemen Waarmerking</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <flux:card class="lg:col-span-1">
            <form wire:submit="save" class="space-y-4">
                <flux:input wire:model="nomor_register" label="Nomor Register" placeholder="WM/2026/..." />
                <flux:input wire:model="nama_pemohon" label="Nama Pemohon" />
                <flux:select wire:model="jenis_dokumen" label="Jenis Dokumen">
    <option value="Perjanjian">Perjanjian</option>
    <option value="Pernyataan">Pernyataan</option>
    <option value="Kuasa">Kuasa</option>
    <option value="Lainnya">Lainnya</option>
</flux:select>
                <flux:input wire:model="tanggal_legalisasi" type="date" label="Tanggal Legalisasi" />
                <flux:textarea wire:model="catatan" label="Catatan" />
                <flux:input wire:model="file_dokumen" type="file" label="Upload Dokumen (PDF)" />

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ $isEditing ? 'Update Register' : 'Simpan Register' }}
                </flux:button>
                @if($isEditing)
                    <flux:button wire:click="resetForm" variant="ghost" class="w-full">Batal</flux:button>
                @endif
            </form>
        </flux:card>

        <flux:card class="lg:col-span-2">
            <div class="mb-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pemohon atau nomor..." icon="magnifying-glass" />
            </div>

            <flux:table>
        <flux:table.columns>
            <flux:table.column>No. Register</flux:table.column>
            <flux:table.column>Pemohon</flux:table.column>
            <flux:table.column>Jenis Dokumen</flux:table.column> <flux:table.column>Tanggal</flux:table.column>        <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>
        
        <flux:table.rows>
            @foreach($registers as $item)
                <flux:table.row>
                    <flux:table.cell>{{ $item->nomor_register }}</flux:table.cell>
                    <flux:table.cell>{{ $item->nama_pemohon }}</flux:table.cell>
                    <flux:table.cell>
    @php
        $warna = match ($item->jenis_dokumen) {
            'Perjanjian' => 'green',
            'Pernyataan' => 'blue',
            'Kuasa' => 'orange',
            'Lainnya' => 'gray',
            default => 'zinc',
        };
    @endphp
    
    <flux:badge color="{{ $warna }}" size="sm">
        {{ $item->jenis_dokumen }}
    </flux:badge>
</flux:table.cell>
                    <flux:table.cell>{{ $item->tanggal_legalisasi ? $item->tanggal_legalisasi->format('d/m/Y') : '-' }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil" />
                            <flux:button href="{{ asset('storage/'.$item->file_path) }}" target="_blank" variant="ghost" size="sm" icon="arrow-down-tray" />
                            <flux:button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus data ini?" variant="ghost" size="sm" icon="trash" class="text-red-600" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
            <div class="mt-4">{{ $registers->links() }}</div>
        </flux:card>
    </div>
</div>