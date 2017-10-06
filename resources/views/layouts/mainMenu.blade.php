<div class="yz-menu-header">
    <nav class="navbar navbar-transparent navbar-absolute">
        <div class="container-fluid">
            <div class="navbar-minimize">
                <button id="minimizeSidebar" class="btn btn-round btn-white btn-fill btn-just-icon">
                    <i class="material-icons visible-on-sidebar-regular">more_vert</i>
                    <i class="material-icons visible-on-sidebar-mini">view_list</i>
                </button>
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <ul class="clearfix pull-left" style="">
                    <li class=" active" style="">
                        <a ui-sref="shop.dashboard" href="/shop">商城</a>
                    </li>
                    <li class="" style="">
                        <a ui-sref="shop.dashboard" href="/shop">商品</a>
                    </li>
                    <li class="" style="">
                        <a ui-sref="shop.dashboard" href="/shop">会员</a>
                    </li>
                    <li class="" style="">
                        <a ui-sref="shop.dashboard" href="/shop">订单</a>
                    </li>
                    <li class="" style="">
                        <a ui-sref="shop.dashboard" href="/shop">推客</a>
                    </li>
                </ul>
            </div>
            <div class="collapse navbar-collapse" style="float:right">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="material-icons">dashboard</i>
                            <p class="hidden-lg hidden-md">Dashboard</p>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="material-icons">notifications</i>
                            <span class="notification">5</span>
                            <p class="hidden-lg hidden-md">
                                Notifications
                                <b class="caret"></b>
                            </p>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="?c=system&a=updatecache&" target="_blank"><span class="fa fa-refresh fa-fw"></span>更新系统缓存</a>
                            </li>
                            <li>
                            <li> <a target="_self" href="{{yzWebUrl('cache.update')}}" onclick="return confirm('确认更新缓存？');return false;"><span class="fa fa-refresh fa-fw"></span>更新商城缓存</a></li>
                            </li>
                            <li>
                                <a href="#">You're now friend with Andrew</a>
                            </li>
                            <li>
                                <a href="#">Another Notification</a>
                            </li>
                            <li>
                                <a href="#">Another One</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="material-icons">person</i>
                            <p class="hidden-lg hidden-md">Profile</p>
                        </a>
                    </li>
                    <li class="separator hidden-lg hidden-md"></li>
                </ul>
                {{--<form class="navbar-form navbar-right" role="search">
                    <div class="form-group form-search is-empty">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="material-input"></span>
                    </div>
                    <button type="submit" class="btn btn-white btn-round btn-just-icon">
                        <i class="material-icons">search</i>
                        <div class="ripple-container"></div>
                    </button>
                </form>--}}
            </div>
        </div>
    </nav>
</div>