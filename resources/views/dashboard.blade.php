<div class="p-6 space-y-8">
    <div>
        <flux:heading size="xl">Selamat Datang</flux:heading>
        <flux:subheading>Ringkasan sistem manajemen perkara.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="flex flex-col gap-2">
            <flux:subheading>Total Perkara Pidana</flux:subheading>
            <flux:heading size="xl">{{ $totalPidana }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-2">
            <flux:subheading>Total Perkara Perdata</flux:subheading>
            <flux:heading size="xl">{{ $totalPerdata }}</flux:heading>
        </flux:card>
        <flux:card class="flex flex-col gap-2">
            <flux:subheading>Dokumen Terunggah</flux:subheading>
            <flux:heading size="xl">{{ $totalDokumen }}</flux:heading>
        </flux:card>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm px-5">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading>Aktivitas Perkara Terbaru</flux:heading>
        </div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nomor Registrasi</flux:table.column>
                <flux:table.column>Modul</flux:table.column>
                <flux:table.column>Tanggal</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($recentBerkas as $item)
                    <flux:table.row>
                        <flux:table.cell>{{ $item->nomor_registrasi }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="{{ $item->modul == 'pidana' ? 'danger' : 'primary' }}">
                                {{ ucfirst($item->modul) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $item->updated_at->diffForHumans() }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
