<table>
    <thead>
    <tr>
        {{-- <th>Deal ID</th> --}}
        <th>Title</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Website</th>
        <th>Deal Source</th>
        <th>Priority</th>
        <th>Contact Person</th>
        <th>Close Date</th>
        <th>Creation Date</th>
        <th>Progress</th>
        <th>Last Updated</th>
        <th>Status</th>
        {{-- <th>Deal Value</th>
        <th>Assigned To</th>
        <th>Stage</th>
        <th>Notes</th> --}}




        
    </tr>
    </thead>
    <tbody>
    @foreach($deals as $deal)
        <tr>
            {{-- <td>{{ $deal->id }}</td> --}}
            <td>{{ $deal->title }}</td>
            <td>{{ $deal->email }}</td>
            <td>{{ $deal->phone }}</td>
            <td>{{ $deal->website }}</td>
            <td>{{ $deal->deal_source }}</td>
            <td>{{ $deal->priority }}</td>
            <td>{{ $deal->contact_person }}</td>
            <td>{{ $deal->close_date }}</td>
            <td>{{ $deal->created_at }}</td>
            <td>{{ $deal->comment }}</td>
            <td>{{ $deal->updated_at }}</td>
            <td>{{ $deal->status }}</td>
          


        </tr>
    @endforeach
    </tbody>
</table>
