<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pengaduan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" width="100" alt="Logo SIPAKUM" />
        <h2>REGISTER PENGADUAN</h2>
        <p>Periode: {{ $start ?? '...' }} s/d {{ $end ?? '...' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelapor</th>
                <th>Terlapor</th>
                <th>Jenis</th>
                <th>Sarana</th>
                <th>Tindak Lanjut</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->pelapor }}</td>
                    <td>{{ $item->terlapor }}</td>
                    <td>{{ $item->jenis_pengaduan }}</td>
                    <td>{{ $item->sarana_pengaduan }}</td>
                    <td>{{ $item->tindak_lanjut ?? '-' }}</td>
                    <td class="text-center">{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
