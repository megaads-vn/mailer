<div class="modal fade" id="modal-add-email">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new email</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="email-form" action="{{ route('admin::emails:create') }}" method="post">
                    <div class="form-group">
                        <input id="email_name" type="text" name="name" class="form-control" placeholder="Enter the name" />    
                    </div>
                    <div class="form-group">
                        <input id="email_address" type="text" name="address" class="form-control" placeholder="Email address" />    
                    </div>
                    <div class="form-group">
                        <select id="mail_group" name="group" class="form-control">
                            <option value="">-- Select group --</option>
                            @if (isset($groups) && count($groups) > 0)
                                @foreach ($groups as $item) 
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="js_close_email_modal" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-save-email">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>