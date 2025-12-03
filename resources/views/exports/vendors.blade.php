<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendors as $i => $vendor)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $vendor->name }}</td>
                <td>{{ $vendor->contact ?? '-' }}</td>
                <td>{{ $vendor->phone ?? '-' }}</td>
                <td>{{ $vendor->email ?? '-' }}</td>
                <td>{{ $vendor->address ?? '-' }}</td>
                <td>{{ $vendor->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
