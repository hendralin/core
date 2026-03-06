<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Stock Wahana OTO</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px 4px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 7px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        .no-wrap {
            white-space: nowrap;
        }
        tr.row-old-stock td {
            background-color: #fecaca !important;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daftar Stock Wahana OTO</h1>
        <p>Generated on {{ now()->format('M d, Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Brand</th>
                <th>Type</th>
                <th>Model</th>
                <th>Year</th>
                <th>Nopol</th>
                <th>Cylinder</th>
                <th>Kilometer</th>
                <th>Warehouse</th>
                <th>Tgl. STNK</th>
                <th>Tgl. Pajak</th>
                <th>Modal</th>
                <th>Harga Tunai</th>
                <th>Harga Kredit</th>
                @if(($statusFilter ?? '') !== '1')
                    <th>Tgl. Penjualan</th>
                    <th>Harga Penjualan</th>
                @endif
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $i => $vehicle)
                <tr class="@if($vehicle->purchase_date && \Carbon\Carbon::parse($vehicle->purchase_date)->diffInMonths(\Carbon\Carbon::now()) > 3) row-old-stock @endif">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $vehicle->brand?->name ?? '-' }}</td>
                    <td>{{ $vehicle->type?->name ?? '-' }}</td>
                    <td>{{ $vehicle->vehicle_model?->name ?? '-' }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td class="no-wrap">{{ $vehicle->police_number }}</td>
                    <td>{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0) . 'cc' : '-' }}</td>
                    <td class="no-wrap">{{ number_format($vehicle->kilometer, 0) }}</td>
                    <td>{{ $vehicle->warehouse?->name ?? '-' }}</td>
                    <td>{{ $vehicle->vehicle_registration_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $vehicle->vehicle_registration_expiry_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('d-m-Y') : '-' }}</td>
                    @php
                        $totalModal = ($vehicle->purchase_price ?? 0) + ($vehicle->costs->where('cost_type', '!=', 'sales_commission')->where('cost_type', '!=', 'purchase_commission')->sum('total_price') ?? 0) + ($vehicle->commissions->where('type', 2)->sum('amount') ?? 0) + ($vehicle->roadside_allowance ?? 0);
                    @endphp
                    <td class="no-wrap">{{ $totalModal > 0 ? number_format($totalModal, 0) : '-' }}</td>
                    <td class="no-wrap">{{ $vehicle->display_price ? number_format($vehicle->display_price, 0) : '-' }}</td>
                    <td class="no-wrap">{{ $vehicle->loan_price ? number_format($vehicle->loan_price, 0) : '-' }}</td>
                    @if(($statusFilter ?? '') !== '1')
                        <td>{{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('d-m-Y') : '-' }}</td>
                        <td class="no-wrap">{{ $vehicle->selling_price ? number_format($vehicle->selling_price, 0) : '-' }}</td>
                    @endif
                    <td>{{ $vehicle->status == 1 ? 'Available' : ($vehicle->status == 2 ? 'Pending' : 'Sold') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated by {{ config('app.name') }} - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>

