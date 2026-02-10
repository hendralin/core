<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 1px solid #333; padding-bottom: 12px; }
        .header h1 { font-size: 18pt; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLIP GAJI</h1>
        <p>Periode: {{ $salary->salary_date ? $salary->salary_date->format('F Y') : '-' }}</p>
    </div>
    <p><strong>Nama:</strong> {{ $salary->employee?->name ?? '-' }}</p>
    <p><strong>Jabatan:</strong> {{ $salary->employee?->position?->name ?? '-' }}</p>
    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Satuan (Rp)</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salary->salaryDetails ?? [] as $d)
            <tr>
                @php
                    $compName = $d->salaryComponent?->name ?? '-';
                    if ($d->vehicle) {
                        $vehicleLabel = trim(implode(' ', array_filter([
                            $d->vehicle->brand?->name,
                            $d->vehicle->vehicle_model?->name,
                            $d->vehicle->year,
                            $d->vehicle->police_number,
                        ])));
                        $compName = $compName . ' - ' . $vehicleLabel;
                    }
                @endphp
                <td>{{ $compName }}</td>
                <td class="text-right">{{ $d->quantity }}</td>
                <td class="text-right">{{ number_format($d->amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($d->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Gaji</td>
                <td class="text-right">Rp {{ number_format($salary->salaryDetails->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
