<?php

namespace App\Exports\Modules;

use App\Models\Berkas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PerdataExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;

    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    // Mengambil data sesuai dengan filter yang diberikan
    public function collection()
    {
        $query = Berkas::where('modul', 'perdata');

        if (!empty($this->start)) {
            $query->whereDate('created_at', '>=', $this->start);
        }

        if (!empty($this->end)) {
            $query->whereDate('created_at', '<=', $this->end);
        }

        return $query->latest()->get();
    }

    // Menentukan judul kolom pada file Excel
    public function headings(): array
    {
        return [
            'Nomor Perkara',
            'Penggugat',
            'Tergugat',
            'Jenis Perkara',
            'Pasal / Dasar Hukum',
            'Tanggal Register'
        ];
    }

    // Memetakan data model ke baris Excel
    public function map($row): array
    {
        return [
            $row->nomor_registrasi,
            $row->metadata['penggugat'] ?? '-',
            $row->metadata['tergugat'] ?? '-',
            $row->metadata['jenis_perkara'] ?? '-',
            $row->metadata['pasal'] ?? '-',
            $row->created_at ? $row->created_at->format('d-m-Y') : '-',
        ];
    }
}
