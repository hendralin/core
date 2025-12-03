<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Police Number</th>
            <th>Brand</th>
            <th>Type</th>
            <th>Model</th>
            <th>Category</th>
            <th>Year</th>
            <th>Chassis Number</th>
            <th>Engine Number</th>
            <th>Cylinder Capacity</th>
            <th>Color</th>
            <th>Fuel Type</th>
            <th>Kilometer</th>
            <th>Warehouse</th>
            <th>Registration Date</th>
            <th>Registration Expiry</th>
            <th>Purchase Date</th>
            <th>Purchase Price</th>
            <th>Selling Date</th>
            <th>Selling Price</th>
            <th>Status</th>
            <th>Description</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vehicles as $i => $vehicle)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $vehicle->police_number }}</td>
                <td>{{ $vehicle->brand?->name ?? '-' }}</td>
                <td>{{ $vehicle->type?->name ?? '-' }}</td>
                <td>{{ $vehicle->vehicle_model?->name ?? '-' }}</td>
                <td>{{ $vehicle->category?->name ?? '-' }}</td>
                <td>{{ $vehicle->year }}</td>
                <td>{{ $vehicle->chassis_number }}</td>
                <td>{{ $vehicle->engine_number }}</td>
                <td>{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 2) : '-' }}</td>
                <td>{{ $vehicle->color ?? '-' }}</td>
                <td>{{ $vehicle->fuel_type ?? '-' }}</td>
                <td>{{ number_format($vehicle->kilometer, 2) }}</td>
                <td>{{ $vehicle->warehouse?->name ?? '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->vehicle_registration_expiry_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->purchase_date ? \Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->purchase_price ? number_format($vehicle->purchase_price, 2) : '-' }}</td>
                <td>{{ $vehicle->selling_date ? \Carbon\Carbon::parse($vehicle->selling_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $vehicle->selling_price ? number_format($vehicle->selling_price, 2) : '-' }}</td>
                <td>{{ $vehicle->status == 1 ? 'Available' : 'Sold' }}</td>
                <td>{{ $vehicle->description ?? '-' }}</td>
                <td>{{ $vehicle->created_at ? \Carbon\Carbon::parse($vehicle->created_at)->format('M d, Y H:i') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

