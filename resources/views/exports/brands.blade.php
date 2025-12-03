<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Description</th>
            <th>Vehicles</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brands as $i => $brand)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->description ?? '-' }}</td>
                <td>{{ $brand->vehicles_count }}</td>
                <td>{{ $brand->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
