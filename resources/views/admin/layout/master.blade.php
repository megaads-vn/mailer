<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MegaAds Mailer | @yield('title') </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @include('admin.common.stylesheets')
</head>
<body class="sidebar-mini layout-fixed sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
  @include('admin.common.top-bar')
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  @include('admin.common.left-menu')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @yield('content')
  </div>
  <!-- /.content-wrapper -->
  @include('admin.common.footer')
</div>
<!-- ./wrapper -->
@include('admin.common.scripts')
</body>
</html>
