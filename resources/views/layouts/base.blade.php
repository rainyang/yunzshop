<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | Yunshop</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/addons/sz_yi/static/yunshop/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/addons/sz_yi/static/yunshop/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/addons/sz_yi/static/yunshop/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/addons/sz_yi/static/yunshop/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="/addons/sz_yi/static/yunshop/dist/css/skins/skin-red.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    {{--loding--}}
    <link href="/addons/sz_yi/static/yunshop/dist/css/load/load.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../addons/sz_yi/static/css/webstyle.css">
    @yield('css')
    {!! yz_header('admin') !!}
    <script>var require = { urlArgs: 'v=2017031511' };</script>
    <script src="./resource/js/lib/jquery-1.11.1.min.js"></script>
    <script src="./resource/js/app/util.js"></script>
    <script src="./resource/js/require.js"></script>
    <script src="./resource/js/app/config.js"></script>
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-red sidebar-mini">
<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_four"></div>
            <div class="object" id="object_three"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_one"></div>
        </div>
    </div>
</div>
<div class="wrapper">

    <!-- Main Header -->
    @include('layouts.mainHeader')
            <!-- Left side column. contains the logo and sidebar -->
    @include('layouts.mainSidebar')
            <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            {{--<h1>
              @yield('pageHeader')
              <small>@yield('pageDesc')</small>
            </h1>
            <ol class="breadcrumb">
              <li><a href="/admin"><i class="fa fa-dashboard"></i> 控制面板</a></li>
              <li class="active">Here</li>
            </ol>--}}
            <h6>
              {{--  @if(Request::is('admin/log-viewer*'))
                    仪表盘
                @else
                    {!! Breadcrumbs::render(Route::currentRouteName()) !!}
                @endif--}}

            </h6>
        </section>

        <!-- Main content -->
        <section class="content">

            @yield('content')
                    <!-- Your Page Content Here -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<!-- REQUIRED JS SCRIPTS -->


<!-- Bootstrap 3.3.6 -->
<script src="/addons/sz_yi/static/yunshop/bootstrap/js/bootstrap.js"></script>
<!-- AdminLTE App -->
<script src="/addons/sz_yi/static/yunshop/dist/js/app.min.js"></script>

<!-- dataTables -->
<script src="/addons/sz_yi/static/yunshop/dist/js/common.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
@yield('js')
        <!-- Main Footer -->
@include('layouts.mainFooter')
{!! yz_footer('admin') !!}
</body>
</html>
