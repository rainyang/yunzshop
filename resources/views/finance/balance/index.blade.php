@extends('layouts.base')
@section('title', '余额设置')
@section('content')

    <div class="main rightlist">
        {{--<div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#"> 余额设置</a></li>
            </ul>
        </div>--}}

        <form action="{{ yzWebUrl('finance.balance.index') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
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
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
    <script language='javascript'>
        $(function () {
            $(":radio[name='balance[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#recharge").show();
                }
                else {
                    $("#recharge").hide();
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