<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Cost Date</th>
            <th>Description</th>
            <th>Total Inject</th>
            <th>Created By</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($costs as $i => $cost)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                <td>{{ $cost->description }}</td>
                <td>{{ $cost->total_price }}</td>
                <td>{{ $cost->createdBy?->name ?? '-' }}</td>
                <td>{{ $cost->created_at ? \Carbon\Carbon::parse($cost->created_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
