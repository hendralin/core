<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Description</th>
            <th>Usage Count</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salaryComponents as $i => $salaryComponent)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $salaryComponent->name }}</td>
                <td>{{ $salaryComponent->description ?? '-' }}</td>
                <td>{{ $salaryComponent->employee_salary_components_count }}</td>
                <td>{{ $salaryComponent->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
