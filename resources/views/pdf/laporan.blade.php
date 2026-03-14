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
        <div class="title">Laporan Ringkasan Sistem SIPAKUM</div>
        <div>Periode: {{ $startDate }} s/d {{ $endDate }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Modul</th>
                <th>Jumlah Perkara/Laporan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Perkara Pidana</td>
                <td>{{ $data['pidana'] }}</td>
            </tr>
            <tr>
                <td>Perkara Perdata</td>
                <td>{{ $data['perdata'] }}</td>
            </tr>
            <tr>
                <td>Pengaduan</td>
                <td>{{ $data['pengaduan'] }}</td>
            </tr>
            <tr>
                <td>Gratifikasi</td>
                <td>{{ $data['gratifikasi'] }}</td>
            </tr>
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