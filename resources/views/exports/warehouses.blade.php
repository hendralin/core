<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Address</th>
            <th>Stock Items</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warehouses as $i => $warehouse)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $warehouse->name }}</td>
                <td>{{ $warehouse->address ?? '-' }}</td>
                <td>{{ $warehouse->vehicles_count }}</td>
                <td>{{ $warehouse->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
