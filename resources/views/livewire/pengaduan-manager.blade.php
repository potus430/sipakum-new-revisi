<div class="p-6 space-y-6">
    <flux:heading size="xl">Manajemen Pengaduan</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <flux:card class="lg:col-span-1">
            <form wire:submit="save" class="space-y-4">
                <flux:input wire:model="judul" label="Judul Pengaduan" />
                <flux:textarea wire:model="isi_pengaduan" label="Isi Pengaduan" />
                <flux:checkbox wire:model="is_anonim" label="Kirim sebagai Anonim" />
                <flux:input wire:model="file_pendukung" type="file" label="Bukti Pendukung" />
                <flux:button type="submit" variant="primary" class="w-full">Kirim Pengaduan</flux:button>
            </form>
        </flux:card>

        <flux:card class="lg:col-span-2">
            <div class="mb-4">
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="Cari judul atau isi pengaduan..." 
            icon="magnifying-glass" 
        />
    </div>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Judul</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($pengaduans as $item)
                        <flux:table.row>
                            <flux:table.cell>{{ $item->judul }}</flux:table.cell>
                            <flux:table.cell>
                                @php $color = ['Terima'=>'zinc', 'Verifikasi'=>'orange', 'Investigasi'=>'blue', 'Selesai'=>'green'][$item->status] @endphp
                                <flux:badge color="{{ $color }}">{{ $item->status }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
        <flux:button wire:click="viewDetail({{ $item->id }})" variant="ghost" size="sm" icon="eye" />
        
        <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" class="text-blue-600" />
        
        <flux:button wire:click="delete({{ $item->id }})" variant="ghost" size="sm" icon="trash" class="text-red-600" />
    </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
            <div class="mt-4">{{ $pengaduans->links() }}</div>
        </flux:card>
    </div>

    <flux:modal name="modal-detail-pengaduan" class="md:w-96 space-y-4">
    <div>
        <flux:heading size="lg">Detail Pengaduan</flux:heading>
        <flux:subheading>Informasi lengkap mengenai laporan yang diajukan.</flux:subheading>
    </div>

    @if($selectedPengaduan)
        <div class="space-y-4">
            <div>
                <flux:label>Judul</flux:label>
                <flux:text>{{ $selectedPengaduan->judul }}</flux:text>
            </div>
            <div>
                <flux:label>Isi Pengaduan</flux:label>
                <flux:text>{{ $selectedPengaduan->isi_pengaduan }}</flux:text>
            </div>
            <div>
                <flux:label>Status</flux:label>
                <flux:badge>{{ $selectedPengaduan->status }}</flux:badge>
            </div>
             @if($selectedPengaduan && $selectedPengaduan->file_pendukung)
    <div>
        <flux:label>Lampiran Bukti</flux:label>
        <flux:button 
            wire:click="downloadFile({{ $selectedPengaduan->id }})" 
            variant="outline" 
            size="sm" 
            icon="arrow-down-tray"
        >
            Unduh File Lampiran
        </flux:button>
    </div>

    <flux:heading size="sm" class="mt-4">Riwayat Perubahan Status</flux:heading>
    <div class="space-y-2 border-t pt-2">
        @foreach($selectedPengaduan->logs()->latest()->get() as $log)
            <div class="text-xs text-zinc-600">
                <span class="font-bold">{{ $log->user->name }}</span> 
                mengubah status: 
                <span class="bg-zinc-100 px-1">{{ $log->status_lama }}</span> → 
                <span class="bg-blue-100 px-1 font-semibold">{{ $log->status_baru }}</span>
                <br>
                <span class="text-[10px] italic">{{ $log->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
@endif
        </div>
    @endif

    
</flux:modal>

{{-- Modal untuk update status pengaduan --}}
<flux:modal name="modal-update-status">
    <form wire:submit="save" class="space-y-4">
        <flux:heading>Update Status Pengaduan</flux:heading>
        
        <flux:select wire:model="status" label="Pilih Status Baru">
            <option value="Terima">Terima</option>
            <option value="Verifikasi">Verifikasi</option>
            <option value="Investigasi">Investigasi</option>
            <option value="Selesai">Selesai</option>
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button wire:click="$flux.modal('modal-update-status').close()" variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
        </div>
    </form>
</flux:modal>
</div>