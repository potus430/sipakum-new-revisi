<div class="p-6 x-data @notify.window="flux.toast.success($event.detail.message)"">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl" level="1">Register Perdata</flux:heading>
            <flux:subheading>Manajemen berkas dan dokumen perkara perdata terpadu.</flux:subheading>
        </div>

        {{-- Tombol Tambah Perkara di Header --}}
        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
            <flux:button href="{{ route('perdata.create') }}" variant="primary" icon="plus" wire:navigate>
                Tambah Perkara
            </flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" />

    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 mb-6 space-y-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

            <div class="md:col-span-5">
                <flux:input wire:model.live.debounce.300ms="search" label="Pencarian"
                    placeholder="Cari nomor perkara atau nama pihak..." clearable />
            </div>

            <div class="md:col-span-2">
                <flux:select wire:model.live="filterJenis" label="Jenis Perkara">
                    <flux:select.option value="">Semua Jenis</flux:select.option>
                    <flux:select.option value="Gugatan">Gugatan</flux:select.option>
                    <flux:select.option value="Permohonan">Permohonan</flux:select.option>
                    <flux:select.option value="Gugatan Sederhana">Gugatan Sederhana</flux:select.option>
                </flux:select>
            </div>

            <div class="md:col-span-2">
                <flux:input wire:model.live="startDate" type="date" label="Dari" />
            </div>
            <div class="md:col-span-2">
                <flux:input wire:model.live="endDate" type="date" label="Sampai" />
            </div>

            <div class="md:col-span-1 flex gap-2">
                <flux:button wire:click="exportExcel" icon="table-cells" variant="ghost" title="Excel" />
                <flux:button wire:click="exportPDF" icon="document-text" variant="ghost" title="PDF" />
            </div>
        </div>
    </div>

    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto px-5">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="w-1/6">Nomor Perkara</flux:table.column>
                    <flux:table.column class="w-1/6">Tanggal Register</flux:table.column>
                    <flux:table.column class="w-1/6">Penggugat / Pemohon</flux:table.column>
                    <flux:table.column class="w-1/6">Tergugat</flux:table.column>
                    <flux:table.column class="w-1/4">Jenis</flux:table.column>
                    <flux:table.column class="w-1/4">Isi Putusan</flux:table.column>
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
                                {{ $item->tanggal_kejadian->format('d/m/Y') }}
                            </flux:table.cell>

                            <flux:table.cell class="font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $item->metadata['penggugat_pemohon'] ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-600">
                                {{ $item->metadata['tergugat'] ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-600">
                                <flux:badge size="sm" color="sky" inset="top bottom">
                                    {{ $item->metadata['jenis_perkara'] ?? '-' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-600 truncate max-w-50"
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
                                {{-- Tombol Detail tetap bisa diakses semua user --}}
                                <flux:button wire:click="showDetail({{ $item->id }})" variant="ghost"
                                    icon="eye" />

                                {{-- Sembunyikan Edit dan Delete dari User Biasa --}}
                                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                                    <flux:button href="{{ route('perdata.edit', $item->id) }}" variant="ghost"
                                        icon="pencil-square" wire:navigate />
                                    <flux:button wire:click="delete({{ $item->id }})" variant="ghost"
                                        icon="trash" class="text-red-600 hover:text-red-700"
                                        wire:confirm="Apakah Anda yakin ingin menghapus perkara ini?" />
                                @endif
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

    <flux:modal name="modal-detail-perdata" class="md:w-[600px]">
        @if ($selectedBerkas)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Perkara Perdata</flux:heading>
                    <flux:subheading>{{ $selectedBerkas->nomor_registrasi }}</flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Pihak (Subjek)</flux:label>
                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $selectedBerkas->subjek }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>Jenis Perkara</flux:label>
                        <flux:badge size="sm" color="zinc" inset="top bottom">
                            {{ $selectedBerkas->metadata['jenis_perkara'] ?? '-' }}
                        </flux:badge>
                    </flux:field>

                    <flux:field>
                        <flux:label>Tanggal Putusan</flux:label>
                        <flux:text>
                            {{ isset($selectedBerkas->metadata['tgl_putusan']) ? \Carbon\Carbon::parse($selectedBerkas->metadata['tgl_putusan'])->format('d M Y') : '-' }}
                        </flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>Tanggal Penyerahan</flux:label>
                        <flux:text>
                            {{ isset($selectedBerkas->metadata['tgl_penyerahan_berkas']) ? \Carbon\Carbon::parse($selectedBerkas->metadata['tgl_penyerahan_berkas'])->format('d M Y') : '-' }}
                        </flux:text>
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Isi Gugatan / Putusan</flux:label>
                    <div
                        class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 text-sm italic">
                        {{ $selectedBerkas->metadata['isi_gugatan'] ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </flux:field>

                <div>
                    <flux:label class="mb-2 block">Dokumen PDF Terlampir</flux:label>
                    <div class="space-y-2">
                        @forelse ($selectedBerkas->files as $file)
                            <div
                                class="flex items-center justify-between p-2 bg-zinc-100 dark:bg-zinc-900 rounded border border-dashed border-zinc-300 dark:border-zinc-700">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="document-text" size="sm" class="text-zinc-400" />
                                    <span class="text-xs truncate w-64">{{ $file->file_name }}</span>
                                </div>
                                <flux:button size="xs" variant="subtle" icon="arrow-down-tray"
                                    wire:click="downloadFile({{ $file->id }})">Unduh</flux:button>
                            </div>
                        @empty
                            <flux:text size="sm" class="text-zinc-400">Tidak ada file yang diunggah.</flux:text>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:modal.close>
                        <flux:button variant="primary">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
