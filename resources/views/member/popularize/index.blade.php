@extends('layouts.base')
@section('content')

<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不显示插件</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline">
                            <input type="checkbox"  name="base[relation_level][]" value="1" @if (in_array(1, $relation_level)) checked @endif >推广中心</input>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        @foreach($info as $item )
                            <label class="checkbox-inline" style="margin-left: 15px;margin-bottom: 10px">
                                <input type="checkbox"  name="base[relation_level][]" value="1" @if (in_array(1, $relation_level)) checked @endif >区域分红</input>
                            </label>
                        @endforeach
                    </div>

                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-6 col-xs-12">
                        <span class="help-block">不显示推广中心，则在未勾选端口中无法访问推广中心、提现、提现明细、收入明细、领取收益、收入分享等页面，会员中心不显示提现、我的推广。
其他插件如果不勾选，则在未勾选端口中无法访问插件任何前端页面，会员中心也不显示对应入口；
未勾选端口访问不显示的页面，将强制跳转到跳转页面。</span>
                    </div>
                </div>

                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>


            </div>
        </div>
    </form>
</div>
</div>
@endsection
