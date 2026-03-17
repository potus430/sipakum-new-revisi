<div class="p-6 space-y-6">
    <flux:heading size="xl">Manajemen Pengaduan</flux:heading>

    {{-- Panel Filter & Periode Laporan --}}
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div class="md:col-span-2">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul, pelapor, atau terlapor..."
                    icon="magnifying-glass" />
            </div>
            <flux:input type="date" wire:model.live="startDate" label="Mulai Periode" />
            <flux:input type="date" wire:model.live="endDate" label="Akhir Periode" />

            <flux:button wire:click="exportExcel" variant="subtle" icon="table-cells" class="text-green-600" />
            <flux:button wire:click="exportPdf" variant="subtle" icon="document-text" class="text-red-600" />

        </div>
        {{-- Di dalam bagian Filter Card --}}


    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Input Pengaduan --}}
        <flux:card class="lg:col-span-1">
            <form wire:submit="save" class="space-y-4">
                <flux:heading level="2">Formulir Pengaduan</flux:heading>

                <flux:input wire:model="judul" label="Judul Pengaduan" placeholder="Ringkasan inti pengaduan" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="pelapor" label="Nama Pelapor" />
                    <flux:input wire:model="terlapor" label="Nama Terlapor" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:select wire:model="jenis_pengaduan" label="Jenis Pengaduan">
                        <option value="">-- Pilih --</option>
                        <option value="Pelayanan">Pelayanan</option>
                        <option value="Disiplin">Disiplin</option>
                        <option value="Kode Etik">Kode Etik</option>
                        <option value="Lainnya">Lainnya</option>
                    </flux:select>
                    <flux:select wire:model="sarana_pengaduan" label="Sarana">
                        <option value="">-- Pilih --</option>
                        <option value="Meja Pengaduan">Meja Pengaduan</option>
                        <option value="Website">Website</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Surat">Surat</option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="isi_pengaduan" label="Rincian Pengaduan" rows="3" />
                <flux:textarea wire:model="tindak_lanjut" label="Tindak Lanjut" rows="2" />
                <flux:textarea wire:model="keterangan" label="Keterangan" rows="2" />

                <div class="flex items-center gap-4">
                    <flux:checkbox wire:model="is_anonim" label="Anonim" />
                    <flux:input wire:model="file_pendukung" type="file" size="sm" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ $selectedId ? 'Update Data' : 'Simpan Pengaduan' }}
                </flux:button>

                @if ($selectedId)
                    <flux:button wire:click="resetForm" variant="ghost" class="w-full">Batal Edit</flux:button>
                @endif
            </form>
        </flux:card>

        {{-- Tabel Register Pengaduan --}}
        <flux:card class="lg:col-span-2">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column>Pelapor/Terlapor</flux:table.column>
                    <flux:table.column>Jenis & Sarana</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($pengaduans as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell class="text-xs">
                                {{ $item->created_at->format('d/m/Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-800">{{ $item->pelapor }}</span>
                                    <span class="text-xs text-zinc-500 italic">Vs. {{ $item->terlapor }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col gap-1">
                                    <flux:badge size="sm" color="zinc" inset="top bottom">
                                        {{ $item->jenis_pengaduan }}</flux:badge>
                                    <span class="text-[10px] text-zinc-500">{{ $item->sarana_pengaduan }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $color = match ($item->status) {
                                        'Selesai' => 'green',
                                        'Investigasi' => 'orange',
                                        'Verifikasi' => 'blue',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge color="{{ $color }}" size="sm">{{ $item->status }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex justify-end gap-1">
                                    {{-- Tombol Detail --}}
                                    <flux:button wire:click="viewDetail({{ $item->id }})" variant="ghost"
                                        size="sm" icon="eye" inset="top bottom" />

                                    {{-- Tombol Edit/Update Status --}}
                                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm"
                                        icon="pencil-square" inset="top bottom" />

                                    {{-- Tombol Hapus --}}
                                    <flux:button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus pengaduan ini?" variant="ghost"
                                        size="sm" icon="trash" class="text-red-600" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
            <div class="mt-4">{{ $pengaduans->links() }}</div>
        </flux:card>
    </div>

    {{-- Modal Detail --}}
    <flux:modal name="modal-detail-pengaduan" class="md:w-[600px]">
        @if ($selectedPengaduan)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ $selectedPengaduan->judul }}</flux:heading>
                    <flux:subheading>Detail Register Pengaduan</flux:subheading>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <flux:field>
                        <flux:label>Pelapor</flux:label>
                        <flux:text>{{ $selectedPengaduan->pelapor }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Terlapor</flux:label>
                        <flux:text>{{ $selectedPengaduan->terlapor }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Tindak Lanjut</flux:label>
                        <flux:text>{{ $selectedPengaduan->tindak_lanjut ?? '-' }}</flux:text>
                    </flux:field>
                    <flux:field>
                        <flux:label>Keterangan</flux:label>
                        <flux:text>{{ $selectedPengaduan->keterangan ?? '-' }}</flux:text>
                    </flux:field>
                </div>

                @if ($selectedPengaduan && $selectedPengaduan->logs->count() > 0)
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:heading size="sm" class="mb-2">Riwayat Perubahan Status</flux:heading>
                        <div class="space-y-3">
                            @foreach ($selectedPengaduan->logs()->latest()->get() as $log)
                                <div
                                    class="text-xs flex flex-col p-2 bg-zinc-50 dark:bg-zinc-800/50 rounded border border-zinc-100 dark:border-zinc-700">
                                    <div class="flex justify-between font-medium">
                                        <span>{{ $log->user->name }}</span>
                                        <span class="text-zinc-500">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="mt-1">
                                        Mengubah status dari <span
                                            class="px-1 bg-zinc-200 dark:bg-zinc-700 rounded">{{ $log->status_lama }}</span>
                                        ke <span
                                            class="px-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded">{{ $log->status_baru }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($selectedPengaduan->file_pendukung)
                    <flux:button wire:click="downloadFile({{ $selectedPengaduan->id }})" variant="subtle"
                        icon="arrow-down-tray" class="w-full">
                        Unduh Bukti Pendukung
                    </flux:button>
                @endif
            </div>
        @endif
    </flux:modal>

    {{-- Modal Update Status (Tetap seperti sebelumnya) --}}
    <flux:modal name="modal-update-status" class="md:w-[400px]">
        <form wire:submit="save" class="space-y-4">
            <flux:heading>Update Status & Tindak Lanjut</flux:heading>
            <flux:select wire:model="status" label="Status Terkini">
                <option value="Terima">Terima</option>
                <option value="Verifikasi">Verifikasi</option>
                <option value="Investigasi">Investigasi</option>
                <option value="Selesai">Selesai</option>
            </flux:select>
            <flux:textarea wire:model="tindak_lanjut" label="Catatan Tindak Lanjut" />
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$flux.modal('modal-update-status').close()" variant="ghost">Batal
                </flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
