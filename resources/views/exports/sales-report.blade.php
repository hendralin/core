<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Kendaraan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .price {
            text-align: right;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan Kendaraan</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($dateFrom ?? now()->startOfMonth())->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo ?? now()->endOfMonth())->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Tanggal Jual</th>
                <th style="width: 12%;">Kendaraan</th>
                <th style="width: 8%;">Nomor Polisi</th>
                <th style="width: 8%;">Harga Jual</th>
                <th style="width: 8%;">Modal</th>
                <th style="width: 8%;">Keuntungan</th>
                <th style="width: 12%;">Pembeli</th>
                <th style="width: 8%;">Telepon</th>
                <th style="width: 8%;">Salesman</th>
                <th style="width: 8%;">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $index => $vehicle)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($vehicle->brand)
                            {{ $vehicle->brand->name }}
                        @endif
                        @if($vehicle->vehicle_model)
                            {{ $vehicle->vehicle_model->name }}
                        @endif
                        @if($vehicle->year)
                            ({{ $vehicle->year }})
                        @endif
                    </td>
                    <td>{{ $vehicle->police_number ?? '-' }}</td>
                    <td class="price">Rp {{ number_format($vehicle->selling_price ?? 0, 0) }}</td>
                    <td class="price">
                        @php
                            $purchasePrice = $vehicle->purchase_price ?? 0;
                            $totalCosts = $vehicle->costs->sum('total_price');
                            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
                            $totalModal = $purchasePrice + $totalCosts + $purchaseCommissions;
                        @endphp
                        Rp {{ number_format($totalModal, 0) }}
                    </td>
                    <td class="price @if(($vehicle->selling_price ?? 0) - $totalModal >= 0) text-green-600 @else text-red-600 @endif">
                        @php
                            $profit = ($vehicle->selling_price ?? 0) - $totalModal;
                        @endphp
                        Rp {{ number_format($profit, 0) }}
                    </td>
                    <td>{{ $vehicle->buyer_name ?? '-' }}</td>
                    <td>{{ $vehicle->buyer_phone ?? '-' }}</td>
                    <td>{{ $vehicle->salesman ? $vehicle->salesman->name : '-' }}</td>
                    <td>{{ $vehicle->payment_type == 1 ? 'Cash' : 'Kredit' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data penjualan kendaraan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($vehicles->count() > 0)
        <div class="summary">
            <h3>Ringkasan Penjualan</h3>
            <div class="summary-row">
                <span>Total Kendaraan Terjual:</span>
                <strong>{{ $stats['total_vehicles'] }} unit</strong>
            </div>
            <div class="summary-row">
                <span>Total Nilai Penjualan:</span>
                <strong>Rp {{ number_format($stats['total_sales'], 0) }}</strong>
            </div>
            <div class="summary-row">
                <span>Total Keuntungan:</span>
                <strong>Rp {{ number_format($stats['total_profit'], 0) }}</strong>
            </div>
            <div class="summary-row">
                <span>Margin Keuntungan:</span>
                <strong>{{ number_format($stats['profit_margin'], 1) }}%</strong>
            </div>
        </div>
    @endif
</body>
</html>
