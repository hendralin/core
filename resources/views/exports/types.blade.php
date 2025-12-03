<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Brand</th>
            <th>Name</th>
            <th>Description</th>
            <th>Vehicles Count</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($types as $i => $type)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $type->brand->name ?? '-' }}</td>
                <td>{{ $type->name }}</td>
                <td>{{ $type->description ?? '-' }}</td>
                <td>{{ $type->vehicles->count() }}</td>
                <td>{{ $type->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
