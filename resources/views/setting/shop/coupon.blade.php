@extends('layouts.base')
@section('content')
@section('title', trans('优惠券设置'))
<div class="w1200 m0a">
    <div class="rightlist">

        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="shopform">
            <div class="panel panel-default">
                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券到期消息通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="table-responsive ">
                                <div class="input-group">
                                    <div class="input-group-addon">到期前</div>
                                    <input type="text" name="coupon[delayed]" class="form-control" value="{{$set['delayed']}}"/>
                                    <div class="input-group-addon">天发送通知</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提醒时间</label>
                        <div class="col-sm-6 col-xs-6">
                            <div class='input-group'>
                                <label class="radio-inline">
                                    <input type="radio" name="coupon[send_times]" value="0"
                                           @if($set['send_times'] == 0) checked="checked" @endif />
                                    每天
                                    <select name='coupon[every_day]' class='form-control'>
                                        @foreach($hourData as $hour)
                                            <option value='{{$hour['key']}}'
                                                    @if($set['every_day'] == $hour['key']) selected @endif>{{$hour['name']}}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">任务处理通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="coupon[template_id]" class="form-control" value="{{$set['template_id']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券过期提醒</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="coupon[expire_title]" class="form-control" value="{{$set['expire_title']}}" />
                            <div class="help-block">标题，默认"优惠券过期提醒"</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="coupon[expire]" class="form-control" >{{$set['expire']}}</textarea>
                            模板变量: [优惠券名称][优惠券使用范围][过期时间]
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
