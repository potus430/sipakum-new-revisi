<div class="p-6 space-y-4">
    <flux:heading size="xl">Riwayat Aktivitas Sistem</flux:heading>

    <flux:card>
        <flux:input wire:model.live="search" placeholder="Cari modul atau aksi (CREATE/UPDATE/DELETE)..."
            icon="magnifying-glass" />

        <flux:table class="mt-4">
            <flux:table.columns>
                <flux:table.column>Waktu</flux:table.column>
                <flux:table.column>Petugas</flux:table.column>
                <flux:table.column>IP Address</flux:table.column>
                <flux:table.column>Modul</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
                <flux:table.column>Data Terkait</flux:table.column>
                <flux:table.column align="end">Detail</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($logs as $log)
                    <flux:table.row>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="font-medium">{{ $log->user->name ?? 'System' }}</div>
                        </flux:table.cell>

                        {{-- Isi Cell IP Address --}}
                        <flux:table.cell>
                            <flux:badge variant="subtle" size="sm" class="font-mono text-[10px]">
                                {{ $log->ip_address ?? '0.0.0.0' }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" variant="subtle">{{ $log->module }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $color = match ($log->action) {
                                    'CREATE' => 'green',
                                    'UPDATE' => 'amber',
                                    'DELETE' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm" variant="solid">{{ $log->action }}
                            </flux:badge>
                        </flux:table.cell>
                        {{-- KOLOM DATA TERKAIT: Menampilkan Identitas Utama dari JSON --}}
                        <flux:table.cell>
                            <div class="text-xs w-48 truncate">
                                @php
                                    // Ambil data dari new_data, jika kosong (saat delete) ambil dari old_data
                                    $displayData = $log->new_data ?: $log->old_data;
                                @endphp

                                @if ($log->module === 'Waarmerking')
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">Reg:
                                        {{ $displayData['nomor_register'] ?? '-' }}</span>
                                    <div class="text-[10px] text-zinc-500">{{ $displayData['nama_pemohon'] ?? '' }}
                                    </div>
                                @elseif($log->module === 'Gratifikasi')
                                    <span class="font-semibold">Pelapor: {{ $displayData['pelapor'] ?? '-' }}</span>
                                    <div class="text-[10px] text-zinc-500">Nilai:
                                        Rp{{ number_format($displayData['estimasi_nilai'] ?? 0, 0, ',', '.') }}</div>
                                @elseif($log->module === 'Pengaduan')
                                    <span
                                        class="font-semibold">{{ Str::limit($displayData['judul'] ?? '-', 30) }}</span>
                                    <div class="text-[10px] text-zinc-500">Oleh:
                                        {{ $displayData['is_anonim'] ?? false ? 'Anonim' : $displayData['pelapor'] ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic">ID: {{ $log->target_id }}</span>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:button wire:click="showDiff({{ $log->id }})" icon="eye" variant="ghost"
                                size="sm" inset="top bottom" tooltip="Lihat Detail & Perubahan" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </flux:card>

    {{-- MODAL DETAIL LOG AKTIVITAS LENGKAP --}}
    <flux:modal name="modal-detail-log" variant="flyout" class="space-y-6">
        @if ($selectedLog)
            <div class="space-y-6">
                {{-- Header --}}
                <div>
                    <div class="flex justify-between items-center text-sm mb-2">
                        <flux:badge size="sm" variant="subtle" color="zinc" icon="globe-alt" class="font-mono">
                            {{ $selectedLog->ip_address }}
                        </flux:badge>
                        <span
                            class="text-zinc-400 text-[10px]">{{ $selectedLog->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <flux:heading size="xl" class="font-bold tracking-tight">
                        @if ($selectedLog->action === 'DELETE')
                            <span class="text-red-600 uppercase">Penghapusan Data</span>
                        @else
                            Detail Aktivitas: <span class="text-indigo-600">{{ $selectedLog->action }}</span>
                        @endif
                    </flux:heading>
                    <flux:subheading>Modul {{ $selectedLog->module }} (ID #{{ $selectedLog->target_id }})
                    </flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                {{-- Metadata Box --}}
                <div
                    class="grid grid-cols-2 gap-4 bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase block">Petugas</label>
                        <span
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ $selectedLog->user->name ?? 'System' }}</span>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase block">Role</label>
                        <flux:badge size="sm" variant="subtle" color="indigo">
                            {{ $selectedLog->user->role ?? '-' }}</flux:badge>
                    </div>
                </div>

                {{-- Tampilan Perbandingan Data --}}
                <div class="space-y-4">

                    {{-- KONDISI DELETE: Tampilkan data yang dibuang --}}
                    @if ($selectedLog->action === 'DELETE')
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-red-600 font-bold text-xs uppercase">
                                <flux:icon name="trash" variant="mini" />
                                Data Terakhir Sebelum Dihapus
                            </div>
                            <div
                                class="p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/40 rounded-xl">
                                <pre class="text-[11px] font-mono leading-relaxed text-red-900 dark:text-red-200 overflow-x-auto">{{ json_encode($selectedLog->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>

                        {{-- KONDISI UPDATE: Tampilkan Perbandingan --}}
                    @elseif($selectedLog->action === 'UPDATE')
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold text-zinc-500 uppercase italic">Data Lama:</span>
                                <div
                                    class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-x-auto">
                                    <pre class="text-[10px] font-mono">{{ json_encode($selectedLog->old_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Data Baru:</span>
                                <div
                                    class="p-3 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 rounded-lg overflow-x-auto">
                                    <pre class="text-[10px] font-mono text-emerald-900 dark:text-emerald-200">{{ json_encode($selectedLog->new_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>

                        {{-- KONDISI CREATE --}}
                    @else
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold text-emerald-600 uppercase">Data Baru Dibuat:</span>
                            <div
                                class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 rounded-xl overflow-x-auto">
                                <pre class="text-[11px] font-mono text-emerald-900 dark:text-emerald-200">{{ json_encode($selectedLog->new_data, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="w-full">Tutup Riwayat</flux:button>
                    </flux:modal.close>
                </div>

                @if ($selectedLog->action === 'DELETE')
                    <div
                        class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <flux:icon name="exclamation-triangle" class="text-amber-600" />
                            <div>
                                <p class="text-sm font-bold text-amber-800 dark:text-amber-200">Pulihkan Data ini?</p>
                                <p class="text-xs text-amber-700 dark:text-amber-400">Data akan dimasukkan kembali ke
                                    modul {{ $selectedLog->module }}.</p>
                            </div>
                        </div>

                        <flux:button wire:click="restore({{ $selectedLog->id }})"
                            wire:confirm="Apakah Anda yakin ingin memulihkan data ini kembali ke sistem?"
                            variant="primary" color="amber" size="sm" icon="arrow-path">
                            Pulihkan Sekarang
                        </flux:button>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>

</div>
