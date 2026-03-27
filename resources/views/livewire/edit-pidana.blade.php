{{-- resources/views/livewire/edit-pidana.blade.php --}}
<div class="p-8 max-w-4xl mx-auto">
    <flux:heading size="xl" class="mb-6 text-accent-900">Edit Register Pidana</flux:heading>

    <form wire:submit="update"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-accent-100 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required />
            <flux:input wire:model="pihak" label="Terdakwa / Pihak" required />

            <flux:select wire:model="jenis" label="Jenis Perkara">
                <flux:select.option value="PID.B">PID.B (Biasa)</flux:select.option>
                <flux:select.option value="PID.SUS">PID.SUS (Khusus)</flux:select.option>
                <flux:select.option value="ANAK">ANAK</flux:select.option>
                <flux:select.option value="PRAPERADILAN">PRAPERADILAN</flux:select.option>
            </flux:select>

            <flux:input wire:model="tgl_putus" type="date" label="Tanggal Putus" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="pasal" label="Pasal yang Disangkakan" />
            <flux:input wire:model="tgl_penyerahan" type="date" label="Tanggal Penyerahan Berkas" />
        </div>

        {{-- Kolom Isi Putusan (Revisi) --}}
        <flux:textarea wire:model="isi_putusan" label="Isi Putusan (Amar)" rows="5" required />

        <div class="space-y-4 border-t border-accent-100 pt-6">
            <flux:label class="text-accent-900 font-semibold">Dokumen Terlampir (Sudah Tersimpan)</flux:label>

            <div class="grid grid-cols-1 gap-2">
                @foreach ($existingFiles as $file)
                    <div
                        class="flex items-center justify-between p-3 bg-accent-50/50 dark:bg-zinc-800/30 border border-accent-100 dark:border-zinc-700 rounded-lg group hover:border-red-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <flux:icon name="document-text" class="text-accent-500" />
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $file->file_name }}</span>
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                    class="text-xs text-indigo-600 underline">Buka Dokumen</a>
                            </div>
                        </div>
                        <flux:button wire:click="deleteFile({{ $file->id }})" variant="ghost" icon="trash"
                            size="sm"
                            class="text-red-500 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                            wire:confirm="Yakin ingin menghapus dokumen ini secara permanen dari server?" />
                    </div>
                @endforeach
            </div>

            {{-- BAGIAN TAMBAH DOKUMEN BARU (INTEGRASI PDFOBJECT) --}}
            <div class="space-y-4 border-t border-accent-100 pt-6 mt-6">
                <flux:label class="text-accent-900 font-semibold">Tambah Dokumen Baru (Hanya PDF)</flux:label>

                @foreach ($fileInputs as $index => $val)
                    <div x-data="{
                        previewUrl: null,
                        generatePreview(event) {
                            const file = event.target.files[0];
                            if (file && file.type === 'application/pdf') {
                                // Membuat URL lokal dari file yang dipilih di komputer user
                                this.previewUrl = URL.createObjectURL(file);
                            }
                        }
                    }"
                        class="space-y-3 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-indigo-100 transition-colors">

                        <div class="flex gap-2 items-center">
                            {{-- Input File dengan Event Change untuk memicu pratinjau --}}
                            <input type="file" wire:model="files.{{ $index }}" @change="generatePreview"
                                accept="application/pdf"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-indigo-50 cursor-pointer" />

                            @if (count($fileInputs) > 1)
                                <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                    variant="danger" icon="trash" size="sm" class="flex-shrink-0" />
                            @endif
                        </div>

                        {{-- FITUR PREVIEW PDF UNTUK FILE BARU --}}
                        <div x-show="previewUrl" x-transition class="mt-4 pt-4 border-t border-dashed border-zinc-200">
                            <div class="flex items-center gap-2 mb-2">
                                <flux:icon name="eye" size="sm" class="text-indigo-600" />
                                <flux:text size="sm" class="font-medium text-indigo-600">Preview Dokumen Baru:
                                </flux:text>
                            </div>

                            <div
                                class="w-full h-[450px] border border-zinc-300 rounded-lg overflow-hidden bg-white shadow-inner">
                                <template x-if="previewUrl">
                                    {{-- Menggunakan <embed> dengan Blob URL untuk menghindari instruksi download server --}}
                                    <embed :src="previewUrl" type="application/pdf" class="w-full h-full" />
                                </template>
                            </div>

                            <flux:button variant="ghost" size="xs" class="mt-2"
                                @click="previewUrl = null; $wire.set('files.{{ $index }}', null)">
                                Batalkan Pilihan
                            </flux:button>
                        </div>

                        <flux:error name="files.{{ $index }}" />
                    </div>
                @endforeach

                <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus"
                    class="text-indigo-600">
                    Tambah Slot Dokumen
                </flux:button>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-6 border-t border-accent-100 dark:border-zinc-800">
            <flux:button type="submit" variant="primary" class="bg-amber-400 hover:bg-amber-500 text-white">
                Simpan Perubahan
            </flux:button>
            <flux:button href="{{ route('pidana.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
