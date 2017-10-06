<div class="main-panel">
    <div class="content">
        @yield('content')
    </div>
    <footer class="footer">
        <div class="container-fluid">
            <nav class="pull-left">
                <ul>
                    <li>
                        <a href="#">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Company
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Portfolio
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Blog
                        </a>
                    </li>
                </ul>
            </nav>
            <p class="copyright pull-right">
                &copy;
                <script>
                    document.write(new Date().getFullYear())
                </script>
                <a href="http://www.creative-tim.com">Creative Tim</a>, made with love for a better web
            </p>
        </div>
    </footer>
</div>
{{--

<header class="main-header">

    <a href="/" class="logo">
      <span class="logo-mini"><b>{{YunShop::app()->account['name']}}</b></span>
      <span class="logo-lg"><b>{{YunShop::app()->account['name']}}</b></span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"> <span  class="fa fa-group"></span>{{YunShop::app()->account['name']}}</span>
            </a>
            @if(YunShop::app()->role)
              <ul class="dropdown-menu">
                @if(YunShop::app()->role !='operator')
                <li class="about"> <i></i><a href="?c=account&a=post&uniacid={{YunShop::app()->uniacid}}&acid={{YunShop::app()->uniacid}}"> <span class="fa fa-wechat"></span>编辑当前账号资料</a> </li>
                @endif
                <li> <a href="?c=account&a=display&"><span class="fa fa-cogs fa-fw"></span>管理其他公众号</a> </li>
                <li> <a target="_blank" href="?c=utility&a=emulator&"><span class="fa fa-mobile fa-fw"></span>模拟测试</a> </li>
                @if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production')
                <li> <a target="_blank" href="{{yzWebUrl('menu.index')}}"><span class="fa fa-align-justify fa-fw"></span>菜单管理</a></li>
                @endif
                <li> <a target="_self" href="{{yzWebUrl('cache.update')}}" onclick="return confirm('确认更新缓存？');return false;"><span class="fa fa-refresh fa-fw"></span>更新缓存</a></li>
                <li> <a href="{{yzWebUrl('setting.shop.entry')}}"> <span class="fa fa-camera-retro fa-fw"></span>商城入口 </a>  </li>
              </ul>
            @endif
          </li>

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"> <span class="fa fa-user fa-fw"></span>{{YunShop::app()->username}}(@if(YunShop::app()->role == 'founder')系统管理员@elseif(YunShop::app()->role =='manager')公众号管理员@else公众号操作员@endif)</span>
            </a>
            <ul class="dropdown-menu">
              <li class="about"> <i></i> <a href="?c=user&a=profile&do=profile&"> <span class="fa fa-wechat fa-fw"></span>我的账号</a> </li>
              @if(YunShop::app()->role == 'founder')
              <li class="system one"> <a href="{{yzWebFullUrl('setting.key.index')}}"><span class="fa fa-key fa-fw"></span>商城授权</a> </li>
              <li class="system one"> <a href="?c=system&a=welcome&"><span class="fa fa-sitemap fa-fw"></span>系统选项</a> </li>
              <li class="system"> <a href="?c=system&a=welcome" target="_blank"><span class="fa fa-cloud-download fa-fw"></span>自动更新</a> </li>
              <li class="system three"> <a href="?c=system&a=updatecache&" target="_blank"><span class="fa fa-refresh fa-fw"></span>更新缓存</a> </li>
              @endif
              <li class="drop_out"> <a href="?c=user&a=logout"><span class="fa fa-sign-out fa-fw"></span>退出系统</a> </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>--}}
