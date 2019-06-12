@extends('layouts.base')
@section('content')
@section('title', trans('应用中心'))
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>

        <!--应用列表样式-->
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/plugins/list-icon/css/list-icon.css')}}">

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h3 style="display: inline-block;    padding-left: 10px;">
                {{ trans('应用中心') }}<i class="pl api-group-purchase"></i>
            </h3>
        </section>

        <div class="row">
            @foreach( $class as $key1 => $value)
                @if(is_array($data[$key1]))
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background-color: #f6f6f6">
                            <h3 class="panel-title">{{ $value['name'] }}</h3>
                        </div>
                        <div class="panel-body">
                            @foreach($data[$key1] as $key => $plugin)
                                @if(can($key))
                                <div class="col-md-2">
                                    <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                        <div class="plugin-i-div">
                                            <i class="plugin-i" style="background-color: {{$value['color']}}; background-image: url({{ $plugin['icon_url'] }})"></i>
                                        </div>
                                        <span class="plugin-span">{{$plugin['name']}}</span>
                                        <object>
                                            <a class="top_show"
                                               style="display: none;"
                                               href="{{yzWebUrl('plugins.setTopShow',['name'=>$key,'action'=>(app('plugins')->isTopShow($key) ? 1 : 0)])}}">
                                                <i class="fa fa-tags" @if(app('plugins')->isTopShow($key))style="color: red" @endif
                                                data-toggle="tooltip"  data-placement="top"
                                                   @if(app('plugins')->isTopShow($key))title="取消顶部显示?" @else title="选择顶部显示?"@endif></i>
                                            </a>
                                        </object>
                                        {{--<span class="plugin-span-down">{{$plugin['description']}}</span>--}}
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
         <div>
    </div>

    <script>
        $(function () { $("[data-toggle='tooltip']").tooltip(); });
        $(".plugin-a").mouseover(function(){
            $(this).find("a").css("display","inline");
        });
        $(".plugin-a").mouseleave(function () {
            $(this).find("a").css("display","none");
        })
</script>
@endsection