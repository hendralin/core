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
        @foreach($categories as $i => $category)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->description ?? '-' }}</td>
                <td>{{ $category->vehicles_count }}</td>
                <td>{{ $category->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
