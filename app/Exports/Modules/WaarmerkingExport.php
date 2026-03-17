<?php

namespace App\Exports\Modules;

use App\Models\Waarmerking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WaarmerkingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;
    protected $search;

    public function __construct($start, $end, $search)
    {
        $this->start = $start;
        $this->end = $end;
        $this->search = $search;
    }

    /**
     * Mengambil data berdasarkan filter yang aktif di UI
     */
    public function collection()
    {
        return Waarmerking::query()
            ->when($this->search, function ($q) {
                $q->where('nomor_register', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_pemohon', 'like', '%' . $this->search . '%');
            })
            ->when($this->start, function ($q) {
                $q->whereDate('tanggal_legalisasi', '>=', $this->start);
            })
            ->when($this->end, function ($q) {
                $q->whereDate('tanggal_legalisasi', '<=', $this->end);
            })
            ->latest()
            ->get();
    }

    /**
     * Judul kolom di file Excel
     */
    public function headings(): array
    {
        return [
            'Nomor Register',
            'Nama Pemohon',
            'Tanggal Legalisasi',
            'Catatan',
            'Petugas Input'
        ];
    }

    /**
     * Memetakan data dari database ke kolom Excel
     */
    public function map($row): array
    {
        return [
            $row->nomor_register,
            $row->nama_pemohon,
            $row->tanggal_legalisasi ? $row->tanggal_legalisasi->format('d-m-Y') : '-',
            $row->catatan ?? '-',
            $row->user?->name ?? 'Sistem', // Mengambil nama petugas dari relasi user
        ];
    }
}
