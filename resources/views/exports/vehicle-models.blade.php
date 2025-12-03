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
        @foreach($vehicleModels as $i => $vehicleModel)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $vehicleModel->name }}</td>
                <td>{{ $vehicleModel->description ?? '-' }}</td>
                <td>{{ $vehicleModel->vehicles_count }}</td>
                <td>{{ $vehicleModel->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
