<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar sidebar2" data-active-color="blue" data-background-color="white"
         data-image="../assets/img/sidebar-1.jpg">
    <div class="sidebar-wrapper" style="overflow-y: scroll;overflow-x: hidden;">
        <ul class="nav">
            @foreach(config('menu')[Yunshop::$currentItems[0]]['child'] as $key=>$value)
                @if(isset($value['menu']) && $value['menu'] == 1 && $value['can'])
                    @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))
                        <li class="{{in_array($key,Yunshop::$currentItems) ? 'active' : ''}}">
                            <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                <p>{{$value['name']}}</p>
                            </a>

                        </li>
                    @elseif($value['menu'] == 1)

                        <li class="{{in_array($key,Yunshop::$currentItems) ? 'active' : ''}}">
                            <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                <p>{{$value['name']}}</p>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </div>
</section>
