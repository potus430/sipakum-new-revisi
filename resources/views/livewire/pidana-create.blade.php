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

        {{-- Ganti bagian loop Dokumen Pendukung dengan kode ini --}}
        <div class="space-y-4 border-t border-accent-100 pt-6">
            <flux:label>Dokumen Pendukung (Hanya PDF)</flux:label>

            @foreach ($fileInputs as $index => $value)
                <div
                    class="space-y-2 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="flex gap-2 items-center">
                        <input type="file" wire:model="files.{{ $index }}" accept="application/pdf"
                            class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 hover:file:bg-zinc-200 cursor-pointer" />

                        @if (count($fileInputs) > 1)
                            <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                variant="danger" icon="trash" size="sm" />
                        @endif
                    </div>

                    {{-- Fitur Preview PDF --}}
                    @if (isset($files[$index]) && $files[$index] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <flux:text size="sm" class="font-medium">Preview Dokumen {{ $index + 1 }}:
                                </flux:text>
                                <flux:button size="xs" variant="subtle" icon="magnifying-glass-plus"
                                    wire:click="$dispatch('open-modal', { name: 'preview-pdf-{{ $index }}' })">
                                    Perbesar
                                </flux:button>
                            </div>

                            <div class="w-full h-64 border border-zinc-300 rounded-lg overflow-hidden bg-white">
                                <iframe src="{{ $files[$index]->temporaryUrl() }}#toolbar=0"
                                    class="w-full h-full"></iframe>
                            </div>
                        </div>
                    @endif

                    <flux:error name="files.{{ $index }}" />
                </div>
            @endforeach

            <flux:button type="button" wire:click="addFileInput" variant="subtle" size="sm" icon="plus">
                Tambah Dokumen Lagi
            </flux:button>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <flux:button type="submit" variant="primary">Simpan Berkas</flux:button>
            <flux:button href="{{ route('pidana.index') }}" variant="ghost" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
