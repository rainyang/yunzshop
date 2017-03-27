@extends('layouts.base')

@section('content')

        <div class="main rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
                <input type='hidden' name='setid' value="{$set['id']}" />
                <input type='hidden' name='op' value="trade" />
                <div class="panel panel-default">
                    <div class="panel-heading">
                        余额设置
                    </div>
                    <div class="alert alert-warning">
                        余额支付开关、及其他支付设置，请到交易设置查看<a href="{{ yzWebUrl('setting.shop.pay') }}" target="_blank">【点击跳转交易设置】</a>.
                    </div>


                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启账户充值</label>
                            <div class="col-sm-9 col-xs-12">
                                <!--原字段 name = trade[closerecharge] -->
                                <label class='radio-inline'><input type='radio' name='balance[recharge]' value='1' @if($balance['recharge'] == 1)checked@endif/> 开启</label>
                                <label class='radio-inline'><input type='radio' name='balance[recharge]' value='0' @if($balance['recharge'] == 0)checked@endif/> 关闭</label>
                                <span class='help-block'>是否允许用户对账户余额进行充值</span>
                            </div>
                        </div>
                        <div id='recharge' {if empty($set['pay']['paypalstatus'])}style="display:none"{/if}>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <h4>充值满额送:</h4>
                                        <div class='recharge-items'>
                                            <div class="input-group recharge-item" style="margin-top:5px; width: 60%">
                                                <span class="input-group-addon">满</span>
                                                <input type="text" class="form-control" name='enough[]' value='{$item['enough']}' />
                                                <span class="input-group-addon">赠送</span>
                                                <input type="text" class="form-control"  name='give[]' value='{$item['give']}' />
                                                <span class="input-group-addon">元</span>
                                                <div class='input-group-btn'>
                                                    <button class='btn btn-danger' type='button' onclick="removeRechargeItem(this)"><i class='fa fa-remove'></i></button>
                                                </div>

                                            </div>
                                        </div>

                                        <div style="margin-top:5px">
                                            <button type='button' class="btn btn-default" onclick='addRechargeItem()' style="margin-bottom:5px"><i class='fa fa-plus'></i> 增加优惠项</button>
                                        </div>
                                        <span class="help-block">两项都填写才能生效，赠送的余额可以固定数或比例(带%)号</span>
                                        <span class="help-block">例如：充值满100，赠送10</span>
                                        <span class="help-block">例如：充值满200，赠送15%，实际赠送30(200*15%)</span>
                                </div>
                            </div>
                        </div>

                    </div>




                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class='input-group'>
                                        <input type="text" name="trade[poundage]" class="form-control" value="{$set['trade']['poundage']}" onkeyup="value=value.replace(/[^\d.]/g,'');if(value >= 100){value=''}" onafterpaste="value=value.replace(/[^\d.]/g,'');if(value >= 100){value=''"/>
                                        <span class='input-group-addon'>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                                 <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现自动打款限制</label>
                                 <div class="col-sm-9 col-xs-12">
                                     {ifp 'sysset.save.trade'}
                                     <input type="text" name="trade[withdrawautomoney]" class="form-control" value="{$set['trade']['withdrawautomoney']}" />
                                     <span class='help-block'>提现金额少于等于自定义的限制金额，则提现不需要审核，否则需要审核。</span>
                                     {else}
                                     <input type="hidden" name="trade[withdrawautomoney]" value="{$set['trade']['withdrawautomoney']}"/>
                                     <div class='form-control-static'>
                                         {if empty($set['trade']['withdrawautomoney'])}不限制{else}{$set['trade']['withdrawautomoney']} 元 {/if}
                                     </div>
                                     {/if}
                                 </div>
                             </div> 删除功能-->

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现限制</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="trade[withdrawmoney]" class="form-control" value="{$set['trade']['withdrawmoney']}" />
                                <span class='help-block'>余额满多少才能提现,空或0不限制</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                            <input type="hidden" name="token" value="{$_W['token']}" />
                        </div>
                    </div>




        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启账户充值</label>
            <div class="col-sm-9 col-xs-12">
                <label class='radio-inline'><input type='radio' name='trade[closerecharge]' value='0' {if empty($set['trade']['closerecharge'])}checked{/if}/> 开启</label>
                <label class='radio-inline'><input type='radio' name='trade[closerecharge]' value='1' {if $set['trade']['closerecharge']=='1'}checked{/if} /> 关闭</label>
                <span class='help-block'>是否允许用户对账户余额进行充值</span>
            </div>
        </div>




        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额转账</label>
            <div class="col-sm-9 col-xs-12">
                <label class='radio-inline'><input type='radio' name='trade[transfer]' value='0' {if empty($set['trade']['transfer'])}checked{/if}/> 开启</label>
                <label class='radio-inline'><input type='radio' name='trade[transfer]' value='1' {if $set['trade']['transfer']=='1'}checked{/if} /> 关闭</label>
                <span class='help-block'>是否允许用户对账户余额进行转账</span>
            </div>
        </div>



                </div>
            </form>
        </div>
        <script language='javascript'>

            function addRechargeItem(){
                var html= '<div class="input-group recharge-item"  style="margin-top:5px; width: 60%;">';
                html+='<span class="input-group-addon">满</span>';
                html+='<input type="text" class="form-control" name="enough[]"  />';
                html+='<span class="input-group-addon">赠送</span>';
                html+='<input type="text" class="form-control"  name="give[]"  />';
                html+='<span class="input-group-addon">元</span>';
                html+='<div class="input-group-btn"><button type="button" class="btn btn-danger" onclick="removeRechargeItem(this)"><i class="fa fa-remove"></i></button></div>';
                html+='</div>';
                $('.recharge-items').append(html);
            }
            function removeRechargeItem(obj){
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
                })
                $(":radio[name='pay[weixin_jie]']").click(function () {
                    if ($(this).val() == 1) {
                        $("#jie").show();
                    }
                    else {
                        $("#jie").hide();
                    }
                })
                $(":radio[name='pay[paypalstatus]']").click(function () {
                    if ($(this).val() == 1) {
                        $("#paypal").show();
                    }
                    else {
                        $("#paypal").hide();
                    }
                })
                $(":radio[name='pay[yeepay]']").click(function () {
                    if ($(this).val() == 1) {
                        $("#yeepay_set").show();
                    }
                    else {
                        $("#yeepay_set").hide();
                    }
                })

                $(":radio[name='pay[alipay_withdrawals]']").click(function () {
                    if ($(this).val() == 1) {
                        $("#alipay_withdrawals").show();
                    }
                    else {
                        $("#alipay_withdrawals").hide();
                    }
                })

            })
        </script>


@endsection