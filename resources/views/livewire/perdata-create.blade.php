<div class="p-8 max-w-4xl mx-auto" x-data @notify.window="flux.toast.success($event.detail.message)">
    <flux:heading size="xl" class="mb-6 text-accent-900">Tambah Register Perdata</flux:heading>

    <form wire:submit="store"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-accent-100 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required placeholder="Masukkan nomor perkara..." />

            <flux:input wire:model="tgl_register" type="date" label="Tanggal Register" />

            {{-- Revisi: Perubahan Label Penggugat menjadi Penggugat/Pemohon --}}
            <flux:input wire:model="penggugat_pemohon" label="Penggugat / Pemohon" required
                placeholder="Nama penggugat atau pemohon" />

            <flux:input wire:model="tergugat" label="Nama Tergugat" required placeholder="Nama tergugat" />

            <flux:select wire:model="jenis_perkara" label="Jenis Perkara">
                <flux:select.option value="Gugatan">Gugatan</flux:select.option>
                <flux:select.option value="Permohonan">Permohonan</flux:select.option>
                <flux:select.option value="Gugatan Sederhana">Gugatan Sederhana</flux:select.option>
            </flux:select>

            {{-- Kolom Pasal dihilangkan sesuai revisi --}}

            {{-- Revisi: Penambahan Kolom Tanggal Putusan --}}
            <flux:input wire:model="tgl_putusan" type="date" label="Tanggal Putusan" />

            {{-- Revisi: Penambahan Kolom Tanggal Penyerahan Berkas --}}
            <flux:input wire:model="tgl_penyerahan_berkas" type="date" label="Tanggal Penyerahan Berkas" />
        </div>

        <flux:textarea wire:model="isi_gugatan" label="Isi Putusan" placeholder="Masukkan ringkasan amar putusan..."
            rows="5" />

        <div class="space-y-4">
            <flux:label>Dokumen Pendukung</flux:label>

            @foreach ($fileInputs as $index => $value)
                <div class="flex flex-col gap-2 p-4 border border-dashed border-accent-200 rounded-lg bg-accent-50/30">
                    <div class="flex gap-2 items-center">
                        <input type="file" wire:model="files.{{ $index }}"
                            class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer" />

                        @if (count($fileInputs) > 1)
                            <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                variant="danger" icon="trash" size="sm" />
                        @endif
                    </div>

                    @if (isset($files[$index]))
                        <div class="mt-2">
                            @if (in_array($files[$index]->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $files[$index]->temporaryUrl() }}"
                                    class="h-20 w-20 object-cover rounded border border-primary-200">
                            @else
                                <div class="flex items-center gap-2 text-xs text-accent-700">
                                    <flux:icon name="document-text" size="sm" />
                                    {{ $files[$index]->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>
                    @endif
                    <flux:error name="files.{{ $index }}" />
                </div>
            @endforeach

            <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus"
                class="text-primary-600">
                Tambah Dokumen Lagi
            </flux:button>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-zinc-100">
            {{-- Tombol Simpan dengan warna Hijau (Primary) --}}
            <flux:button type="submit" variant="primary" class="bg-primary-500 hover:bg-primary-600">
                Simpan Perkara
            </flux:button>

            <flux:button href="{{ route('perdata.index') }}" variant="ghost" wire:navigate>
                Batal
            </flux:button>
        </div>
    </form>
</div>
