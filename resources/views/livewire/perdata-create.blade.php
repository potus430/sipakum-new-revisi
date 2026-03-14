<div class="p-8 max-w-4xl mx-auto">
    <flux:heading size="xl" class="mb-6">Tambah Register Perdata</flux:heading>

    <form wire:submit="store"
        class="space-y-6 bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="no_perkara" label="Nomor Perkara" required />
            <flux:input wire:model="tgl_register" type="date" label="Tanggal Register" />
            <flux:input wire:model="penggugat" label="Nama Penggugat" required />
            <flux:input wire:model="tergugat" label="Nama Tergugat" required />
            <flux:select wire:model="jenis_perkara" label="Jenis Perkara" placeholder="Pilih Jenis...">
                <flux:select.option value="Gugatan">Gugatan</flux:select.option>
                <flux:select.option value="Permohonan">Permohonan</flux:select.option>
                <flux:select.option value="Gugatan Sederhana">Gugatan Sederhana</flux:select.option>
            </flux:select>
            <flux:input wire:model="pasal" label="Pasal / Dasar Hukum" />
        </div>

        <flux:textarea wire:model="isi_gugatan" label="Isi Gugatan / Permohonan" />

        <div class="space-y-4">
            <flux:label>Dokumen Perkara</flux:label>

            @foreach ($fileInputs as $index => $value)
                <div class="flex gap-2 items-center">
                    <input type="file" wire:model="files.{{ $index }}"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 hover:file:bg-zinc-200 cursor-pointer" />

                    @if (count($fileInputs) > 1)
                        <flux:button type="button" wire:click="removeFileInput({{ $index }})" variant="danger"
                            icon="trash" size="sm" />
                    @endif
                    <flux:error name="files.*" />
                    <flux:error name="files" />
                </div>
            @endforeach

            <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus">
                Tambah Dokumen Lagi
            </flux:button>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <flux:button type="submit" variant="primary">Simpan Perkara</flux:button>
            <flux:button href="{{ route('perdata.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
