<div class="p-6 space-y-6">
    <flux:heading size="xl">Manajemen Laporan Gratifikasi</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
            <flux:card class="lg:col-span-1 space-y-4">
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="pelapor" label="Nama Pelapor" icon="user" required />

                    <flux:input wire:model="pemberi" label="Nama Terlapor" icon="user-minus"
                        placeholder="Nama yang dilaporkan" required />

                    <flux:input wire:model="bentuk_gratifikasi" label="Bentuk Gratifikasi"
                        placeholder="Contoh: Uang, Barang, atau Nihil" />

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="estimasi_nilai" type="number" label="Estimasi Nilai (Rp)" />
                        <flux:input wire:model="tanggal_penerimaan" type="date" label="Tanggal Penerimaan" />
                    </div>

                    <flux:textarea wire:model="kronologi" label="Kronologi / Keterangan" />

                    <flux:button type="submit" variant="primary" class="w-full">Simpan Laporan</flux:button>
                </form>
            </flux:card>
        @endif

        {{-- Table Section: Lebar otomatis menyesuaikan role --}}
        <div class="{{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 'lg:col-span-2' : 'lg:col-span-3' }}">

            <flux:card class="lg:col-span-2">
                <flux:input wire:model.live="search" placeholder="Cari pelapor..." class="mb-4" />
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Pelapor</flux:table.column>
                        <flux:table.column>Terlapor</flux:table.column>
                        <flux:table.column>Bentuk</flux:table.column>
                        <flux:table.column>Nilai</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($gratifikasis as $item)
                            <flux:table.row>
                                <flux:table.cell class="font-medium">{{ $item->pelapor }}</flux:table.cell>
                                <flux:table.cell>{{ $item->pemberi }}</flux:table.cell>
                                <flux:table.cell>{{ $item->bentuk_gratifikasi }}</flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($item->estimasi_nilai, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm">{{ $item->status }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex gap-2">
                                        {{-- Tombol View Detail --}}
                                        <flux:button wire:click="showDetail({{ $item->id }})" variant="ghost"
                                            icon="eye" size="sm" tooltip="Lihat Detail" />
                                        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <flux:button wire:click="edit({{ $item->id }})" variant="ghost"
                                                icon="pencil-square" size="sm" />
                                            <flux:button wire:click="delete({{ $item->id }})" variant="ghost"
                                                icon="trash" size="sm" class="text-red-500" />
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>

    {{-- Modal Detail Gratifikasi --}}
    <flux:modal name="detail-gratifikasi"
        x-on:open-modal.window="$event.detail.name === 'detail-gratifikasi' && $reveal()" class="md:w-[600px]">
        <div wire:key="detail-modal-{{ $selectedGratifikasi?->id ?? 'none' }}">
            @if ($selectedGratifikasi)
                <div class="space-y-6">
                    <div>
                        <flux:heading size="xl" class="text-indigo-600 font-bold">Detail Laporan Gratifikasi
                        </flux:heading>
                        <flux:subheading>Informasi lengkap penerimaan gratifikasi</flux:subheading>
                    </div>

                    <flux:separator variant="subtle" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Nama Pelapor</span>
                            <span
                                class="text-base font-bold text-zinc-900 dark:text-white">{{ $selectedGratifikasi->pelapor }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Nama Terlapor/Pemberi</span>
                            <span
                                class="text-base font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGratifikasi->pemberi }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Bentuk Gratifikasi</span>
                            <flux:badge variant="subtle">{{ $selectedGratifikasi->bentuk_gratifikasi }}</flux:badge>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Estimasi Nilai</span>
                            <span class="text-base font-bold text-emerald-600">Rp
                                {{ number_format($selectedGratifikasi->estimasi_nilai, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Tanggal Penerimaan</span>
                            <span
                                class="text-sm">{{ $selectedGratifikasi->tanggal_penerimaan ? $selectedGratifikasi->tanggal_penerimaan->format('d F Y') : '-' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 uppercase font-semibold">Status Laporan</span>
                            <flux:badge size="sm">{{ $selectedGratifikasi->status }}</flux:badge>
                        </div>
                    </div>

                    <div
                        class="flex flex-col p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-100 dark:border-zinc-700">
                        <span class="text-xs text-zinc-500 uppercase font-semibold mb-1">Kronologi / Keterangan</span>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            {{ $selectedGratifikasi->kronologi ?? 'Tidak ada keterangan tambahan.' }}
                        </p>
                    </div>

                    @if ($selectedGratifikasi->file_bukti)
                        <div class="pt-2">
                            <flux:button href="{{ asset('storage/' . $selectedGratifikasi->file_bukti) }}"
                                target="_blank" variant="primary" icon="paper-clip" class="w-full shadow-lg">Lihat Bukti
                                Lampiran (PDF/Gambar)</flux:button>
                        </div>
                    @endif

                    <div class="pt-4 flex justify-between items-center border-t border-zinc-100">
                        <span class="text-[10px] text-zinc-400 italic">Diinput oleh:
                            {{ $selectedGratifikasi->user->name ?? 'Sistem' }}</span>
                        <flux:modal.close>
                            <flux:button variant="ghost">Tutup</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            @else
                <div class="flex justify-center p-12">
                    <flux:icon name="arrow-path" class="animate-spin text-zinc-300" />
                </div>
            @endif
        </div>
    </flux:modal>
</div>
