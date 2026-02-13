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
            <th>Tgl. Pembelian</th>
            <th>Harga Beli</th>
            <th>Harga Tunai</th>
            <th>Harga Kredit</th>
            <th>Tgl. Penjualan</th>
            <th>Harga Penjualan</th>
            <th>Status</th>
            <th>Description</th>
            <th>Created At</th>
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
                <td>{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 2) : '-' }}</td>
                <td>{{ $vehicle->color ?? '-' }}</td>
                <td>{{ $vehicle->fuel_type ?? '-' }}</td>
                <td>{{ number_format($vehicle->kilometer, 2) }}</td>
                <td>{{ $vehicle->warehouse?->name ?? '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_expiry_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->purchase_date ? \Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->purchase_price ? number_format($vehicle->purchase_price, 2) : '-' }}</td>
                <td>{{ $vehicle->display_price ? number_format($vehicle->display_price, 2) : '-' }}</td>
                <td>{{ $vehicle->loan_price ? number_format($vehicle->loan_price, 2) : '-' }}</td>
                <td>{{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->selling_price ? number_format($vehicle->selling_price, 2) : '-' }}</td>
                <td>{{ $vehicle->status == 1 ? 'Available' : 'Sold' }}</td>
                <td>{{ $vehicle->description ?? '-' }}</td>
                <td>{{ $vehicle->created_at ? \Carbon\Carbon::parse($vehicle->created_at)->format('M d, Y H:i') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

