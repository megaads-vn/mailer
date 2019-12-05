<div class="modal fade" id="modal-add-template">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new template</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="content-template-form" action="{{ route('admin::content::create') }}" method="post">
                    <div class="form-group">
                        <input id="template_name" type="text" name="name" class="form-control" placeholder="Name of template content" />    
                    </div>
                    <div class="form-group">
                        <textarea id="template-content"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="js_close_template_modal" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-save-content">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>