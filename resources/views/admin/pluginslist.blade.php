@extends('layouts.base')
@section('title', trans('应用中心'))
@section('content')
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
            @foreach($plugins as $key => $plugin)
                @if(can($key))
                <div class="col-md-3">
                    <div class="col-md-11">
                        <div class="card card-pricing1 card-raised" style="border-radius:4px;    height: 100px;">
                            <div class="content">
                                {{--<div class="icon icon-rose">
                                    <i class="fa {{$plugin['icon']}}"></i>
                                </div>
                                <h3 class="card-title"></h3>
                                <p class="card-description">

                                </p>--}}
                                <div class="icon icon-rose">
                                    <i class="fa {{$plugin['icon']}}"></i>
                                </div>
                                <a style="display: inline-block;margin-top: 8px;" href="{{yzWebFullUrl($plugin['url'])}}" class="btn btn-rose btn-round">{{$plugin['name']}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

@endsection