<div class="modal fade" id="modal-add-group">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new group</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="group-form" action="{{ route('admin:groups::create') }}" method="post">
                    <div class="form-group">
                        <input id="group_name" type="text" name="name" class="form-control" placeholder="Name of group" />    
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="js_close_group_modal" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-save-group">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>