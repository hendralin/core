<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>User</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salesmen as $i => $salesman)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $salesman->name }}</td>
                <td>{{ $salesman->phone ?? '-' }}</td>
                <td>{{ $salesman->email ?? '-' }}</td>
                <td>{{ $salesman->address ?? '-' }}</td>
                <td>{{ $salesman->user->name ?? '-' }}</td>
                <td>{{ $salesman->user && $salesman->user->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>{{ $salesman->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
