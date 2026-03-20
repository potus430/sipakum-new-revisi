<div class="p-6 space-y-8">
    <flux:heading size="xl">Manajemen Surat Kuasa</flux:heading>

    {{-- Section Filter & Ekspor (BARU) --}}
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            {{-- Pencarian Nomor/Nama --}}
            <div class="md:col-span-3">
                <flux:input wire:model.live.debounce.300ms="search" label="Pencarian" placeholder="Nomor atau Nama..."
                    icon="magnifying-glass" clearable />
            </div>

            {{-- Filter Kategori --}}
            <div class="md:col-span-2">
                <flux:select wire:model.live="filterKategori" label="Kategori">
                    <option value="">Semua</option>
                    <option value="Pidana">Pidana</option>
                    <option value="Perdata">Perdata</option>
                </flux:select>
            </div>

            {{-- Rentang Tanggal --}}
            <div class="md:col-span-2">
                <flux:input type="date" wire:model.live="startDate" label="Dari Tanggal" />
            </div>
            <div class="md:col-span-2">
                <flux:input type="date" wire:model.live="endDate" label="Sampai Tanggal" />
            </div>

            {{-- Tombol Ekspor --}}
            <div class="md:col-span-3 flex justify-end gap-2">
                <flux:button wire:click="exportExcel" variant="subtle" icon="table-cells" class="text-green-600"
                    title="Ekspor ke Excel" />
                <flux:button wire:click="exportPdf" variant="subtle" icon="document-text" class="text-red-600"
                    title="Ekspor ke PDF" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Section: Hanya untuk Admin/Superadmin --}}
        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
            <flux:card class="lg:col-span-1 border-t-4 border-t-indigo-500 shadow-lg">
                <form wire:submit="save" class="space-y-5">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        <flux:icon name="{{ $isEditing ? 'pencil-square' : 'plus-circle' }}" variant="mini" />
                        {{ $isEditing ? 'Update Data' : 'Daftar Baru' }}
                    </flux:heading>

                    <div class="space-y-4">
                        <flux:select wire:model="kategori_perkara" label="Kategori Perkara">
                            <option value="Pidana">Pidana</option>
                            <option value="Perdata">Perdata</option>
                        </flux:select>

                        <flux:input wire:model="nomor_surat" label="Nomor Surat Kuasa" placeholder="01/SK/2026..." />
                        <flux:input wire:model="tanggal_surat" type="date" label="Tanggal Surat" />

                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="pemberi" label="Pemberi" placeholder="Nama..." />
                            <flux:input wire:model="penerima" label="Penerima" placeholder="Nama..." />
                        </div>

                        <flux:select wire:model="jenis" label="Jenis Kuasa">
                            <option value="Khusus">Khusus</option>
                            <option value="Substitusi">Substitusi</option>
                            <option value="Insidentil">Insidentil</option>
                        </flux:select>

                        <flux:field>
                            <flux:label>Scan PDF (Maks 10MB)</flux:label>
                            <input type="file" wire:model="file_kuasa" accept="application/pdf"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />

                            {{-- Preview PDF Baru --}}
                            @if ($file_kuasa && $file_kuasa instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                <div class="mt-4 ring-1 ring-zinc-200 rounded-lg overflow-hidden h-48">
                                    <iframe src="{{ $file_kuasa->temporaryUrl() }}#toolbar=0"
                                        class="w-full h-full"></iframe>
                                </div>
                            @endif

                            @if ($isEditing && $old_file)
                                <div
                                    class="mt-3 flex items-center justify-between p-2.5 bg-zinc-50 dark:bg-zinc-800 border border-dashed border-zinc-300 rounded-lg text-xs">
                                    <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                        <flux:icon name="document-check" size="sm" />
                                        <span>Dokumen Tersimpan</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $old_file) }}" target="_blank"
                                        class="text-indigo-600 font-bold hover:underline">LIHAT</a>
                                </div>
                            @endif
                            <flux:error name="file_kuasa" />
                        </flux:field>
                    </div>

                    <div class="flex gap-2 pt-4 border-t border-zinc-100">
                        <flux:button type="submit" variant="primary"
                            class="flex-1 shadow-md shadow-indigo-200 dark:shadow-none">
                            {{ $isEditing ? 'Simpan Perubahan' : 'Daftarkan Sekarang' }}
                        </flux:button>
                        @if ($isEditing)
                            <flux:button wire:click="resetForm" variant="ghost">Batal</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        @endif

        {{-- Table Section: Lebar otomatis menyesuaikan role --}}
        <div class="{{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <flux:card class="lg:col-span-2">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Kategori</flux:table.column>
                        <flux:table.column>Nomor Surat</flux:table.column>
                        <flux:table.column>Tanggal Surat</flux:table.column>
                        <flux:table.column>Pemberi/Penerima</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($daftarKuasa as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell>
                                    <flux:badge color="{{ $item->kategori_perkara === 'Pidana' ? 'red' : 'blue' }}"
                                        inset="top bottom">
                                        {{ $item->kategori_perkara }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="font-medium text-xs">{{ $item->nomor_surat_kuasa }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm">
                                            {{ $item->tanggal_surat ? $item->tanggal_surat->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-sm font-medium">{{ $item->pemberi_kuasa }}</div>
                                    <div class="text-xs text-zinc-500 italic">Ke: {{ $item->penerima_kuasa }}</div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <flux:button wire:click="showDetail({{ $item->id }})" variant="ghost"
                                        icon="eye" size="sm" />
                                    @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
                                        <flux:button wire:click="edit({{ $item->id }})" variant="ghost"
                                            icon="pencil-square" size="sm" class="text-amber-600" />
                                        <flux:button wire:click="delete({{ $item->id }})" variant="ghost"
                                            icon="trash" size="sm" class="text-red-500 hover:bg-red-50"
                                            wire:confirm="Hapus permanen data ini?" />
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-zinc-400 py-10">Data tidak
                                    ditemukan
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                @if (method_exists($daftarKuasa, 'links'))
                    <div class="mt-4">{{ $daftarKuasa->links() }}</div>
                @endif
            </flux:card>
        </div>
    </div>

    {{-- Modal Detail --}}
    <flux:modal name="detail-kuasa" class="min-w-[400px]">
        @if ($detail)
            <div class="space-y-4">
                <flux:heading size="lg">Detail Surat Kuasa</flux:heading>
                <flux:separator variant="subtle" />
                <div class="grid grid-cols-1 gap-4">
                    <flux:field>
                        <flux:label>Kategori</flux:label>
                        <flux:text>{{ $detail->kategori_perkara }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Nomor Surat</flux:label>
                        <flux:text>{{ $detail->nomor_surat_kuasa }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Tanggal Surat</flux:label>
                        <flux:text>{{ $detail->tanggal_surat ? $detail->tanggal_surat->format('d F Y') : '-' }}
                        </flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Pemberi Kuasa</flux:label>
                        <flux:text>{{ $detail->pemberi_kuasa }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Penerima Kuasa</flux:label>
                        <flux:text>{{ $detail->penerima_kuasa }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Jenis Kuasa</flux:label>
                        <flux:badge size="sm">{{ $detail->jenis_kuasa }}</flux:badge>
                    </flux:field>
                </div>
                <div class="pt-4 border-t border-zinc-100">
                    @if ($detail->file_path)
                        <flux:button href="{{ asset('storage/' . $detail->file_path) }}" target="_blank"
                            variant="primary" icon="eye" class="w-full">Buka Dokumen PDF</flux:button>
                    @else
                        <div
                            class="text-center p-4 bg-zinc-50 rounded-lg border border-dashed text-xs text-zinc-500 italic">
                            File scan tidak tersedia</div>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>
</div>
