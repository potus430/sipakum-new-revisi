<?php

namespace App\Exports\Modules;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengaduanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start, $end, $search;

    public function __construct($start, $end, $search)
    {
        $this->start = $start;
        $this->end = $end;
        $this->search = $search;
    }

    public function collection()
    {
        return Pengaduan::query()
            ->when($this->search, function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('pelapor', 'like', '%' . $this->search . '%')
                    ->orWhere('terlapor', 'like', '%' . $this->search . '%');
            })
            ->when($this->start, function ($q) {
                $q->whereDate('created_at', '>=', $this->start);
            })
            ->when($this->end, function ($q) {
                $q->whereDate('created_at', '<=', $this->end);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Laporan',
            'Judul Pengaduan',
            'Nama Pelapor',
            'Nama Terlapor',
            'Jenis',
            'Sarana',
            'Status',
            'Tindak Lanjut',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y'),
            $row->judul,
            $row->pelapor,
            $row->terlapor,
            $row->jenis_pengaduan,
            $row->sarana_pengaduan,
            $row->status,
            $row->tindak_lanjut ?? '-',
            $row->keterangan ?? '-',
        ];
    }
}
