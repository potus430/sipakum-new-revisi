<!DOCTYPE html>
<html>

<head>
    <title>Laporan Surat Kuasa</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 8px;
            vertical-align: top;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
        }

        .filter-info {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" width="100" alt="Logo SIPAKUM" />
        <div class="title">Laporan Data Surat Kuasa</div>
        <div>Dicetak pada: {{ now()->format('d-m-Y H:i') }}</div>
    </div>

    <div class="filter-info">
        @if ($start || $end)
            <strong>Periode:</strong> {{ $start ?: '...' }} s/d {{ $end ?: '...' }} <br>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kategori</th>
                <th width="20%">Nomor Surat</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Pemberi Kuasa</th>
                <th width="20%">Penerima Kuasa</th>
                <th width="10%">Jenis</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kategori_perkara }}</td>
                    <td>{{ $item->nomor_surat_kuasa }}</td>
                    <td>{{ $item->tanggal_surat ? $item->tanggal_surat->format('d-m-Y') : '-' }}</td>
                    <td>{{ $item->pemberi_kuasa }}</td>
                    <td>{{ $item->penerima_kuasa }}</td>
                    <td>{{ $item->jenis_kuasa }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem SIPAKUM.
    </div>
</body>

</html>
