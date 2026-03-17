<?php

namespace App\Modules\Exports;

use App\Models\Berkas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PidanaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Mengambil data sesuai dengan filter tanggal
     */
    public function collection()
    {
        $query = Berkas::where('modul', 'pidana');

        if (!empty($this->start)) {
            $query->whereDate('tanggal_kejadian', '>=', $this->start);
        }

        if (!empty($this->end)) {
            $query->whereDate('tanggal_kejadian', '<=', $this->end);
        }

        return $query->latest()->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'Nomor Perkara',
            'Terdakwa / Pihak',
            'Jenis Perkara',
            'Pasal yang Disangkakan',
            'Tanggal Putus',
            'Tanggal Penyerahan Berkas',
            'Isi Putusan (Amar)'
        ];
    }

    /**
     * Mapping data dari Model/JSON Metadata ke kolom Excel
     */
    public function map($row): array
    {
        return [
            $row->nomor_registrasi,
            $row->subjek,
            $row->metadata['jenis_perkara'] ?? '-',
            $row->metadata['pasal'] ?? '-',
            $row->tanggal_kejadian ? $row->tanggal_kejadian->format('d-m-Y') : '-',
            $row->metadata['tgl_penyerahan'] ?? '-',
            $row->metadata['isi_putusan'] ?? '-',
        ];
    }
}
