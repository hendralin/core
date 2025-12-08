<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Description</th>
            <th>Debet</th>
            <th>Kredit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <!-- Opening Balance Row -->
        <tr>
            <td>-</td>
            <td colspan="4">Opening Balance</td>
            <td>
                @if($openingBalanceExcel >= 0)
                    {{ $openingBalanceExcel }}
                @else
                    {{ abs($openingBalanceExcel) }}
                @endif
            </td>
        </tr>
        @foreach($costsWithBalance as $i => $cost)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                <td>
                    {{ $cost->description }}
                    @if($cost->vehicle)
                        - {{ $cost->vehicle->license_plate }}
                    @endif
                    @if($cost->vendor)
                        - {{ $cost->vendor->name }}
                    @endif
                </td>
                @if($cost->cost_type === 'cash')
                    <td>-</td>
                    <td>{{ $cost->total_price }}</td>
                @else
                    <td>{{ $cost->total_price }}</td>
                    <td>-</td>
                @endif
                <td>
                    @if($cost->running_balance >= 0)
                        {{ $cost->running_balance }}
                    @else
                        {{ abs($cost->running_balance) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
