@extends('layouts.base')
@section('title', trans('修改密码'))
@section('content')

    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    {{--<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>--}}
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>修改密码</a></li>
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
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">原密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="user[old_password]" id="displayorder" placeholder="请输入密码" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">新密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="user[password]" id="displayorder" placeholder="请输入密码" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认新密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="user[re_password]" id="displayorder" placeholder="请输入确认密码" class="form-control" />
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