  <header class="main-header">

    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>芸</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>芸</b>商城</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
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
              </ul>
            @endif
          </li>

          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"> <span class="fa fa-user fa-fw"></span>{{YunShop::app()->username}}(@if(YunShop::app()->role == 'founder')系统管理员@elseif(YunShop::app()->role =='manager')公众号管理员@else公众号操作员@endif)</span>
            </a>
            <ul class="dropdown-menu">
              <li class="about"> <i></i> <a href="?c=user&a=profile&do=profile&"> <span class="fa fa-wechat fa-fw"></span>我的账号</a> </li>
              @if(YunShop::app()->role == 'founder')
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
  </header>