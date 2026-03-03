<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kendaraan</th>
            <th>Warehouse</th>
            <th>Deskripsi</th>
            <th>Total Pembayaran</th>
            <th>Dibuat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @foreach($costs as $i => $cost)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') : '-' }}</td>
                <td>{{ $cost->vehicle?->police_number ?? '-' }}</td>
                <td>{{ $cost->warehouse?->name ?? '-' }}</td>
                <td>{{ $cost->description }}</td>
                <td>{{ $cost->total_price }}</td>
                <td>{{ $cost->createdBy?->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

