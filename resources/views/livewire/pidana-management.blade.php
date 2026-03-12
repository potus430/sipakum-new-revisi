<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" level="1">Register Pidana</flux:heading>
            <flux:subheading>Manajemen berkas dan dokumen perkara pidana terpadu.</flux:subheading>
        </div>


        <flux:button href="{{ route('pidana.create') }}" variant="primary" icon="plus" wire:navigate>
            Tambah Berkas
        </flux:button>

    </div>

    <flux:separator variant="subtle" />

    <div class="flex flex-col md:flex-row gap-4 my-6">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" view="search"
                placeholder="Cari nomor perkara atau nama terdakwa..." clearable />
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="filterJenis" placeholder="Semua Jenis">
                <option value="">Semua Jenis</option>
                <option value="PID.B">PID.BIASA</option>
                <option value="PID.SUS">PID.KHUSUS</option>
                <option value="ANAK">ANAK</option>
                <option value="PRAPERADILAN">PRAPERADILAN</option>
            </flux:select>
        </div>
    </div>

    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto px-5">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-1/4">Nomor Perkara</flux:table.column>
                    <flux:table.column class="w-1/4">Pasal</flux:table.column>
                    <flux:table.column class="w-1/4">Terdakwa / Pihak</flux:table.column>
                    <flux:table.column>Tanggal Putus</flux:table.column>
                    <flux:table.column>Dokumen</flux:table.column>
                    <flux:table.column align="end">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($listPidana as $item)
                        <flux:table.row :key="$item->id"
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span
                                        class="font-bold text-zinc-900 dark:text-white">{{ $item->nomor_registrasi }}</span>
                                    <span
                                        class="text-xs text-zinc-500">{{ $item->metadata['jenis_perkara'] ?? '-' }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="text-xs text-zinc-500">{{ $item->metadata['pasal'] ?? '-' }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $item->subjek }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="text-sm italic text-zinc-500">
                                    {{ $item->tanggal_kejadian ? $item->tanggal_kejadian->format('d M Y') : '-' }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                @foreach ($item->files as $file)
                                    <div class="flex items-center gap-2 mb-1">
                                        {{-- Logika Penentuan Ikon --}}
                                        <flux:icon name="{{ $file->icon }}" />

                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                            class="text-sm hover:underline">
                                            {{ $file->file_name }}
                                        </a>
                                    </div>
                                @endforeach
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                <flux:button href="{{ route('pidana.edit', $item->id) }}" variant="ghost"
                                    icon="pencil-square" wire:navigate />

                                <flux:button wire:click="delete({{ $item->id }})" variant="ghost" icon="trash"
                                    wire:confirm="Apakah Anda yakin ingin menghapus perkara ini? Tindakan ini akan menghapus semua file terkait secara permanen." />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-20 text-zinc-400">
                                <flux:icon name="magnifying-glass" class="mx-auto mb-2 opacity-20" size="xl" />
                                <p>Tidak ada data perkara yang ditemukan.</p>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($listPidana->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/30">
                {{ $listPidana->links() }}
            </div>
        @endif
    </div>
</div>
