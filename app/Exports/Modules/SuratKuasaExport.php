<?php

namespace App\Exports\Modules;

use App\Models\SuratKuasa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuratKuasaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;
    protected $kategori;

    public function __construct($start, $end, $kategori)
    {
        $this->start = $start;
        $this->end = $end;
        $this->kategori = $kategori;
    }

    /**
     * Mengambil koleksi data berdasarkan filter
     */
    public function collection()
    {
        $query = SuratKuasa::query();

        if (!empty($this->kategori)) {
            $query->where('kategori_perkara', $this->kategori);
        }

        if (!empty($this->start)) {
            $query->whereDate('tanggal_surat', '>=', $this->start);
        }

        if (!empty($this->end)) {
            $query->whereDate('tanggal_surat', '<=', $this->end);
        }

        return $query->latest()->get();
    }

    /**
     * Menentukan Header Kolom Excel
     */
    public function headings(): array
    {
        return [
            'Kategori Perkara',
            'Nomor Surat Kuasa',
            'Tanggal Surat',
            'Pemberi Kuasa',
            'Penerima Kuasa',
            'Jenis Kuasa'
        ];
    }

    /**
     * Memetakan data model ke baris Excel
     */
    public function map($row): array
    {
        return [
            $row->kategori_perkara,
            $row->nomor_surat_kuasa,
            $row->tanggal_surat ? $row->tanggal_surat->format('d-m-Y') : '-',
            $row->pemberi_kuasa,
            $row->penerima_kuasa,
            $row->jenis_kuasa,
        ];
    }
}
