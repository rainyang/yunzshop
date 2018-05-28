@extends('layouts.base')
@section('title', trans('应用中心'))
@section('content')
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
            @if($dividend)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">分润类</h3>
                </div>
                <div class="panel-body">
                    @foreach($dividend as $key => $plugin)
                        @if(can($key))
                        <div class="col-md-2">
                            <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                <div class="plugin-i-div">
                                    <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #F15353"></i>
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
            @if($industry)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">行业类</h3>
                </div>
                <div class="panel-body">
                    @foreach($industry as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #eb6f50"></i>
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
            @if($marketing)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">营销类</h3>
                </div>
                <div class="panel-body">
                    @foreach($marketing as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #f0b652"></i>
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
            @if($tool)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">工具类</h3>
                </div>
                <div class="panel-body">
                    @foreach($tool as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #f59753"></i>
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
            @if($recharge)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">生活充值</h3>
                </div>
                <div class="panel-body">
                    @foreach($recharge as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #50d9a7"></i>
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
            @if($api)
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">接口类</h3>
                </div>
                <div class="panel-body">
                    @foreach($api as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i {{$plugin['list_icon']}}" style="background-color: #53d5f0"></i>
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
        </div>
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