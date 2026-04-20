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
            border-collapse: collapse;
            width: 100%;
            max-width: 480px;
        }
        .summary th,
        .summary td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary thead th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 14px;
        }
        .summary td:first-child {
            width: 55%;
        }
        .summary .text-right {
            text-align: right;
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
                <th>No</th>
                <th>Tanggal Jual</th>
                <th>Kendaraan</th>
                <th>Nomor Polisi</th>
                <th>Harga Jual</th>
                <th>Modal</th>
                <th>Keuntungan</th>
                <th>Pembeli</th>
                <th>Telepon</th>
                <th>Salesman</th>
                <th>Pembayaran</th>
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
                        @if($vehicle->type)
                            {{ $vehicle->type->name }}
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
                            $totalCosts = $vehicle->costs->where('cost_type', '!=', 'sales_commission')->where('cost_type', '!=', 'purchase_commission')->sum('total_price');
                            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
                            $roadsideAllowance = $vehicle->roadside_allowance ?? 0;
                            $totalModal = $purchasePrice + $totalCosts + $purchaseCommissions + $roadsideAllowance;
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
        {{-- Table agar ringkasan ter-import ke Excel (Html reader tidak menulis div biasa ke sel) --}}
        <table class="summary">
            <thead>
                <tr>
                    <th colspan="2">Ringkasan Penjualan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Kendaraan Terjual:</td>
                    <td></td>
                    <td class="text-right"><strong>{{ $stats['total_vehicles'] }} unit</strong></td>
                </tr>
                <tr>
                    <td>Total Nilai Penjualan:</td>
                    <td></td>
                    <td class="text-right"><strong>Rp {{ number_format($stats['total_sales'], 0) }}</strong></td>
                </tr>
                <tr>
                    <td>Total Keuntungan:</td>
                    <td></td>
                    <td class="text-right"><strong>Rp {{ number_format($stats['total_profit'], 0) }}</strong></td>
                </tr>
                <tr>
                    <td>Margin Keuntungan:</td>
                    <td></td>
                    <td class="text-right"><strong>{{ number_format($stats['profit_margin'], 1) }}%</strong></td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
