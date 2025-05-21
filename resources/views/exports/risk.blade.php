<table>
    <thead>
        <tr><th>Name</th><th>Description</th><th>Recommendation</th></tr>
    </thead>
    <tbody>
        @foreach($risk->details as $detail)
        <tr>
            <td>{{ $detail->name }}</td>
            <td>{{ $detail->description }}</td>
            <td>{{ $detail->recommendation }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
<strong>Solusi Otomatis:</strong>
<p>{{ $solution }}</p> 