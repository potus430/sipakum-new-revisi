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
            <flux:label class="text-accent-900 font-semibold">Dokumen Terlampir</flux:label>

            <div class="grid grid-cols-1 gap-2">
                @foreach ($existingFiles as $file)
                    <div
                        class="flex items-center justify-between p-3 bg-accent-50/50 border border-accent-100 rounded-lg group">
                        <div class="flex items-center gap-3">
                            <flux:icon name="document-text" class="text-accent-500" />
                            <span class="text-sm text-zinc-700">{{ $file->file_name }}</span>
                        </div>
                        <flux:button wire:click="deleteFile({{ $file->id }})" variant="ghost" icon="trash"
                            size="sm" class="text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"
                            wire:confirm="Yakin ingin menghapus dokumen ini?" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <flux:label>Tambah Dokumen Baru</flux:label>
                @foreach ($fileInputs as $index => $val)
                    <div class="flex flex-col gap-2 mt-2">
                        <div class="flex gap-2">
                            <input type="file" wire:model="files.{{ $index }}"
                                class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                            @if (count($fileInputs) > 1)
                                <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                    variant="danger" icon="trash" size="sm" />
                            @endif
                        </div>
                    </div>
                @endforeach
                <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus"
                    class="mt-2 text-primary-600">
                    Tambah Input File
                </flux:button>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-accent-100">
            <flux:button type="submit" variant="primary" class="bg-amber-400 hover:bg-amber-500 text-white">
                Simpan Perubahan
            </flux:button>
            <flux:button href="{{ route('pidana.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
