 <ul class="treeview-menu {{in_array($item,Yunshop::$currentItems) ? 'menu-open' : ''}}">
        @foreach($childs as $key=>$value)
            @if(can($value['item']))
            @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))
                <li class="{{in_array($value['item'],Yunshop::$currentItems) ? 'active' : ''}}" >
                    <a href="javascript:void(0);"><i class="fa {{$value['icon'] or 'fa-circle-o'}}"></i> {{$value['name'] or ''}}
                        <span class="pull-right-container">
                                              <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                    </a>
                    @include('layouts.childMenu',['childs'=>$value['child']])
                </li>
            @else
                <li class="{{in_array($value['item'],Yunshop::$currentItems) ? 'active' : ''}}"><a href="{{isset($value['url']) ? yzWebFullUrl($value['url']) : ''}}">
                        <i class="fa {{$value['icon'] or 'fa-circle-o'}}"></i>{{$value['name'] or ''}}
                    </a>
                </li>
            @endif
            @endif
        @endforeach
    </ul>

