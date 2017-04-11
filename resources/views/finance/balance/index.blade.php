@extends('layouts.base')

@section('content')

    <div class="main rightlist">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#"> 余额设置</a></li>
            </ul>
        </div>

        <form action="{{ yzWebUrl('finance.balance.index') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default">

                <div class="alert alert-warning">
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
                                <h4>
                                    充值满额送:
                                    <button type='button' class="btn btn-default" onclick='addRechargeItem()' style="margin-bottom:5px">
                                        <i class='fa fa-plus'></i> 增加优惠项
                                    </button>
                                </h4>

                                <div class='recharge-items'>
                                    <div class="input-group recharge-item" style="margin-top:5px; width: 60%">
                                        <span class="input-group-addon">满</span>
                                        <input type="text" class="form-control" name='balance[enough][]' value=' '/>
                                        <span class="input-group-addon">赠送</span>
                                        <input type="text" class="form-control" name='balance[give][]' value=' '/>
                                        <span class="input-group-addon">元</span>
                                        <div class='input-group-btn'>
                                            <button class='btn btn-danger' type='button'
                                                    onclick="removeRechargeItem(this)"><i class='fa fa-remove'></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                                <span class="help-block">两项都填写才能生效，赠送的余额可以固定数或比例(带%)号</span>
                                <span class="help-block">例如：充值满100，赠送10</span>
                                <span class="help-block">例如：充值满200，赠送15%，实际赠送30(200*15%)</span>
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

        function addRechargeItem() {
            var html = '<div class="input-group recharge-item"  style="margin-top:5px; width: 60%;">';
            html += '<span class="input-group-addon">满</span>';
            html += '<input type="text" class="form-control" name="balance[enough][]"  />';
            html += '<span class="input-group-addon">赠送</span>';
            html += '<input type="text" class="form-control"  name="balance[give][]"  />';
            html += '<span class="input-group-addon">元</span>';
            html += '<div class="input-group-btn"><button type="button" class="btn btn-danger" onclick="removeRechargeItem(this)"><i class="fa fa-remove"></i></button></div>';
            html += '</div>';
            $('.recharge-items').append(html);
        }
        function removeRechargeItem(obj) {
            $(obj).closest('.recharge-item').remove();
        }


    </script>
    <script language="javascript">
        $(function () {
            $(":radio[name='balance[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#recharge").show();
                }
                else {
                    $("#recharge").hide();
                }
            });
            $(":radio[name='balance[withdraw][status]']").click(function () {
                if ($(this).val() == 1) {
                    $("#withdraw").show();
                }
                else {
                    $("#withdraw").hide();
                }
            });
        })
    </script>


@endsection