@extends('layouts.base')
@section('title', trans('站点信息'))
@section('content')

    <script type='text/javascript'>
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
        });
    </script>

    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    {{--<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>--}}
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>站点设置</a></li>
        </ul>
    </div>
    {{--<div class="main rightlist">--}}

    <div class="top">
        <ul class="add-shopnav" id="myTab">
            <li class="active"><a href="#global">全局设置</a></li>
            <li><a href="#remote">远程附件</a></li>
        </ul>
    </div>
    <div class="info">
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane  active" id="global">@include('system.global')</div>
                <div class="tab-pane" id="remote">@include('system.remote')</div>
            </div>
        </div>
    </div>


@endsection