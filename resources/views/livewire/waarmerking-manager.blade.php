<div class="p-6 space-y-8">
    <flux:heading size="xl">Manajemen Waarmerking</flux:heading>

    {{-- Section Filter & Ekspor (BARU) --}}
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            {{-- Pencarian --}}
            <div class="md:col-span-4">
                <flux:input wire:model.live.debounce.300ms="search" label="Pencarian"
                    placeholder="Nama pemohon atau nomor register..." icon="magnifying-glass" clearable />
            </div>

            {{-- Rentang Tanggal --}}
            <div class="md:col-span-2">
                <flux:input type="date" wire:model.live="startDate" label="Dari Tanggal" />
            </div>
            <div class="md:col-span-2">
                <flux:input type="date" wire:model.live="endDate" label="Sampai Tanggal" />
            </div>

            {{-- Tombol Ekspor --}}
            <div class="md:col-span-4 flex justify-end gap-2">
                <flux:button wire:click="exportExcel" variant="subtle" icon="table-cells" class="text-green-600"
                    title="Ekspor ke Excel" />
                <flux:button wire:click="exportPdf" variant="subtle" icon="document-text" class="text-red-600"
                    title="Ekspor ke PDF" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Card --}}
        <flux:card class="lg:col-span-1">
            <form wire:submit="save" class="space-y-4">
                <flux:input wire:model="nomor_register" label="Nomor Register" placeholder="WM/2026/..." />
                <flux:input wire:model="nama_pemohon" label="Nama Pemohon" />
                <flux:input wire:model="tanggal_legalisasi" type="date" label="Tanggal Legalisasi" />
                <flux:textarea wire:model="catatan" label="Catatan" />

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ $isEditing ? 'Update Register' : 'Simpan Register' }}
                </flux:button>
                @if ($isEditing)
                    <flux:button wire:click="resetForm" variant="ghost" class="w-full">Batal</flux:button>
                @endif
            </form>
        </flux:card>

        {{-- Table Card --}}
        <flux:card class="lg:col-span-2">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>No. Register</flux:table.column>
                    <flux:table.column>Pemohon</flux:table.column>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column align="end">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($registers as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell class="font-medium">{{ $item->nomor_register }}</flux:table.cell>
                            <flux:table.cell>{{ $item->nama_pemohon }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $item->tanggal_legalisasi ? $item->tanggal_legalisasi->format('d/m/Y') : '-' }}
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex justify-end gap-2">
                                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm"
                                        icon="pencil" />
                                    <flux:button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Yakin ingin menghapus data ini?" variant="ghost" size="sm"
                                        icon="trash" class="text-red-600" />
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
