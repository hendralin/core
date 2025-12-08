<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Report Export</title>
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
        .total-row {
            background-color: #e3f2fd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kas</h1>
        <p>Generated on {{ now()->format('d F Y H:i') }}</p>
        <p>Total Records: {{ $costs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Date</th>
                <th>Description</th>
                <th class="text-right">Debet</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance Row -->
            <tr style="background-color: #eff6ff;">
                <td class="text-center" style="font-weight: bold; color: #1e40af;">-</td>
                <td colspan="4" style="font-weight: bold; color: #1e40af;">Opening Balance</td>
                <td class="text-right no-wrap" style="font-weight: bold; color: @if($openingBalancePdf >= 0) #16a34a @else #dc2626 @endif;">
                    @if($openingBalancePdf >= 0)
                        {{ 'Rp ' . number_format($openingBalancePdf, 0) }}
                    @else
                        {{ '-Rp ' . number_format(abs($openingBalancePdf), 0) }}
                    @endif
                </td>
            </tr>
            @foreach($costsWithBalance as $i => $cost)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="no-wrap">{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                    <td style="max-width: 250px; word-wrap: break-word;">
                        {{ Str::limit($cost->description, 120) }}
                        @if($cost->vehicle)
                            <br><small style="color: #2563eb;">🚗 {{ $cost->vehicle->license_plate }}</small>
                        @endif
                        @if($cost->vendor)
                            <br><small style="color: #16a34a;">🏢 {{ $cost->vendor->name }}</small>
                        @endif
                    </td>
                    @if($cost->cost_type === 'cash')
                        <td class="text-right no-wrap"><strong>-</strong></td>
                        <td class="text-right no-wrap" style="color: #16a34a;"><strong>{{ 'Rp ' . number_format($cost->total_price, 0) }}</strong></td>
                    @else
                        <td class="text-right no-wrap"><strong>{{ 'Rp ' . number_format($cost->total_price, 0) }}</strong></td>
                        <td class="text-right no-wrap"><strong>-</strong></td>
                    @endif
                    <td class="text-right no-wrap" style="color: @if($cost->running_balance >= 0) #16a34a @else #dc2626 @endif;">
                        <strong>
                            @if($cost->running_balance >= 0)
                                {{ 'Rp ' . number_format($cost->running_balance, 0) }}
                            @else
                                {{ '-Rp ' . number_format(abs($cost->running_balance), 0) }}
                            @endif
                        </strong>
                    </td>
                </tr>
            @endforeach
            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right no-wrap"><strong>{{ 'Rp ' . number_format($totalDebetPdf, 0) }}</strong></td>
                <td class="text-right no-wrap" style="color: #16a34a;"><strong>{{ 'Rp ' . number_format($totalKreditPdf, 0) }}</strong></td>
                <td class="text-right no-wrap" style="color: @if($netBalancePdf >= 0) #16a34a @else #dc2626 @endif;">
                    <strong>
                        @if($netBalancePdf >= 0)
                            {{ 'Rp ' . number_format($netBalancePdf, 0) }}
                        @else
                            {{ '-Rp ' . number_format(abs($netBalancePdf), 0) }}
                        @endif
                    </strong>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the system on {{ now()->format('d F Y \a\t H:i') }}</p>
    </div>
</body>
</html>
