@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="main">
            <form id="baseform" method="post" class="form-horizontal form">
                <div class="rightlist">

                    <div class="panel panel-default">
                        <div class="panel-heading">客户端</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="set[switch]" value="1"
                                               @if ($set['switch'] == 1)
                                               checked
                                                @endif> 开启
                                    </label>

                                    <label class="radio-inline">
                                        <input type="radio" name="set[switch]" value="0"
                                               @if (empty($set['switch']) || $set['switch'] == 0)
                                               checked
                                                @endif> 关闭
                                    </label>
                                    <span class="help-block">启用代付功能后，代付发起人（买家）下单后，可将订单分享给小伙伴（朋友圈、微信群、微信好友），请他帮忙付款。</span>
                                </div>
                            </div>
                        </div>
                        <div class="panel-heading">分享设置</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">发起人求助</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="set[title]" class="form-control" value="{{$set['title']}}" autocomplete="off" placeholder="土豪大大，跪求代付">
                                    <span class="help-block">默认：土豪大大，跪求代付</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="submit" name="submit" value="保存设置" class="btn btn-primary" data-original-title="" title="">
                                    <input type="hidden" name="token" value="{$_W['token']}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection