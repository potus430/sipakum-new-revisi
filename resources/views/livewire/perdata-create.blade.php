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

        <flux:textarea wire:model="isi_gugatan" label="Isi Putusan">Isi Putusan</flux:textarea>

        <div class="space-y-4">
            <flux:label>Dokumen Perkara</flux:label>
            @foreach ($fileInputs as $index => $value)
                <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50 space-y-3">
                    <div class="flex gap-2 items-center">
                        <input type="file" wire:model="files.{{ $index }}"
                            class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 hover:file:bg-zinc-200 cursor-pointer" />

                        @if (count($fileInputs) > 1)
                            <flux:button type="button" wire:click="removeFileInput({{ $index }})"
                                variant="danger" icon="trash" size="sm" />
                        @endif
                    </div>

                    @if (isset($files[$index]))
                        <div class="flex items-center gap-3">
                            <div class="text-sm text-zinc-600 truncate">
                                File: <strong>{{ $files[$index]->getClientOriginalName() }}</strong>
                            </div>

                            @if ($files[$index]->getClientOriginalExtension() === 'pdf')
                                <flux:button type="button" size="sm" icon="eye" x-data
                                    x-on:click="$flux.modal('modal-pdf-{{ $index }}').show()">
                                    Lihat Dokumen
                                </flux:button>

                                <flux:modal name="modal-pdf-{{ $index }}" class="w-full max-w-4xl">
                                    <flux:heading>Preview: {{ $files[$index]->getClientOriginalName() }}</flux:heading>

                                    <div class="h-[60vh] mt-4 border rounded-lg overflow-hidden bg-zinc-100">
                                        <object data="{{ $files[$index]->temporaryUrl() }}" type="application/pdf"
                                            width="100%" height="100%">

                                            <p class="p-10 text-center">
                                                Browser tidak dapat memuat PDF.
                                                <a href="{{ $files[$index]->temporaryUrl() }}" target="_blank"
                                                    class="text-blue-600 underline">
                                                    Klik di sini untuk membuka dokumen di tab baru.
                                                </a>
                                            </p>
                                        </object>
                                    </div>
                                </flux:modal>
                                @elseif (in_array($files[$index]->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ $files[$index]->temporaryUrl() }}" class="h-16 w-16 object-cover rounded border">
                            @endif
                        </div>
                    @endif
                    <flux:error name="files.{{ $index }}" />
                </div>
            @endforeach

            {{-- @foreach ($fileInputs as $index => $value)
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
            @endforeach --}}

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
