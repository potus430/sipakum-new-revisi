<div class="p-8 max-w-4xl mx-auto">
    <flux:heading size="xl" class="mb-6">Edit Register Perdata</flux:heading>

    <form wire:submit="update"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required />
            <flux:input wire:model="tgl_register" type="date" label="Tanggal Register" />
            <flux:input wire:model="penggugat" label="Nama Penggugat" required />
            <flux:input wire:model="tergugat" label="Nama Tergugat" required />
            <flux:select wire:model="jenis_perkara" label="Jenis Perkara">
                <option value="Gugatan">Gugatan</option>
                <option value="Permohonan">Permohonan</option>
            </flux:select>
            <flux:input wire:model="pasal" label="Pasal / Dasar Hukum" />
        </div>
        <flux:textarea wire:model="isi_gugatan" label="Isi Gugatan / Permohonan" />

        <div class="space-y-4 border-t pt-6">
            <div>
                <flux:label>Dokumen Saat Ini</flux:label>
                @foreach ($oldFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 border rounded-lg mt-2">
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                            class="text-sm text-blue-600 underline">
                            {{ $file->file_name }}
                        </a>
                        <flux:button type="button" wire:click="deleteOldFile({{ $file->id }})" variant="danger"
                            icon="trash" size="sm" wire:confirm="Yakin ingin menghapus?" />
                    </div>
                @endforeach
            </div>

            <div>
                <flux:label>Tambah Dokumen Baru</flux:label>
                @foreach ($fileInputs as $index => $val)
                    <div class="flex flex-col gap-1 mt-2">
                        <div class="flex gap-2">
                            <input type="file" wire:model="files.{{ $index }}"
                                class="block w-full text-sm border p-2 rounded" />
                            @if (count($fileInputs) > 1)
                                <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                    variant="danger" icon="trash" />
                            @endif
                        </div>
                        @error("files.$index")
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
                <flux:button type="button" wire:click="addFileInput" variant="subtle" class="mt-2" icon="plus">
                    Tambah Input File</flux:button>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            <flux:button href="{{ route('perdata.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
