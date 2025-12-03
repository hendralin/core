<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Cost Date</th>
            <th>Vehicle Police Number</th>
            <th>Vehicle Brand</th>
            <th>Vehicle Type</th>
            <th>Vendor Name</th>
            <th>Description</th>
            <th>Total Cost</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($costs as $i => $cost)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                <td>{{ $cost->vehicle?->police_number ?? '-' }}</td>
                <td>{{ $cost->vehicle?->brand?->name ?? '-' }}</td>
                <td>{{ $cost->vehicle?->type?->name ?? '-' }}</td>
                <td>{{ $cost->vendor?->name ?? '-' }}</td>
                <td>{{ $cost->description }}</td>
                <td>{{ $cost->total_price }}</td>
                <td>{{ ucfirst($cost->status) }}</td>
                <td>{{ $cost->createdBy?->name ?? '-' }}</td>
                <td>{{ $cost->created_at ? \Carbon\Carbon::parse($cost->created_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
