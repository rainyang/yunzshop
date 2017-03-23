<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">


        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header" style="color: white">栏目导航</li>
            <!-- Optionally, you can add icons to the links -->

            <li><a href="{{yzWebFullUrl('index.index')}}"><i class="fa fa-dashboard"></i> <span>控制面板</span></a></li>

            @foreach(Config::get('menu') as $key=>$value)
                @if(isset($value['menu']) && $value['menu'] == 1 && can($value['item']))
                    @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))
                        <li class="treeview {{in_array($value['item'],Yunshop::$currentItems) ? 'active' : ''}}">
                            <a href="javascript:void(0);" ><i class="fa {{$value['icon'] or 'fa-circle-o'}}"></i> <span>{{$value['name'] or ''}}</span>
                            <span class="pull-right-container"><i
                                        class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                             @include('layouts.childMenu',['childs'=>$value['child'],'item'=>$value['item']])
                        </li>
                    @else
                        <li class="{{in_array($value['item'],Yunshop::$currentItems) ? 'active' : ''}}"><a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}">
                                <i class="fa {{$value['icon'] or 'fa-circle-o'}}"></i> {{$value['name'] or ''}}
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>