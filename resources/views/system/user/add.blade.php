@extends('layouts.base')
@section('title', trans('添加用户'))
@section('content')

    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    {{--<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>--}}
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>添加用户</a></li>
        </ul>
    </div>
    {{--<div class="main rightlist">--}}

    <form id="goods-edit" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel-default panel-center">
            <div class="info">
                <div class="panel-body">
                    <div class="tab-content">

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="user[name]" id="displayorder" placeholder="请输入用户名，用户名为3-30个字符，包括汉字、大写小字母、数字" class="form-control" value="{{$user->name}}" />
                            </div>
                        </div>

                        @if(!$user->password)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="user[password]" id="displayorder" placeholder="请输入密码" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="user[re_password]" id="displayorder" placeholder="请输入确认密码" class="form-control" />
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="user[phone]" id="displayorder" placeholder="请输入手机号" class="form-control" value="{{$user->phone}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">创建平台数量</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="user[application_number]" id="displayorder" placeholder="允许该用户创建平台的数量，为0或为空则不允许创建！" class="form-control" value="{{$user->application_number}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">有效期</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="user[effective_time]" id="displayorder" placeholder="设置用户组有效期，为0则永久有效！" class="form-control" value="{{$user->effective_time}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                            <div class="col-xs-6">
                                <textarea placeholder="可输入备注信息" rows="3" cols="20" name="user[remarks]" class="form-control">{{$user->remarks}}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-sm-12 mrleft40 border-t">
                        <input type="submit" style="float:right;" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection