<div class="p-6 space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <flux:heading size="xl">Dashboard SIPAKUM</flux:heading>
            <flux:subheading>Selamat datang kembali, pantau kinerja perkara hari ini.</flux:subheading>
        </div>
        <flux:subheading>Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}</flux:subheading>

        
    </div>

    

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
            <flux:subheading>Dokumen Terunggah</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $totalDokumen }}</flux:heading>
            <flux:text size="sm" class="text-zinc-500 mt-2">Total file bukti/berkas</flux:text>
        </flux:card>

        <flux:card class="border-l-4 border-orange-500">
        <flux:subheading>Pengaduan (Pending)</flux:subheading>
        <flux:heading size="xl" class="text-orange-600">{{ $stats['pengaduan_pending'] }}</flux:heading>
    </flux:card>

    <flux:card class="border-l-4 border-zinc-500">
        <flux:subheading>Total Gratifikasi</flux:subheading>
        <flux:heading size="xl">{{ $stats['gratifikasi_total'] }}</flux:heading>
    </flux:card>
    </div>

    <flux:card>
    <flux:heading>Ekspor Laporan Bulanan</flux:heading>
    <div class="flex gap-4 items-end mt-4">
        <flux:input wire:model="startDate" type="date" label="Dari Tanggal" />
        <flux:input wire:model="endDate" type="date" label="Sampai Tanggal" />
        
        <div class="flex gap-2">
            <flux:button wire:click="exportExcel" icon="table-cells">Excel</flux:button>
            <flux:button wire:click="exportPDF" icon="document-text" variant="primary">PDF</flux:button>
        </div>
    </div>
</flux:card>
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
                        <flux:table.cell class="text-zinc-500 italic">
                            {{ $item->updated_at->diffForHumans() }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    
</div>