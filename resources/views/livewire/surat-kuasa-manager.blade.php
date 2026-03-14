<div class="p-6 space-y-8">
    @if (session()->has('success'))
    <div class="mb-4 p-2 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg">
        {{ session('success') }}
    </div>
@endif
    <flux:heading size="xl">Manajemen Surat Kuasa</flux:heading>
    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <flux:card class="lg:col-span-1">
            <form wire:submit="save" class="space-y-4">
                <flux:select wire:model="berkas_id" label="Pilih Perkara" placeholder="Pilih Nomor Registrasi...">
    <option value="">-- Pilih Perkara --</option>
    @foreach($daftarBerkas as $berkas)
        <option value="{{ $berkas->id }}">{{ $berkas->nomor_registrasi }}</option>
    @endforeach
</flux:select>

                <flux:input wire:model="nomor_surat" label="Nomor Surat Kuasa" />
                <flux:input wire:model="tanggal_surat" type="date" label="Tanggal Surat" />
                <flux:input wire:model="pemberi" label="Pemberi Kuasa" placeholder="Nama Klien" />
                <flux:input wire:model="penerima" label="Penerima Kuasa" placeholder="Nama Advokat" />
                
                <flux:select wire:model="jenis" label="Jenis Kuasa">
    <option value="Khusus">Khusus</option>
    <option value="Substitusi">Substitusi</option>
    <option value="Umum">Umum</option>
</flux:select>

                <flux:input wire:model="file_kuasa" type="file" label="Upload Scan (PDF)" />

                @if($isEditing)
    <flux:button wire:click="resetForm" variant="ghost">Batal</flux:button>
@endif
<flux:button type="submit" variant="primary">
    {{ $isEditing ? 'Update Surat Kuasa' : 'Simpan Surat Kuasa' }}
</flux:button>
            </form>
        </flux:card>

        <flux:card class="lg:col-span-2">
            <div class="mb-4">
    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nomor surat atau pihak..." icon="magnifying-glass" />
</div>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nomor Surat</flux:table.column>
                    <flux:table.column>Perkara</flux:table.column>
                    <flux:table.column>Pemberi/Penerima</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($daftarKuasa as $kuasa)
                        <flux:table.row>
                            <flux:table.cell>{{ $kuasa->nomor_surat_kuasa }}</flux:table.cell>
                            <flux:table.cell>{{ $kuasa->berkas->nomor_registrasi }}</flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs italic">{{ $kuasa->pemberi_kuasa }}</span> → 
                                <span class="text-xs font-bold">{{ $kuasa->penerima_kuasa }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                               <div class="flex gap-1">
        @if($kuasa->file_path)
            <flux:button href="{{ asset('storage/'.$kuasa->file_path) }}" target="_blank" variant="ghost" size="sm" icon="arrow-down-tray" />
        @endif
       <flux:button wire:click="viewDetail({{ $kuasa->id }})" variant="ghost" size="sm" icon="eye" />                         
        <flux:button wire:click="edit({{ $kuasa->id }})" variant="ghost" size="sm" icon="pencil" />

        <flux:button wire:click="delete({{ $kuasa->id }})" wire:confirm="Yakin ingin menghapus surat kuasa ini?" variant="ghost" size="sm" icon="trash" class="text-red-600" />
    </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>


    <flux:modal name="modal-detail" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Detail Surat Kuasa</flux:heading>
            <flux:subheading>Informasi lengkap surat kuasa</flux:subheading>
        </div>

        @if($detail)
        <div class="space-y-3">
            <flux:field>
                <flux:label>Nomor Surat</flux:label>
                <flux:text>{{ $detail->nomor_surat_kuasa }}</flux:text>
            </flux:field>
            <flux:field>
                <flux:label>Perkara</flux:label>
                <flux:text>{{ $detail->berkas->nomor_registrasi }}</flux:text>
            </flux:field>
            <flux:field>
                <flux:label>Pemberi Kuasa</flux:label>
                <flux:text>{{ $detail->pemberi_kuasa }}</flux:text>
            </flux:field>
            <flux:field>
                <flux:label>Penerima Kuasa</flux:label>
                <flux:text>{{ $detail->penerima_kuasa }}</flux:text>
            </flux:field>
            <flux:field>
                <flux:label>Jenis Kuasa</flux:label>
                <flux:badge>{{ $detail->jenis_kuasa }}</flux:badge>
            </flux:field>
        </div>
        @endif

        <div class="flex justify-end gap-2">
            
            @if($detail && $detail->file_path)
               {{-- <flux:button href="{{ asset('storage/'.$detail->file_path) }}" target="_blank" variant="primary">Unduh PDF</flux:button> --}}

<flux:button href="{{ asset('storage/'.$detail->file_path) }}" target="_blank" variant="primary" icon="arrow-down-tray">
    Unduh PDF
</flux:button>
            @endif
        </div>
    </div>
</flux:modal>

</div>

