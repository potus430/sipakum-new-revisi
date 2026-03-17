<div class="p-6 space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <flux:heading size="xl">Dashboard SIPAKUM</flux:heading>
            <flux:subheading>Selamat datang kembali, pantau kinerja perkara hari ini.</flux:subheading>
        </div>
        <flux:subheading>Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}</flux:subheading>
    </div>

    {{-- Statistik Utama Perkara --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <flux:card class="border-l-4 border-l-red-500">
            <flux:subheading>Total Perkara Pidana</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $totalPidana }}</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Kasus tindak pidana aktif</flux:text>
        </flux:card>

        <flux:card class="border-l-4 border-l-blue-500">
            <flux:subheading>Total Perkara Perdata</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $totalPerdata }}</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Sengketa perdata terdaftar</flux:text>
        </flux:card>

        <flux:card class="border-l-4 border-l-emerald-500">
            <flux:subheading>Total Dokumen</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $totalDokumen }}</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Arsip digital tersimpan</flux:text>
        </flux:card>

        <flux:card class="border-l-4 border-l-amber-500">
            <flux:subheading>Gratifikasi</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $stats['gratifikasi_total'] }}</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Laporan gratifikasi</flux:text>
        </flux:card>

        <flux:card class="border-l-4 border-l-purple-500">
            <flux:subheading>User Aktif</flux:subheading>
            <flux:heading size="xl" class="mt-1">Admin</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Role: Superuser</flux:text>
        </flux:card>
    </div>

    {{-- Section Khusus Ringkasan Pengaduan --}}
    <flux:heading size="lg" class="mt-8 mb-4">Ringkasan Pengaduan Masyarakat</flux:heading>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-400">
                    <flux:icon.chat-bubble-left-right />
                </div>
                <div>
                    <flux:subheading>Total Laporan</flux:subheading>
                    <flux:heading size="xl">{{ $pengaduanStats['total'] }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                    <flux:icon.arrow-path />
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <flux:subheading>Dalam Proses</flux:subheading>
                        <flux:badge color="blue" size="sm" inset="top bottom">{{ $pengaduanStats['proses'] }}
                        </flux:badge>
                    </div>
                    <flux:heading size="lg" class="mt-1">
                        {{ $pengaduanStats['total'] > 0 ? round(($pengaduanStats['proses'] / $pengaduanStats['total']) * 100) : 0 }}%
                    </flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
                    <flux:icon.check-circle />
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <flux:subheading>Selesai</flux:subheading>
                        <flux:badge color="green" size="sm" inset="top bottom">{{ $pengaduanStats['selesai'] }}
                        </flux:badge>
                    </div>
                    <flux:heading size="lg" class="mt-1">{{ $pengaduanStats['persen_selesai'] }}%</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Progress Bar Visual --}}
    <flux:card>
        <div class="flex justify-between items-center mb-2">
            <flux:heading size="sm">Rasio Penyelesaian Laporan Pengaduan</flux:heading>
            <flux:text size="sm">{{ $pengaduanStats['selesai'] }} dari {{ $pengaduanStats['total'] }} Laporan
            </flux:text>
        </div>
        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2.5">
            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $pengaduanStats['persen_selesai'] }}%">
            </div>
        </div>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Aktivitas Perkara Terbaru --}}
        <flux:card>
            <div class="flex justify-between items-center mb-6">
                <flux:heading>Aktivitas Perkara Terbaru</flux:heading>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nomor Registrasi</flux:table.column>
                    <flux:table.column>Jenis Modul</flux:table.column>
                    <flux:table.column>Status Terakhir</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($recentBerkas as $item)
                        <flux:table.row>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $item->nomor_registrasi }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $item->modul == 'pidana' ? 'red' : 'blue' }}">
                                    {{ strtoupper($item->modul) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500 italic text-xs">
                                {{ $item->updated_at->diffForHumans() }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>

        {{-- Laporan Periode --}}
        <flux:card>
            <div class="flex flex-col h-full">
                <flux:heading>Ekspor Laporan Keseluruhan</flux:heading>
                <flux:subheading class="mb-6">Unduh laporan SIPAKUM berdasarkan rentang tanggal.</flux:subheading>

                <form wire:submit.prevent="exportExcel" class="space-y-4 mt-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input type="date" wire:model="startDate" label="Tanggal Mulai" />
                        <flux:input type="date" wire:model="endDate" label="Tanggal Akhir" />
                    </div>

                    <div class="flex gap-2">
                        <flux:button type="submit" icon="table-cells" variant="primary" class="flex-1">Excel
                        </flux:button>
                        <flux:button wire:click="exportPDF" icon="document-text" variant="subtle" class="flex-1">PDF
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>
    </div>
</div>
