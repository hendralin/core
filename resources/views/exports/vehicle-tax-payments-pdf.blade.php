<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembayaran PKB Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .no-wrap {
            white-space: nowrap;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pembayaran PKB</h1>
        <p>Generated on {{ now()->format('d F Y H:i') }}</p>
        <p>Total Records: {{ $costs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Kendaraan</th>
                <th>Warehouse</th>
                <th>Deskripsi</th>
                <th class="text-right">Total Pembayaran</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($costs as $i => $cost)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="no-wrap">{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                    <td class="no-wrap">{{ $cost->vehicle?->police_number ?? '-' }}</td>
                    <td>{{ $cost->warehouse?->name ?? '-' }}</td>
                    <td style="max-width: 300px; word-wrap: break-word;">{{ Str::limit($cost->description, 100) }}</td>
                    <td class="text-right no-wrap"><strong>{{ 'Rp ' . number_format($cost->total_price, 0) }}</strong></td>
                    <td>{{ $cost->createdBy?->name ?? '-' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right no-wrap"><strong>{{ 'Rp ' . number_format($costs->sum('total_price'), 0) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the system on {{ now()->format('d F Y \a\t H:i') }}</p>
    </div>
</body>
</html>

