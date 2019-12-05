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
				<h3 class="card-title">List email</h3>
				<div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-email">
						  <i class="fas fa-plus"></i> Add Email</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div id="js_table_email" class="card-body table-responsive p-0">
                @include('admin.email.list-emails')
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
@include('admin.email.modal')
@endsection
@section('script')
 @parent
 <script type="text/javascript">
	var $templateContent = $('#template-content');
	$(function () {
    	// Summernote
    	$templateContent.summernote({height: 200});
		// Form submit
		$('#email-form').submit(function(e) {
			e.preventDefault();
			var name = $('#email_name').val();
			var email = $('#email_address').val();
			$.ajax({
				url: $(this).attr('action'), 
				method: $(this).attr('method'),
				data: {name: name, email: email},
				success: function(response) {
					if (response.status == 'successful') {
						$('#template_name').val('');
						$templateContent.summernote('reset');
						$('#js_close_email_modal').trigger('click');
						$('#js_table_email').html(response.data);
					}
				}
			})
		});
		$('.btn-save-email').click(function() {
			$('#email-form').submit();
		});
		$(document).on("click", "#js_delete_email", function (ev) {
			var choose = confirm('Are you sure to delete this item?');
			if (choose) {
				$.ajax({
				url: '/admin/email-users/delete', 
				method: 'POST',
				data: {id: $(this).attr('data-id')},
				success: function(response) {
					if (response.status == 'successful') {
						$('#js_table_email').html(response.data);
						}
					}
				});
			}
		});
  	})
</script>
@endsection