<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($groups) && count($groups) > 0)
        @foreach($groups as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->name }}</td>
            <td style="width: 15%">{{ $item->created_at }}</td>
            <td style="width: 10%">
                <span id="js_delete_group" style="color: #fff;cursor: pointer" class="btn btn-warning" data-id="{{ $item->id }}">Delete</span>
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