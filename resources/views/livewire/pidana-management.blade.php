<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" level="1">Register Pidana</flux:heading>
            <flux:subheading>Manajemen berkas dan dokumen perkara pidana terpadu.</flux:subheading>
        </div>


        @if (auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin')
            <flux:button href="{{ route('pidana.create') }}" variant="primary" icon="plus" wire:navigate>
                Tambah Berkas
            </flux:button>
        @endif

    </div>

    <flux:separator variant="subtle" />

    {{-- Filter & Export Section (Sejajar dalam satu baris di Desktop) --}}
    {{-- Filter & Export Section --}}
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-6 shadow-sm">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-end">

            {{-- Search --}}
            <div class="xl:col-span-4">
                <flux:input wire:model.live.debounce.300ms="search" label="Pencarian" placeholder="Nomor/Terdakwa..."
                    icon="magnifying-glass" clearable />
            </div>

            {{-- Filter Jenis --}}
            <div class="xl:col-span-2">
                <flux:select wire:model.live="filterJenis" label="Jenis Perkara">
                    <flux:select.option value="">Semua Jenis</flux:select.option>
                    <flux:select.option value="PID.B">PID.B</flux:select.option>
                    <flux:select.option value="PID.SUS">PID.SUS</flux:select.option>
                    <flux:select.option value="ANAK">ANAK</flux:select.option>
                    <flux:select.option value="PRAPERADILAN">PRAPERADILAN</flux:select.option>
                </flux:select>
            </div>

            {{-- Rentang Tanggal --}}
            <div class="xl:col-span-2">
                <flux:input type="date" wire:model.live="tglMulai" label="Dari" />
            </div>

            <div class="xl:col-span-2">
                <flux:input type="date" wire:model.live="tglSelesai" label="Sampai" />
            </div>

            {{-- Tombol Ekspor (Hanya Icon) --}}
            <div class="xl:col-span-2 flex gap-2 justify-end">
                <flux:button wire:click="exportExcel" variant="subtle" icon="table-cells"
                    class="text-green-600 border-green-100 hover:bg-green-50" title="Ekspor Excel" />
                <flux:button wire:click="exportPdf" variant="subtle" icon="document-text"
                    class="text-red-600 border-red-100 hover:bg-red-50" title="Ekspor PDF" />
            </div>
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
                    <flux:table.column>Tanggal Penyerahan</flux:table.column>
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
                                @if (isset($item->metadata['tgl_penyerahan']) && $item->metadata['tgl_penyerahan'])
                                    <flux:badge color="indigo" variant="subtle" size="sm">
                                        {{ \Carbon\Carbon::parse($item->metadata['tgl_penyerahan'])->format('d/m/Y') }}
                                    </flux:badge>
                                @else
                                    <span class="text-zinc-400 italic text-xs">Belum diinput</span>
                                @endif
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
                                <flux:button wire:click="showDetail({{ $item->id }})" variant="ghost"
                                    icon="eye" />

                                {{-- Sembunyikan tombol Edit dan Delete dari User Biasa --}}
                                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                                    <flux:button href="{{ route('pidana.edit', $item->id) }}" variant="ghost"
                                        icon="pencil-square" wire:navigate />
                                    <flux:button wire:click="delete({{ $item->id }})" variant="ghost"
                                        icon="trash" class="text-red-600" wire:confirm="Hapus berkas ini?" />
                                @endif
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

    <flux:modal name="modal-detail-pidana" class="md:w-[600px]">
        @if ($selectedBerkas)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Perkara Pidana</flux:heading>
                    <flux:subheading>{{ $selectedBerkas->nomor_registrasi }}</flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Terdakwa / Pihak</flux:label>
                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $selectedBerkas->subjek }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>Tanggal Putusan</flux:label>
                        <flux:text>
                            {{ $selectedBerkas->tanggal_kejadian ? $selectedBerkas->tanggal_kejadian->format('d M Y') : '-' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>Tanggal Penyerahan</flux:label>
                        <flux:text>
                            {{ isset($selectedBerkas->metadata['tgl_penyerahan']) ? \Carbon\Carbon::parse($selectedBerkas->metadata['tgl_penyerahan'])->format('d M Y') : '-' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>Jenis Perkara</flux:label>
                        <flux:badge size="sm" inset="top bottom">
                            {{ $selectedBerkas->metadata['jenis_perkara'] ?? '-' }}</flux:badge>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Pasal yang Disangkakan</flux:label>
                    <flux:text>{{ $selectedBerkas->metadata['pasal'] ?? '-' }}</flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Isi Putusan / Keterangan</flux:label>
                    <div
                        class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 text-sm italic">
                        {{ $selectedBerkas->metadata['isi_putusan'] ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </flux:field>

                <div>
                    <flux:label class="mb-2 block">Dokumen Terlampir</flux:label>
                    <div class="space-y-2">
                        @forelse ($selectedBerkas->files as $file)
                            <div
                                class="flex items-center justify-between p-2 bg-zinc-100 dark:bg-zinc-900 rounded border border-dashed border-zinc-300 dark:border-zinc-700">
                                <span class="text-xs truncate">{{ $file->file_name }}</span>
                                <flux:button size="xs" variant="subtle" icon="arrow-down-tray"
                                    wire:click="downloadFile({{ $file->id }})">Unduh</flux:button>
                            </div>
                        @empty
                            <flux:text size="sm" class="text-zinc-400">Tidak ada file yang diunggah.</flux:text>
                        @endforelse
                    </div>
                </div>

                {{-- <div class="flex justify-end">
                    <flux:button wire:click="$flux.modal('modal-detail-pidana').close()" variant="primary">
                        Tutup
                    </flux:button>
                </div> --}}

                <div class="flex justify-end">
                    {{-- Atribut dismiss akan otomatis menutup modal yang sedang terbuka --}}
                    <flux:modal.close>
                        <flux:button variant="primary">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
