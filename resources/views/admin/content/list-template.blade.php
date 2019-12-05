<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Content</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($templates) && count($templates) > 0)
        @foreach($templates as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->name }}</td>
            <td>{!! $item->content !!}</td>
            <td style="width: 15%">{{ $item->created_at }}</td>
            <td style="width: 10%">
                <span id="js_btn_delete_template" style="color: #fff;cursor: pointer" class="btn btn-warning" data-id="{{ $item->id }}">Delete</span>
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