<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Position</th>
            <th>Join Date</th>
            <th>Status</th>
            <th>Salary Components</th>
            <th>User Account</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $i => $employee)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->position->name ?? '-' }}</td>
                <td>{{ $employee->join_date->format('M d, Y') }}</td>
                <td>
                    @if($employee->status == 1) Active @elseif($employee->status == 2) Pending @else Inactive @endif
                </td>
                <td>{{ $employee->employee_salary_components_count }}</td>
                <td>{{ $employee->user->email ?? '-' }}</td>
                <td>{{ $employee->created_at->format('M d, Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
