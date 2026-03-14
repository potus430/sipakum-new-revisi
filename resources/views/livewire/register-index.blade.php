<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <flux:heading size="xl">Buku Register Digital</flux:heading>
            <flux:subheading>Rekapitulasi data perkara terpadu SIPAKUM</flux:subheading>
        </div>
        
        <div class="flex gap-2 w-full md:w-auto">
            <flux:select wire:model.live="filterModul" placeholder="Semua Modul" class="w-full md:w-40">
                <option value="">Semua</option>
                <option value="pidana">Pidana</option>
                <option value="perdata">Perdata</option>
            </flux:select>
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nomor/nama..." class="flex-1" />
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto px-5">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Nomor Registrasi</flux:table.column>
                <flux:table.column>Modul</flux:table.column>
                <flux:table.column>Pihak Terkait</flux:table.column>
                <flux:table.column>Keterangan/Pasal</flux:table.column>
                <flux:table.column>Berkas</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($registers as $item)
                    <flux:table.row>
                        <flux:table.cell>{{ $item->tanggal_kejadian ? $item->tanggal_kejadian->format('d/m/Y') : '-' }}</flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-800 dark:text-white">
                            {{ $item->nomor_registrasi }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="{{ $item->modul == 'pidana' ? 'danger' : 'primary' }}" size="sm">
        {{ strtoupper($item->modul) }}
    </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            @if($item->modul == 'pidana')
                                <span class="font-semibold">Terdakwa:</span> {{ $item->metadata['pihak'] ?? '-' }}
                            @else
                                <span class="font-semibold">P:</span> {{ $item->metadata['penggugat'] ?? '-' }} <br>
                                <span class="font-semibold">T:</span> {{ $item->metadata['tergugat'] ?? '-' }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs italic text-zinc-500">
                            {{ \Illuminate\Support\Str::limit($item->metadata['pasal'] ?? '-', 30) }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex -space-x-2">
                                @foreach($item->files->take(3) as $file)
                                    <div title="{{ $file->file_name }}" class="w-7 h-7 rounded-full bg-zinc-100 border border-white flex items-center justify-center">
                                        <flux:icon name="document-text" size="xs" class="text-zinc-400" />
                                    </div>
                                @endforeach
                                @if($item->files->count() > 3)
                                    <div class="w-7 h-7 rounded-full bg-zinc-200 border border-white flex items-center justify-center text-[10px] font-bold">
                                        +{{ $item->files->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-10 text-zinc-400 italic">
                            Data tidak ditemukan
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </div>

    <div class="mt-4">
        {{ $registers->links() }}
    </div>
</div>