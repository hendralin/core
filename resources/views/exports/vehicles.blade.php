<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Brand</th>
            <th>Type</th>
            <th>Model</th>
            <th>Category</th>
            <th>Year</th>
            <th>Nopol</th>
            <th>Cylinder</th>
            <th>Color</th>
            <th>Fuel Type</th>
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
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $vehicle->brand?->name ?? '-' }}</td>
                <td>{{ $vehicle->type?->name ?? '-' }}</td>
                <td>{{ $vehicle->vehicle_model?->name ?? '-' }}</td>
                <td>{{ $vehicle->category?->name ?? '-' }}</td>
                <td>{{ $vehicle->year }}</td>
                <td>{{ $vehicle->police_number }}</td>
                <td>{{ $vehicle->cylinder_capacity ?? '-' }}</td>
                <td>{{ $vehicle->color ?? '-' }}</td>
                <td>{{ $vehicle->fuel_type ?? '-' }}</td>
                <td>{{ $vehicle->kilometer }}</td>
                <td>{{ $vehicle->warehouse?->name ?? '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_expiry_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}</td>
                @php
                    $totalModal = ($vehicle->purchase_price ?? 0) + ($vehicle->costs->sum('total_price') ?? 0) + ($vehicle->commissions->where('type', 2)->sum('amount') ?? 0) + ($vehicle->roadside_allowance ?? 0);
                @endphp
                <td>{{ $totalModal > 0 ? $totalModal : '-' }}</td>
                <td>{{ $vehicle->display_price ? $vehicle->display_price : '-' }}</td>
                <td>{{ $vehicle->loan_price ? $vehicle->loan_price : '-' }}</td>
                @if(($statusFilter ?? '') !== '1')
                    <td>{{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('M d, Y') : '-' }}</td>
                    <td>{{ $vehicle->selling_price ? $vehicle->selling_price : '-' }}</td>
                @endif
                <td>{{ $vehicle->status == 1 ? 'Available' : 'Sold' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

