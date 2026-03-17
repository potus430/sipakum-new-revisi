<!DOCTYPE html>
<html>

<head>
    <title>Register Pidana</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" width="100" alt="Logo SIPAKUM" />
        <h2>LAPORAN REGISTER PERKARA PIDANA</h2>
        <div>
            @if ($start && $end)
                <p>Periode: {{ $start }} s/d {{ $end }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Perkara</th>
                <th>Terdakwa</th>
                <th>Jenis</th>
                <th>Tgl Putus</th>
                <th>Isi Putusan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->nomor_registrasi }}</td>
                    <td>{{ $item->subjek }}</td>
                    <td>{{ $item->metadata['jenis_perkara'] ?? '-' }}</td>
                    <td>{{ $item->tanggal_kejadian ? $item->tanggal_kejadian->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->metadata['isi_putusan'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ date('d-m-Y') }}</p>
            <br><br><br>
            <p><strong>Admin SIPAKUM</strong></p>
        </div>
    </div>
</body>

</html>
