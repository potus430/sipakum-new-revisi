<div class="p-8 max-w-4xl mx-auto" x-data @notify.window="flux.toast.success($event.detail.message)">
    <flux:heading size="xl" class="mb-6 text-accent-900">Edit Register Perdata</flux:heading>

    <form wire:submit="update"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-accent-100 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required />
            <flux:input wire:model="tgl_register" type="date" label="Tanggal Register" />

            <flux:input wire:model="penggugat_pemohon" label="Penggugat / Pemohon" required />
            <flux:input wire:model="tergugat" label="Nama Tergugat" required />

            <flux:select wire:model="jenis_perkara" label="Jenis Perkara">
                <flux:select.option value="Gugatan">Gugatan</flux:select.option>
                <flux:select.option value="Permohonan">Permohonan</flux:select.option>
                <flux:select.option value="Gugatan Sederhana">Gugatan Sederhana</flux:select.option>
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="tgl_putusan" type="date" label="Tgl. Putusan" />
                <flux:input wire:model="tgl_penyerahan_berkas" type="date" label="Tgl. Serah Berkas" />
            </div>
        </div>

        <flux:textarea wire:model="isi_gugatan" label="Isi Putusan (Amar)" rows="5" />

        <div class="space-y-4 border-t border-accent-100 pt-6">
            <flux:label class="text-accent-900 font-semibold">Dokumen Terlampir</flux:label>

            <div class="grid grid-cols-1 gap-2">
                @foreach ($existingFiles as $file)
                    <div
                        class="flex items-center justify-between p-3 bg-accent-50/50 border border-accent-100 rounded-lg group">
                        <div class="flex items-center gap-3">
                            <flux:icon name="document-text" class="text-accent-500" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $file->file_name }}</span>
                        </div>
                        <flux:button wire:click="deleteFile({{ $file->id }})" variant="ghost" icon="trash"
                            size="sm" class="text-red-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </div>
                @endforeach
            </div>

            {{-- Bagian Tambah File Baru pada perdata-edit.blade.php --}}
            <div class="space-y-4 border-t border-accent-100 pt-6">
                <flux:label>Tambah Dokumen Baru (Hanya PDF)</flux:label>

                @foreach ($fileInputs as $index => $val)
                    <div class="space-y-3 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200">
                        <div class="flex gap-2 items-center">
                            <input type="file" wire:model="files.{{ $index }}" accept="application/pdf"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 hover:file:bg-zinc-200 cursor-pointer" />

                            @if (count($fileInputs) > 1)
                                <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                    variant="danger" icon="trash" size="sm" />
                            @endif
                        </div>

                        {{-- Preview PDF untuk File Baru --}}
                        @if (isset($files[$index]) && $files[$index] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                            <div class="mt-2">
                                <flux:text size="sm" class="mb-2 font-medium text-indigo-600">Preview Dokumen Baru:
                                </flux:text>
                                <div
                                    class="w-full h-64 border border-zinc-300 rounded-lg overflow-hidden bg-white shadow-inner">
                                    <iframe src="{{ $files[$index]->temporaryUrl() }}#toolbar=0"
                                        class="w-full h-full"></iframe>
                                </div>
                            </div>
                        @endif

                        <flux:error name="files.{{ $index }}" />
                    </div>
                @endforeach

                <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus">
                    Tambah Slot Dokumen
                </flux:button>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-accent-100">
            <flux:button type="submit" variant="primary" class="bg-amber-400 hover:bg-amber-500 text-white">
                Simpan Perubahan
            </flux:button>
            <flux:button href="{{ route('perdata.index') }}" variant="ghost" wire:navigate>
                Batal
            </flux:button>
        </div>
    </form>
</div>
