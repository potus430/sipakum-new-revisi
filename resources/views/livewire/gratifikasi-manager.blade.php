<div class="p-6 space-y-6">
    <flux:heading size="xl">Manajemen Laporan Gratifikasi</flux:heading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <flux:card class="lg:col-span-1 space-y-4">
            <form wire:submit="save">
                <flux:input wire:model="pelapor" label="Nama Pelapor" />
                <flux:input wire:model="pemberi" label="Nama Pemberi" />
                <flux:select wire:model="bentuk_gratifikasi" label="Bentuk Gratifikasi">
                    <option value="Uang">Uang</option>
                    <option value="Barang">Barang</option>
                    <option value="Fasilitas">Fasilitas</option>
                </flux:select>
                <flux:input wire:model="tanggal_penerimaan" type="date" label="Tanggal Penerimaan" />
                <flux:textarea wire:model="kronologi" label="Kronologi" />
                @if($isEditing)
                    <flux:select wire:model="status" label="Status Penanganan">
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </flux:select>
                @endif
                <flux:button type="submit" variant="primary" class="mt-4 w-full">Simpan Laporan</flux:button>
            </form>
        </flux:card>

        <flux:card class="lg:col-span-2">
            <flux:input wire:model.live="search" placeholder="Cari pelapor..." class="mb-4" />
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Pelapor</flux:table.column>
                    <flux:table.column>Bentuk</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($gratifikasis as $item)
                        <flux:table.row>
                            <flux:table.cell>{{ $item->pelapor }}</flux:table.cell>
                            <flux:table.cell>{{ $item->bentuk_gratifikasi }}</flux:table.cell>
                            <flux:table.cell>
                                @php $color = ['Pending'=>'orange', 'Diproses'=>'blue', 'Selesai'=>'green'][$item->status] ?? 'zinc'; @endphp
                                <flux:badge color="{{ $color }}">{{ $item->status }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button wire:click="edit({{ $item->id }})" variant="ghost" icon="pencil" />

                                <flux:button 
            wire:click="delete({{ $item->id }})" 
            wire:confirm="Apakah Anda yakin ingin menghapus laporan ini? Data yang dihapus tidak dapat dikembalikan."
            variant="ghost" 
            size="sm" 
            icon="trash" 
            class="text-red-600 hover:text-red-800" 
        />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>