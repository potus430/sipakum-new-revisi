{{-- resources/views/livewire/pidana-create.blade.php --}}
<div class="p-8 max-w-4xl mx-auto">
    <flux:heading size="xl" class="mb-6">Tambah Register Pidana</flux:heading>

    <form wire:submit="store"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" placeholder="Contoh: 123/Pid.B/2026/PN..."
                required />
            <flux:input wire:model="pihak" label="Terdakwa / Pihak" placeholder="Nama Terdakwa" required />

            <flux:select wire:model="jenis" label="Jenis Perkara" placeholder="Pilih jenis...">
                <option value="PID.B">PID.B (Biasa)</option>
                <option value="PID.SUS">PID.SUS (Khusus)</option>
                <option value="ANAK">ANAK</option>
                <option value="PRAPERADILAN">PRAPERADILAN</option>
                <option value="PIDANA CEPAT">PIDANA CEPAT</option>
            </flux:select>

            <flux:input wire:model="tgl_putus" type="date" label="Tanggal Putus" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="pasal" label="Pasal yang Disangkakan" placeholder="Contoh: Pasal 363 KUHP" />
            <flux:input wire:model="tgl_penyerahan" type="date" label="Tanggal Penyerahan Berkas" />
        </div>

        {{-- Tambahan Kolom Isi Putusan --}}
        <flux:textarea wire:model="isi_putusan" label="Isi Putusan (Amar)" rows="5"
            placeholder="Masukkan petikan putusan..." required />

        {{-- BAGIAN DOKUMEN PENDUKUNG (INTEGRASI PDFOBJECT) --}}
        <div class="space-y-4 border-t border-accent-100 pt-6">
            <flux:label>Dokumen Pendukung (Hanya PDF)</flux:label>

            {{-- resources/views/livewire/pidana-create.blade.php --}}

            @foreach ($fileInputs as $index)
                <div class="p-4 border rounded-xl bg-zinc-50 dark:bg-zinc-800/50" x-data="{
                    previewUrl: null,
                    generatePreview(event) {
                        const file = event.target.files[0];
                        if (file && file.type === 'application/pdf') {
                            // Membuat URL lokal dari file yang dipilih
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    }
                }">

                    <flux:label>Unggah Dokumen (PDF)</flux:label>
                    {{-- Input File Native untuk menangkap event change --}}
                    <input type="file" wire:model="files.{{ $index }}" @change="generatePreview"
                        accept="application/pdf"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1" />

                    {{-- Preview Section --}}
                    <div x-show="previewUrl" x-transition class="mt-4">
                        <flux:text size="sm" class="mb-2 font-medium text-indigo-600">
                            Preview Dokumen Baru:
                        </flux:text>

                        <div
                            class="w-full h-[500px] border border-zinc-300 rounded-lg overflow-hidden bg-white shadow-inner">
                            {{-- Menggunakan <embed> dengan Blob URL terbukti lebih ampuh mencegah download --}}
                            <template x-if="previewUrl">
                                <embed :src="previewUrl" type="application/pdf" class="w-full h-full" />
                            </template>
                        </div>

                        <flux:button variant="ghost" size="xs" class="mt-2"
                            @click="previewUrl = null; $wire.set('files.{{ $index }}', null)">
                            Hapus Preview
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
                Tambah Slot Dokumen
            </flux:button>
        </div>

        <div class="flex items-center gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
            <flux:button type="submit" variant="primary">Simpan Berkas</flux:button>
            <flux:button href="{{ route('pidana.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
