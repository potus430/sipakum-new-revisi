<!DOCTYPE html>
<html>

<head>
    <title>Laporan Waarmerking</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }

        .info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" width="100" alt="Logo SIPAKUM" />
        <h1>Laporan Register Waarmerking</h1>
        <p>Sistem Informasi Penatausahaan Hukum (SIPAKUM)</p>
    </div>

    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        @if ($start || $end)
            <strong>Periode:</strong> {{ $start ? \Carbon\Carbon::parse($start)->format('d/m/Y') : 'Awal' }} s/d
            {{ $end ? \Carbon\Carbon::parse($end)->format('d/m/Y') : 'Sekarang' }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">No. Register</th>
                <th width="25%">Nama Pemohon</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->nomor_register }}</td>
                    <td>{{ $item->nama_pemohon }}</td>
                    <td class="text-center">
                        {{ $item->tanggal_legalisasi ? $item->tanggal_legalisasi->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis melalui sistem manajemen dokumen.
    </div>
</body>

</html>
