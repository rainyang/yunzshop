@extends('layouts.base')
@section('title', '余额设置')
@section('content')

    <div class="main rightlist">

        <form action="{{ yzWebUrl('finance.balance-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default">

                <div class="alert alert-warning alert-important">
                    余额支付开关、及其他支付设置，请到交易设置查看<a href="{{ yzWebUrl('setting.shop.pay') }}" target="_blank">【点击跳转交易设置】</a>.
                </div>

                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启账户充值</label>
                        <div class="col-sm-9 col-xs-12">
                            <!--原字段 name = trade[closerecharge] -->
                            <label class='radio-inline'><input type='radio' name='balance[recharge]' value='1'
                                                               @if($balance['recharge'] == 1) checked @endif/>
                                开启</label>
                            <label class='radio-inline'><input type='radio' name='balance[recharge]' value='0'
                                                               @if($balance['recharge'] == 0) checked @endif/>
                                关闭</label>
                            <span class='help-block'>是否允许用户对账户余额进行充值</span>
                        </div>
                    </div>

                    <div id='recharge' @if( empty($balance['recharge']) ) style="display:none" @endif>
                        <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            @if($balance['recharge_activity'] == 1)
                            <label class='radio-inline'>
                                <input type='radio' name='balance[recharge_activity]' value='2'/>
                                重置充值活动
                            </label>
                            @endif
                            <label class='radio-inline'>
                                <input type='radio' name='balance[recharge_activity]' value='1' @if($balance['recharge_activity'] == 1) checked @endif/>
                                启用充值活动
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='balance[recharge_activity]' value='0' @if(empty($balance['recharge_activity'])) checked @endif/>
                                关闭充值活动
                            </label>
                            <span class='help-block'>开启时需选择活动开始及结束时间、会员最多参与次数(-1，0，空则不限参与次数)，重置充值活动：开启新充值活动统计</span>
                            <div id='recharge_activity' @if( empty($balance['recharge_activity']) ) style="display:none" @endif>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay" >
                                        <label class='radio-inline' ></label>
                                    </div>
                                    <div class="cost" >
                                        <label class='radio-inline'>
                                            <div class="input-group" style="width: 330px;">
                                                <div class="input-group-addon" style="width: 120px;">会员最多参与次数</div>
                                                <input type="text" name="balance[recharge_activity_fetter]" class="form-control" value="{{ $balance['recharge_activity_fetter'] or -1 }}" placeholder=""/>
                                                <div class="input-group-addon">次</div>
                                            </div>
                                        </label>
                                        <label class='radio-inline'>
                                            <div class="search-select">
                                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('balance[recharge_activity_time]', [
                                                'starttime'=>date('Y-m-d H:i',$balance['recharge_activity_start'] ?: time()),
                                                'endtime'=>date('Y-m-d H:i',$balance['recharge_activity_end'] ?: strtotime('1 month')),
                                                'start'=>0,
                                                'end'=>0
                                                ], true) !!}
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='balance[proportion_status]' value='0' @if(empty($balance['proportion_status'])) checked @endif/>
                                    赠送固定金额
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='balance[proportion_status]' value='1' @if($balance['proportion_status'] == 1) checked @endif/>
                                    赠送充值比例
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <h4>
                                    充值满额送:
                                    <button type='button' class="btn btn-default" onclick='addRechargeItem()' style="margin-bottom:5px">
                                        <i class='fa fa-plus'></i> 增加优惠项
                                    </button>
                                </h4>


                                <div class='recharge-items'>
                                    @foreach( $balance['sale'] as $list)
                                    <div class="input-group recharge-item" style="margin-top:5px; width: 60%">
                                        <span class="input-group-addon">满</span>
                                        <input type="text" class="form-control" name='balance[enough][]' value='{{ $list['enough'] or '' }}'/>
                                        <span class="input-group-addon">赠送</span>
                                        <input type="text" class="form-control" name='balance[give][]' value='{{ $list['give'] or '' }}'/>
                                        <span class="input-group-addon unit">@if(empty($balance["proportion_status"])) 元 @else % @endif</span>
                                        <div class='input-group-btn'>
                                            <button class='btn btn-danger' type='button'
                                                    onclick="removeRechargeItem(this)"><i class='fa fa-remove'></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <span class="help-block">两项都填写才能生效</span>
                                <span class="help-block">赠送固定金额：充值满100，赠送10元,实际赠送10元</span>
                                <span class="help-block">赠送充值比例：充值满200，赠送15%，实际赠送30【200*15%】元</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额转账</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'><input type='radio' name='balance[transfer]' value='1' @if($balance['transfer'] ==1) checked @endif/>开启</label>
                            <label class='radio-inline'><input type='radio' name='balance[transfer]' value='0' @if($balance['transfer'] ==0) checked @endif/> 关闭</label>
                            <span class='help-block'>是否允许用户对账户余额进行转账</span>
                        </div>
                    </div>




                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额转换@if (app('plugins')->isEnabled('designer') == 1){{ LOVE_NAME }}@else'爱心值'@endif：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="balance[love_swich]" value="1" @if($balance['love_swich'] ==1 ) checked="checked" @endif>
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="balance[love_swich]" value="0" @if($balance['love_swich'] ==0 ) checked="checked" @endif >
                                关闭
                            </label>
                        </div>
                    </div>
                </div>
                <div id="love_rate" @if($balance['love_swich'] !=1 ) style="display:none" @endif >
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-4 col-xs-12">
                            <div class="input-group">
                                <div class="input-group-addon">余额转换比例</div>
                                <input type="text" name="balance[love_rate] " class="form-control" value="{{ $balance['love_rate']  or '0'}}" placeholder="0.00">
                                <div class="input-group-addon">%</div>
                            </div>
                            <div class="help-block">
                                转化实例:实际转化10个@if (app('plugins')->isEnabled('designer') == 1){{ LOVE_NAME }}@else'爱心值'@endif,余额转化比例10%，则需要10 / 10%，比例为空或为0则默认为1:1
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额定时提醒</label>
                <div class="col-sm-4 col-xs-6">
                    <!--原字段 name = trade[closerecharge] -->
                    <label class='radio-inline'>
                        <input type='radio' name='balance[sms_send]' value='1' @if($balance['sms_send'] == 1) checked @endif/>
                        开启
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='balance[sms_send]' value='0' @if($balance['sms_send'] == 0) checked @endif/>
                        关闭
                     </label>
                </div>
            </div>


           <div id="sms_send" @if($balance['sms_send'] !=1 ) style="display:none" @endif >
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">定时提醒设置</label>
                        <div class="input-group recharge-item" style="margin-top:5px; width: 30%">
                            <select name="balance[sms_hour]" class="form-control">
                                @foreach($day_data as $key => $week)
                                    <option value='{{ $key }}' @if($key == $balance['sms_hour']) selected @endif>{{  $week}}</option>
                                @endforeach
                            </select>
                            <span class="input-group-addon">点,金额超过</span>
                            <input type="text" class="form-control" name="balance[sms_hour_amount]" value="{{ $balance['sms_hour_amount']  or '0'}}">
                            <span class="input-group-addon unit"> 元 </span>
                       </div>
                <span style="margin-left: 18%" class="help-block">重新设置时间后一定要在一分钟后重启队列，若不重启该设置则会在第二天才生效</span>
            </div>

           </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>
        </form>
    </div>
    <script language='javascript'>
        $(function () {

            $(":radio[name='balance[love_swich]']").click(function () {

                if ($(this).val() == 1) {
                    $("#love_rate").show();
                }
                else {
                    $("#love_rate").hide();
                }
            });

            $(":radio[name='balance[sms_send]']").click(function () {

                if ($(this).val() == 1) {
                    $("#sms_send").show();
                }
                else {
                    $("#sms_send").hide();
                }
            });


            $(":radio[name='balance[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#recharge").show();
                }
                else {
                    $("#recharge").hide();
                }
            });
            $(":radio[name='balance[recharge_activity]']").click(function () {
                if ($(this).val() == 1 || $(this).val() == 2) {
                    $("#recharge_activity").show();
                }
                else {
                    $("#recharge_activity").hide();
                }
            });
            $(":radio[name='balance[proportion_status]']").click(function () {
                if ($(this).val() == 1) {
                    $(".unit").html('%');
                }
                else {
                    $(".unit").html('元');
                }
            });
        });
        function addRechargeItem() {
            var value  = $('input[name="balance[proportion_status]"]:checked').val();
            if (value == 1) {
                var unit = '%';
            } else {
                var unit = '元';
            }

            var html = '<div class="input-group recharge-item"  style="margin-top:5px; width: 60%;">';
            html += '<span class="input-group-addon">满</span>';
            html += '<input type="text" class="form-control" name="balance[enough][]"  />';
            html += '<span class="input-group-addon">赠送</span>';
            html += '<input type="text" class="form-control"  name="balance[give][]"  />';
            html += '<span class="input-group-addon unit">'+ unit +'</span>';
            html += '<div class="input-group-btn"><button type="button" class="btn btn-danger" onclick="removeRechargeItem(this)"><i class="fa fa-remove"></i></button></div>';
            html += '</div>';
            $('.recharge-items').append(html);
        }
        function removeRechargeItem(obj) {
            $(obj).closest('.recharge-item').remove();
        }


    </script>



@endsection