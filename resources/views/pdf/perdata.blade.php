<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; }
        td { padding: 10px; border: 1px solid #ddd; }
        .footer { margin-top: 50px; width: 100%; }
        .signature { float: right; width: 200px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" width="100" alt="Logo SIPAKUM" />
        <div class="title">Laporan Perkara Perdata</div>
        <div>Periode: {{ $start }} s/d {{ $end }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Perkara</th>
                <th>Penggugat</th>
                <th>Tergugat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->nomor_registrasi }}</td>
                <td>{{ $item->metadata['penggugat'] ?? '-' }}</td>
                <td>{{ $item->metadata['tergugat'] ?? '-' }}</td>
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