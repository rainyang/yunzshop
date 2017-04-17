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

              <span class="hidden-xs">{{YunShop::app()->username}}</span>
            </a>
            <ul class="dropdown-menu">


              <!-- The user image in the menu -->
              <!--<li class="user-header">
                <img src="" class="img-circle" alt="User Image">
                <p>
                  {{YunShop::app()->username}} - {{YunShop::app()->role}}
                  <small>最后登录:{{date('Y-m-d H:i',YunShop::app()->user['lastvisit'])}}</small>
                </p>
              </li>

              </li>
              <!-- Menu Footer
              <li class="user-footer">

                <div class="pull-right">
                  <a href="?c=user&a=logout" class="btn btn-default btn-flat">登出</a>
                </div>
              </li>-->



              <li class="about"> <i></i> <b>关于我们</b></li>
              <li><span class="fa fa-user"></span>我的账号</li>
              <li><span class="fa fa-gears"></span>设置</li>
              <li><span class="fa fa-share"></span>退出</li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>