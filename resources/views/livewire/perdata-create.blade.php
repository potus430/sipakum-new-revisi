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

        {{-- Bagian Dokumen Pendukung pada perdata-create.blade.php --}}
        <div class="space-y-4 border-t border-accent-100 pt-6">
            <flux:label>Dokumen Pendukung (Hanya PDF)</flux:label>

            @foreach ($fileInputs as $index)
                <div class="p-4 border rounded-xl bg-zinc-50 dark:bg-zinc-800/50" x-data="{
                    previewUrl: null,
                    generatePreview(event) {
                        const file = event.target.files[0];
                        if (file && file.type === 'application/pdf') {
                            // Membuat URL lokal dari file yang dipilih di browser
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    }
                }">

                    <flux:label>Unggah Dokumen (PDF)</flux:label>
                    {{-- Input File Native untuk menangkap event change --}}
                    <input type="file" wire:model="files.{{ $index }}" @change="generatePreview"
                        accept="application/pdf"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1 cursor-pointer" />

                    {{-- Area Preview PDF --}}
                    <div x-show="previewUrl" x-transition class="mt-4 border-t border-dashed border-zinc-200 pt-4">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:icon name="eye" size="sm" class="text-indigo-600" />
                            <flux:text size="sm" class="font-medium text-indigo-600">
                                Preview Dokumen Perdata:
                            </flux:text>
                        </div>

                        <div
                            class="w-full h-[500px] border border-zinc-300 rounded-lg overflow-hidden bg-white shadow-inner">
                            <template x-if="previewUrl">
                                {{-- Menggunakan tag <embed> untuk merender Blob URL --}}
                                <embed :src="previewUrl" type="application/pdf" class="w-full h-full" />
                            </template>
                        </div>

                        <flux:button variant="ghost" size="xs" class="mt-2 text-red-500"
                            @click="previewUrl = null; $wire.set('files.{{ $index }}', null)">
                            Hapus Pilihan
                        </flux:button>
                    </div>

                    <flux:error name="files.{{ $index }}" />

                    @if (count($fileInputs) > 1)
                        <flux:button variant="ghost" size="xs" color="red"
                            wire:click="removeFileInput({{ $index }})" class="mt-2">
                            Hapus Slot
                        </flux:button>
                    @endif
                </div>
            @endforeach

            <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus"
                class="text-indigo-600">
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
