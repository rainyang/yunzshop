<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | 芸商城-Yun Shop</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{static_url('yunshop/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{static_url('yunshop/libs/font-awesome/4.5.0/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{static_url('yunshop/libs/ionicons/2.0.1/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{static_url('yunshop/dist/css/AdminLTE.css')}}">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="{{static_url('yunshop/dist/css/skins/skin-red.min.css')}}">
    <link href="./resource/css/common.css?v=20161011" rel="stylesheet">


    {{--loding--}}
    <link href="{{static_url('yunshop/dist/css/load/load.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{static_url('css/webstyle.css')}}">
    @yield('css')
    {!! yz_header('admin') !!}

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>var require = { urlArgs: 'v={{time()}}' };</script>

    <script type="text/javascript">

      if(navigator.appName == 'Microsoft Internet Explorer'){
        if(navigator.userAgent.indexOf("MSIE 5.0")>0 ||
          navigator.userAgent.indexOf("MSIE 6.0")>0 ||
          navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
          alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
        }
      }

      window.sysinfo = {
        'uniacid': '{{YunShop::app()->uniacid}}',
        'acid': '{{YunShop::app()->acid}}',
        'openid': '{{YunShop::app()->openid}}',
        'uid': '{{YunShop::app()->uid}}',
        'siteroot': './',
          'static_url': '{{static_url('')}}',
        'siteurl': '{!! YunShop::app()->siteurl !!}',
        'attachurl': '{{YunShop::app()->attachurl}}',
        'attachurl_local': '{{YunShop::app()->attachurl_local}}',
        'attachurl_remote': '{{YunShop::app()->attachurl_remote}}',

        'cookie' : {'pre': '{{YunShop::app()->config['cookie']['pre']}}'}
      };
    </script>

    <!-- jQuery 2.2.0 -->
    <script src="//cdn.bootcss.com/jquery/2.2.3/jquery.min.js"></script>

    <script type="text/javascript" src="{{static_url('resource/js/app/util.js')}}"></script>

    <script type="text/javascript" src="{{static_url('resource/js/require.js')}}"></script>
    <script type="text/javascript" src="{{static_url('resource/js/app/util.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/app/config.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

</head>
<style type="text/css">
    .red {float:left;color:red}
    .white{float:left;color:#fff}

    .tooltipbox {
        background:#ffffff;border:1px solid #c40808; position:absolute; left:0;top:0; text-align:center;height:25px;
        color:#c40808;padding:2px 5px 1px 5px; border-radius:3px;z-index:1000;
    }
    .red { float:left;color:red}
</style>
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
<body class="hold-transition skin-red sidebar-mini" >
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
      @include('public.admin.message')
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
<!-- AdminLTE App -->
<script src="{{static_url('yunshop/dist/js/app.min.js')}}"></script>

<!-- dataTables -->
<script src="{{static_url('yunshop/dist/js/common.js')}}"></script>

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
