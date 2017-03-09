<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="keywords" content="@yield('keywords')"/>
    <meta name="description"  content="@yield('description')"/>
    <link rel="shortcut icon"  href="@yield('icon')"/>
    @include('public.admin.css')
    @include('public.admin.js')
</head>
<body>


<div class="container-fluid">
    @include('public.admin.top')
    @include('public.admin.menu')
    @include('public.admin.message')
    <div class="main">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header-new" style=""><i class="iconfont icon-huiyuan"></i> 会员</h3>
                <ol class="breadcrumb">
                    <li><i class="iconfont icon-huiyuan"></i><a href="index.html">会员</a></li>  <!--一级分类-->
                    <li><i class="iconfont icon-huiyuanguanli"></i><a href="#">会员管理</a></li>   <!-- 二级分类-->
                    <li>三级分类</li>              <!--三级分类无图标  无三级分类时  不显示-->
                </ol>
            </div>
        </div>

    @yield('content')
    </div>
</div>

    <script language='javascript'>
      require(['bootstrap'], function ($) {
        $('.btn,.tip').each(function () {

          if ($(this).closest('td').css('position') == 'relative') {
            return true;
          }
          $(this).hover(function () {
            $(this).tooltip('show');
          }, function () {
            $(this).tooltip('hide');
          });
        });

        $('.js-clip').each(function () {
          util.clip(this, $(this).attr('data-url'));
        });

      });

    </script>

</body>
</html>
