<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Berkas;
use App\Models\Pengaduan;
use App\Models\Gratifikasi;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanSIPAKUMExport implements FromCollection, WithHeadings
{
    protected $start, $end;

    public function __construct($start, $end) {
        $this->start = $start;
        $this->end = $end;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Menggabungkan ringkasan data per modul berdasarkan rentang tanggal
        return collect([
            ['Perkara Pidana', Berkas::where('modul', 'pidana')->whereBetween('created_at', [$this->start, $this->end])->count()],
            ['Perkara Perdata', Berkas::where('modul', 'perdata')->whereBetween('created_at', [$this->start, $this->end])->count()],
            ['Pengaduan', Pengaduan::whereBetween('created_at', [$this->start, $this->end])->count()],
            ['Gratifikasi', Gratifikasi::whereBetween('created_at', [$this->start, $this->end])->count()],
        ]);
    }

    public function headings(): array { return ['Nama Modul', 'Jumlah']; }
}
