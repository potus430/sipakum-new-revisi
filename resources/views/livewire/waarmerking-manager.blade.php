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
        {{-- Form Card: Hanya muncul untuk Admin/Superadmin --}}
        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
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
        @endif

        {{-- Table Card: Lebar otomatis penuh jika form tidak ada --}}
        <div class="{{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 'lg:col-span-2' : 'lg:col-span-3' }}">
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
                                <flux:table.cell>
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                        {{ $item->nama_pemohon }}</div>
                                    <div class="text-[11px] text-zinc-500 max-w-xs truncate italic">
                                        {{ $item->catatan ?? 'Tidak ada catatan.' }}
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $item->tanggal_legalisasi ? $item->tanggal_legalisasi->format('d/m/Y') : '-' }}
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        {{-- Tombol View Detail --}}
                                        <flux:button wire:click="showDetail({{ $item->id }})" variant="ghost"
                                            size="sm" icon="eye" tooltip="Lihat Detail" />

                                        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <flux:button wire:click="edit({{ $item->id }})" variant="ghost"
                                                size="sm" icon="pencil" />
                                            <flux:button wire:click="delete({{ $item->id }})"
                                                wire:confirm="Yakin ingin menghapus?" variant="ghost" size="sm"
                                                icon="trash" class="text-red-600" />
                                        @endif
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

    {{-- Modal Detail Waarmerking --}}
    <flux:modal name="detail-waarmerking"
        x-on:open-modal.window="$event.detail.name === 'detail-waarmerking' && $reveal()" class="md:w-[500px]">
        @if ($selectedRegister)
            <div class="space-y-6">
                <div>
                    <flux:heading size="xl" class="text-emerald-600">Detail Waarmerking</flux:heading>
                    <flux:subheading>Informasi lengkap pendaftaran dokumen</flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-1 gap-5">
                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 uppercase font-semibold">Nomor Register</span>
                        <span
                            class="text-lg font-bold text-zinc-900 dark:text-white">{{ $selectedRegister->nomor_register }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 uppercase font-semibold">Nama Pemohon</span>
                        <span
                            class="text-base font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedRegister->nama_pemohon }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Tanggal Legalisasi</span>
                            <span
                                class="text-sm">{{ $selectedRegister->tanggal_legalisasi ? $selectedRegister->tanggal_legalisasi->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Petugas Input</span>
                            <span class="text-sm">{{ $selectedRegister->user->name ?? 'Sistem' }}</span>
                        </div>
                    </div>

                    <div
                        class="flex flex-col p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-100 dark:border-zinc-700">
                        <span class="text-xs text-zinc-500 uppercase font-semibold mb-1">Catatan / Keperluan</span>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            {{ $selectedRegister->catatan ?? 'Tidak ada catatan tambahan.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
