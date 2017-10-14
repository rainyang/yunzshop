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
            @foreach($plugins as $plugin)
            <div class="col-md-3">
                <div class="col-md-11">
                    <div class="card card-pricing card-raised" style="border-radius:4px;    height: 290px;">
                        <div class="content">
                            <h6 class="category">{{$plugin['name']}}</h6>
                            <div class="icon icon-rose">
                                {{--<i class="material-icons">home</i>--}}
                                <i class="fa {{$plugin['icon']}}"></i>
                            </div>
                            <h3 class="card-title"></h3>
                            <p class="card-description">

                            </p>
                            <a href="{{yzWebFullUrl($plugin['url'])}}" class="btn btn-rose btn-round">{{$plugin['name']}}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

@endsection