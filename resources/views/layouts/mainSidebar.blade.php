<section class="sidebar" data-active-color="blue" data-background-color="black" data-image="../assets/img/sidebar-1.jpg">
    <div class="sidebar-wrapper">
        <ul class="nav">
            @foreach(config(config('app.menu_key','menu')) as $key=>$value)

                @if(isset($value['menu']) && $value['menu'] == 1 && $value['can'] && $value['left_first_show'] == 1)

                    @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))

                        <li class="{{in_array($key,Yunshop::$currentItems) ? 'active' : ''}}">

                            <a href="{{ \app\common\services\MenuService::canAccess($key) }}">
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>

                                <p style=" margin-top: -5px;">{{$value['name']}}</p>
                            </a>

                        </li>
                    @elseif($value['menu'] == 1)
                        <li class="{{in_array($key,Yunshop::$currentItems) ? 'active' : ''}}">
                            <a href="{{ \app\common\services\MenuService::canAccess($key) }}">
                                <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                <p style=" margin-top: -5px;">{{$value['name'] or ''}}</p>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </div>
</section>
