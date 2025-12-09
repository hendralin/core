<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Kendaraan - PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8px;
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
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .summary h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .vehicle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }
        .vehicle-card {
            border: 1px solid #ddd;
            padding: 8px;
            background-color: #fff;
        }
        .vehicle-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }
        .vehicle-title {
            font-size: 9px;
            font-weight: bold;
        }
        .vehicle-price {
            font-size: 10px;
            font-weight: bold;
            color: #28a745;
            text-align: right;
        }
        .vehicle-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 7px;
        }
        .detail-label {
            color: #666;
        }
        .detail-value {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan Kendaraan</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($dateFrom ?? now()->startOfMonth())->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo ?? now()->endOfMonth())->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <!-- Summary Stats -->
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

    <!-- Vehicle Cards Grid -->
    @if($vehicles->count() > 0)
        <div class="vehicle-grid">
            @foreach($vehicles as $vehicle)
                <div class="vehicle-card">
                    <div class="vehicle-header">
                        <div class="vehicle-title">
                            {{ $vehicle->police_number ?? 'N/A' }}
                        </div>
                        <div class="vehicle-price">
                            Rp {{ number_format($vehicle->selling_price ?? 0, 0) }}
                        </div>
                    </div>

                    <div class="vehicle-detail">
                        <span class="detail-label">Kendaraan:</span>
                        <span class="detail-value">
                            @if($vehicle->brand) {{ $vehicle->brand->name }} @endif
                            @if($vehicle->vehicle_model) {{ $vehicle->vehicle_model->name }} @endif
                            @if($vehicle->year) ({{ $vehicle->year }}) @endif
                        </span>
                    </div>

                    <div class="vehicle-detail">
                        <span class="detail-label">Tanggal Jual:</span>
                        <span class="detail-value">
                            {{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('d/m/Y') : '-' }}
                        </span>
                    </div>

                    @if($vehicle->buyer_name)
                        <div class="vehicle-detail">
                            <span class="detail-label">Pembeli:</span>
                            <span class="detail-value">{{ $vehicle->buyer_name }}</span>
                        </div>
                        @if($vehicle->buyer_phone)
                            <div class="vehicle-detail">
                                <span class="detail-label">Telepon:</span>
                                <span class="detail-value">{{ $vehicle->buyer_phone }}</span>
                            </div>
                        @endif
                    @endif

                    @if($vehicle->salesman)
                        <div class="vehicle-detail">
                            <span class="detail-label">Salesman:</span>
                            <span class="detail-value">{{ $vehicle->salesman->name }}</span>
                        </div>
                    @endif

                    @if($vehicle->payment_type)
                        <div class="vehicle-detail">
                            <span class="detail-label">Pembayaran:</span>
                            <span class="detail-value">{{ $vehicle->payment_type == 1 ? 'Cash' : 'Kredit' }}</span>
                        </div>
                    @endif

                    <!-- Cost Information -->
                    <div style="margin-top: 8px; padding-top: 5px; border-top: 1px solid #eee; font-size: 6px;">
                        @php
                            $purchasePrice = $vehicle->purchase_price ?? 0;
                            $totalCosts = $vehicle->costs->sum('total_price');
                            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
                            $totalModal = $purchasePrice + $totalCosts + $purchaseCommissions;
                            $profit = ($vehicle->selling_price ?? 0) - $totalModal;
                        @endphp
                        <div style="margin-bottom: 3px;">
                            <span style="color: #666;">Modal:</span>
                            <span style="font-weight: bold;">Rp {{ number_format($totalModal, 0) }}</span>
                        </div>
                        <div>
                            <span style="color: #666;">Keuntungan:</span>
                            <span style="font-weight: bold; @if($profit >= 0) color: #28a745; @else color: #dc3545; @endif">
                                Rp {{ number_format($profit, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data penjualan kendaraan dalam periode ini.</p>
        </div>
    @endif
</body>
</html>
