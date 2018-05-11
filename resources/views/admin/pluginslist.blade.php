@extends('layouts.base')
@section('title', trans('应用中心'))
@section('content')
    <style>
        .plugin-a{color: #000000;padding: 15px;margin-bottom: 30px;background: #ffffff;border: 1px #E5E5E5 solid;border-radius: 5px; display: inline-block;}
        .plugin-a:focus,
        .plugin-a:active,
        .plugin-a:hover {color: #000000;box-shadow: 0px 14px 26px -12px rgba(34, 133, 233, 0.42), 0px 4px 23px 0px rgba(0, 0, 0, 0.12), 0px 8px 10px -5px rgba(38, 130, 233, 0.2);}
        .plugin-a:disabled{box-shadow: none;}
        .plugin-i {font-size: 45px;border: 1px solid #E5E5E5; border-radius: 20%;width: 64px;line-height: 64px;height: 64px;float: left;background-color: #2398ff
        }
        .plugin-i-div{color: #999999;}
        .plugin-span {font-size: 18px; margin-left: 20px;}
        .plugin-span-down {font-size: 12px; margin-left: 20px;color: #404040}
    </style>
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h3 style="display: inline-block;    padding-left: 10px;">
                {{ trans('应用中心') }}
            </h3>
        </section>

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$dividend['name']}}</h3>
                    @unset($dividend['name'])
                </div>
                <div class="panel-body">
                    @foreach($dividend as $key => $plugin)
                        @if(can($key))
                        <div class="col-md-2">
                            <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                <div class="plugin-i-div">
                                    <i class="plugin-i"></i>
                                </div>
                                <span class="plugin-span">{{$plugin['name']}}</span>
                                <span class="plugin-span-down">{{$plugin['description']}}</span>
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$industry['name']}}</h3>
                    @unset($industry['name'])
                </div>
                <div class="panel-body">
                    @foreach($industry as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i"></i>
                                    </div>
                                    <span class="plugin-span">{{$plugin['name']}}</span>
                                    <span class="plugin-span-down">{{$plugin['description']}}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$marketing['name']}}</h3>
                    @unset($marketing['name'])
                </div>
                <div class="panel-body">
                    @foreach($marketing as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i"></i>
                                    </div>
                                    <span class="plugin-span">{{$plugin['name']}}</span>
                                    <span class="plugin-span-down">{{$plugin['description']}}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$tool['name']}}</h3>
                    @unset($tool['name'])
                </div>
                <div class="panel-body">
                    @foreach($tool as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i"></i>
                                    </div>
                                    <span class="plugin-span">{{$plugin['name']}}</span>
                                    <span class="plugin-span-down">{{$plugin['description']}}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$recharge['name']}}</h3>
                    @unset($recharge['name'])
                </div>
                <div class="panel-body">
                    @foreach($recharge as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i"></i>
                                    </div>
                                    <span class="plugin-span">{{$plugin['name']}}</span>
                                    <span class="plugin-span-down">{{$plugin['description']}}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #f6f6f6">
                    <h3 class="panel-title">{{$api['name']}}</h3>
                    @unset($api['name'])
                </div>
                <div class="panel-body">
                    @foreach($api as $key => $plugin)
                        @if(can($key))
                            <div class="col-md-2">
                                <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-12">
                                    <div class="plugin-i-div">
                                        <i class="plugin-i"></i>
                                    </div>
                                    <span class="plugin-span">{{$plugin['name']}}</span>
                                    <span class="plugin-span-down">{{$plugin['description']}}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

@endsection