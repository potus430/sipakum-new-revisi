<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" level="1">Register Perdata</flux:heading>
            <flux:subheading>Manajemen berkas dan dokumen perkara perdata terpadu.</flux:subheading>
        </div>

        <flux:button href="{{ route('perdata.create') }}" variant="primary" icon="plus" wire:navigate>
            Tambah Perkara
        </flux:button>
    </div>

    <flux:separator variant="subtle" />

    <div class="flex flex-col md:flex-row gap-4 my-6">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" view="search"
                placeholder="Cari nomor perkara atau nama pihak..." clearable />
        </div>

        <div class="flex gap-2">
            <flux:select wire:model.live="filterJenis" placeholder="Semua Jenis">
                <flux:select.option value="">Semua Jenis</flux:select.option>
                <flux:select.option value="Gugatan">Gugatan</flux:select.option>
                <flux:select.option value="Permohonan">Permohonan</flux:select.option>
                <flux:select.option value="Gugatan Sederhana">Gugatan Sederhana</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto px-5">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-1/6">Nomor Perkara</flux:table.column>
                    <flux:table.column class="w-1/6">Pihak</flux:table.column>
                    <flux:table.column class="w-1/4">Pasal / Dasar Hukum</flux:table.column>
                    <flux:table.column class="w-1/4">Isi Gugatan / Permohonan</flux:table.column>
                    <flux:table.column>Dokumen</flux:table.column>
                    <flux:table.column align="end">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($perdata as $item)
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

                            <flux:table.cell class="font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $item->metadata['penggugat'] ?? '-' }} <span class="text-zinc-400">vs</span>
                                {{ $item->metadata['tergugat'] ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-600">
                                {{ $item->metadata['pasal'] ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-600 truncate max-w-[200px]"
                                title="{{ $item->metadata['isi_gugatan'] ?? '-' }}">
                                {{ Str::limit($item->metadata['isi_gugatan'] ?? '-', 50) }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @foreach ($item->files as $file)
                                    <div class="flex items-center gap-2 mb-1">
                                        <flux:icon name="{{ $file->icon }}" />

                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                            download="{{ $file->file_name }}"
                                            class="text-sm hover:underline text-blue-600 dark:text-blue-400">
                                            {{ $file->file_name }}
                                        </a>
                                    </div>
                                @endforeach
                            </flux:table.cell>

                            <flux:table.cell align="end">
                                <flux:button href="{{ route('perdata.edit', $item->id) }}" variant="ghost"
                                    icon="pencil-square" wire:navigate />
                                <flux:button wire:click="delete({{ $item->id }})" variant="ghost" icon="trash"
                                    class="text-red-600 hover:text-red-700"
                                    wire:confirm="Apakah Anda yakin ingin menghapus perkara ini? Tindakan ini akan menghapus semua file terkait secara permanen." />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-20 text-zinc-400">
                                <flux:icon name="magnifying-glass" class="mx-auto mb-2 opacity-20" size="xl" />
                                <p>Tidak ada data perkara perdata ditemukan.</p>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($perdata->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/30">
                {{ $perdata->links() }}
            </div>
        @endif
    </div>
</div>
