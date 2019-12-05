@extends('admin.layout.master')
@section('title', 'Template Content')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Template Content</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('admin::home') }}">Home</a></li>
              <li class="breadcrumb-item active">Template Content</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <!-- Small boxes (Stat box) -->
        <div class="row">
        <div class="col-12">
            <div class="card">
              <div class="card-header">
				<h3 class="card-title">List template</h3>
				<div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-template">
						  <i class="fas fa-plus"></i> Add Template</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div id="js-table-content" class="card-body table-responsive p-0">
                @include('admin.content.list-template')
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
	<!-- /.content -->
@include('admin.content.modal')
@endsection
@section('script')
 @parent
 <script type="text/javascript">
	var $templateContent = $('#template-content');
	$(function () {
    	// Summernote
    	$templateContent.summernote({height: 200});
		// Form submit
		$('#content-template-form').submit(function(e) {
			e.preventDefault();
			var name = $('#template_name').val();
			var content = $templateContent.summernote('code');
			$.ajax({
				url: $(this).attr('action'), 
				method: $(this).attr('method'),
				data: {name: name, content: content},
				success: function(response) {
					if (response.status == 'successful') {
						$('#template_name').val('');
						$templateContent.summernote('reset');
						$('#js_close_template_modal').trigger('click');
						$('#js-table-content').html(response.data);
					}
				}
			})
		});
		$('.btn-save-content').click(function() {
			$('#content-template-form').submit();
		});
		$(document).on("click", "#js_btn_delete_template", function (ev) {
			var choose = confirm('Are you sure to delete this item?');
			if (choose) {
				$.ajax({
				url: '/admin/template-content/delete', 
				method: 'POST',
				data: {id: $(this).attr('data-id')},
				success: function(response) {
					if (response.status == 'successful') {
						$('#js-table-content').html(response.data);
						}
					}
				});
			}
		});
  	})
</script>
@endsection