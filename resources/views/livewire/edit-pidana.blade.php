<div class="p-8 max-w-4xl mx-auto">
    <flux:heading size="xl" class="mb-6">Edit Register Pidana</flux:heading>

    <form wire:submit="update"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required />
            <flux:input wire:model="pihak" label="Terdakwa / Pihak" required />

            <flux:select wire:model="jenis" label="Jenis Perkara">
                <option value="PID.B">PID.B (Biasa)</option>
                <option value="PID.SUS">PID.SUS (Khusus)</option>
                <option value="ANAK">ANAK</option>
                <option value="PRAPERADILAN">PRAPERADILAN</option>
            </flux:select>

            <flux:input wire:model="tgl_putus" type="date" label="Tanggal Putus" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="pasal" label="Pasal yang Disangkakan" />
            <flux:input wire:model="tgl_penyerahan" type="date" label="Tanggal Penyerahan Berkas" />
        </div>

        <div class="space-y-4 border-t border-zinc-200 dark:border-zinc-700 pt-6">

            <div>
                <flux:label>Dokumen Saat Ini</flux:label>
                @foreach ($oldFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 border rounded-lg mt-2">
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                            class="text-sm text-blue-600 underline">
                            {{ $file->file_name }}
                        </a>

                        {{-- Tombol Hapus Dokumen Lama --}}
                        <flux:button type="button" wire:click="deleteOldFile({{ $file->id }})" variant="danger"
                            icon="trash" size="sm" wire:confirm="Yakin ingin menghapus dokumen ini?" />
                    </div>
                @endforeach
            </div>

            <div>
                <flux:label>Tambah Dokumen Baru</flux:label>
                @foreach ($fileInputs as $index => $val)
                    <div class="flex gap-2 mt-2">
                        <input type="file" wire:model="files.{{ $index }}"
                            class="block w-full text-sm border p-2 rounded" />
                        @if (count($fileInputs) > 1)
                            <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                variant="danger" icon="trash" />
                        @endif
                    </div>
                    <flux:error name="files" />
                    <flux:error name="files.*" />
                @endforeach

                <flux:button type="button" wire:click="addFileInput" variant="subtle" class="mt-2" icon="plus">
                    Tambah Input File
                </flux:button>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            <flux:button href="{{ route('pidana.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
