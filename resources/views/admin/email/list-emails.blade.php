<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>email</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($emails) && count($emails) > 0)
        @foreach($emails as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->email }}</td>
            <td style="width: 15%">{{ $item->created_at }}</td>
            <td style="width: 10%">
                <span id="js_delete_email" style="color: #fff;cursor: pointer" class="btn btn-warning" data-id="{{ $item->id }}">Delete</span>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5">No records found</td>
        </tr>
        @endif
    </tbody>
</table>