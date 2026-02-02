<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Description</th>
            <th>Employees</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($positions as $i => $position)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $position->name }}</td>
                <td>{{ $position->description ?? '-' }}</td>
                <td>{{ $position->employees_count }}</td>
                <td>{{ $position->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
