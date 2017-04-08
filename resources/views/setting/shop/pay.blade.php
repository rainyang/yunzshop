@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
<div class="right-titpos">
	<ul class="add-snav">
		<li class="active"><a href="#">支付设置</a></li>
	</ul>
</div>
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            {{--<div class='alert alert-info'>
                在开启以下支付方式前，请到 <a href='{php echo url('profile/payment')}'>支付选项</a> 去设置好参数。
            </div>--}}
            <div class="alert alert-warning">
                易宝支付，含银联，信用卡等多种支付方式, PC版支付成功后台通知请登录商户后台添加通知地址,<a href="http://www.yeepay.com/" target="_blank">申请及详情请查看这里</a>.
            </div>
        
        <!-- weixin支付设置 _start -->
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付</label>
                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'><input type='radio' name='pay[weixin]' value='1' @if ($set['weixin'] == 1) checked @endif/> 开启</label>
                    <label class='radio-inline'><input type='radio' name='pay[weixin]' value='0' @if ($set['weixin'] == 0) checked @endif /> 关闭</label>
                </div>
            </div>
            <div id='certs' @if (empty($set['weixin'])) style="display:none" @endif>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">身份标识<br>(appId)</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" class="form-control" name="pay[weixin_appid]" value="{{ @$set['weixin_appid'] }}" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">身份密钥<br>(appSecret)</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" class="form-control" name="pay[weixin_secret]" value="{{ @$set['weixin_secret'] }}" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付商户号<br>(mchId)</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" class="form-control"  name="pay[weixin_mchid]" value="{{ @$set['weixin_mchid'] }}" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付商户号<br>(apiSecret)</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" class="form-control"  name="pay[weixin_apisecret]" value="{{ @$set['weixin_apisecret'] }}" autocomplete="off">
                    </div>
                </div>

                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_cert]" value="{{ $set['weixin_cert'] }}"/>
                        <input type="file" name="weixin_cert_file" class="form-control" />
                        <span class="help-block">
                            @if (!empty($set['weixin_cert']))
                            <span class='label label-success'>已上传</span>
                            @else
                            <span class='label label-danger'>未上传</span>
                            @endif
                            下载证书 cert.zip 中的 apiclient_cert.pem 文件</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">KEY密钥文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_key]"  value="{{ $set['weixin_key'] }}"/>

                        <input type="file" name="weixin_key_file" class="form-control" />
                        <span class="help-block">
                           @if (!empty($set['weixin_key']))
                            <span class='label label-success'>已上传</span>
                            @else
                            <span class='label label-danger'>未上传</span>
                            @endif
                            下载证书 cert.zip 中的 apiclient_key.pem 文件
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOT文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_root]" value="{{ $set['weixin_root'] }}"/>

                        <input type="file" name="weixin_root_file" class="form-control" />
                        <span class="help-block">
                          @if (!empty($set['weixin_root']))
                            <span class='label label-success'>已上传</span>
                            @else
                            <span class='label label-danger'>未上传</span>
                            @endif
                            下载证书 cert.zip 中的 rootca.pem 文件 
                        </span>
                    </div>
                </div>
            </div>

        <!-- 借用微信支付设置 _start -->
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">借用微信支付</label>
                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'><input type='radio' name='pay[weixin_jie]' value='1' @if ( $set['weixin_jie']==1)checked @endif/> 开启</label>
                    <label class='radio-inline'><input type='radio' name='pay[weixin_jie]' value='0' @if ( $set['weixin_jie']==0)checked @endif /> 关闭</label>
                    <span class='help-block'>开启借号微信支付，微信支付功能将失效</span>
                </div>

            </div>
            <div id='jie' @if ( empty($set['pay']['weixin_jie'])) style="display:none" @endif>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">公众号(AppId)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_appid]" class="form-control" value="{{ $set['weixin_jie_appid'] }}"/>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">微信支付商户号(Mch_Id)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_mchid]" class="form-control" value="{{ $set['weixin_jie_mchid'] }}"/>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label must">微信支付密钥(APIKEY)</label>
                    <div class="col-sm-9">
                        <input type="text" name="pay[weixin_jie_apikey]" class="form-control" value="{{ $set['weixin_jie_apikey'] }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">CERT证书文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_jie_cert]" value="{{ $data['weixin_jie_cert'] }}"/>

                        <input type="file" name="weixin_jie_cert_file" class="form-control" />
                                    <span class="help-block">
                                        @if (!empty($sec['jie_cert']))
                                        <span class='label label-success'>已上传</span>
                                        @else
                                        <span class='label label-danger'>未上传</span>
                                        @endif
                                        下载证书 cert.zip 中的 apiclient_cert.pem 文件</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">KEY密钥文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_jie_key]"  value="{{ $data['weixin_jie_key'] }}"/>
                        <input type="file" name="weixin_jie_key_file" class="form-control" />
                                    <span class="help-block">
                                       @if (!empty($sec['jie_key']))
                                        <span class='label label-success'>已上传</span>
                                        @else
                                        <span class='label label-danger'>未上传</span>
                                        @endif
                                        下载证书 cert.zip 中的 apiclient_key.pem 文件
                                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">ROOT文件</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="pay[weixin_jie_root]" value="{{ $data['weixin_jie_root'] }}"/>
                        <input type="file" name="weixin_jie_root_file" class="form-control" />
                                    <span class="help-block">
                                      @if ( !empty($sec['jie_root']))
                                        <span class='label label-success'>已上传</span>
                                        @else
                                        <span class='label label-danger'>未上传</span>
                                        @endif
                                        下载证书 cert.zip 中的 rootca.pem 文件
                                    </span>
                    </div>
                </div>
            </div>

<!-- paypal支付设置 _start -->

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">Paypal支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[paypal_status]' value='1' @if ( $set['paypal_status'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[paypal_status]' value='0' @if ( $set['paypal_status'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div id='paypal' @if ( empty($set['paypal_status'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户号：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_mchid]" value="{{ $set['paypal_mchid'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户密码：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_key]" value="{{ $set['paypal_key'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">商户支付密钥：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_signkey]" value="{{ $set['paypal_signkey'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">支付币种：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_currency]" value="{{ $set['paypal_currency'] }}"/></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div style="float:left; width:15%; height:30px;">
                                <label class='radio-inline'  style="padding-left:0px">汇率：</label>
                            </div>
                            <div style="float:left; width:85%; height:30px;">
                                <label class='radio-inline' style="width:70%;"><input class="col-sm-6" style="width:100%;" type="text" name="pay[paypal_currencies]" value="{{ $set['paypal_currencies'] }}"/></label>
                            </div>
                        </div>
                    </div>

                </div>
<!-- paypal支付设置 _end -->

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[alipay]' value='1' @if ( $set['alipay'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[alipay]' value='0' @if ( $set['alipay'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>

                <div id='alipay_block' @if (empty($set['alipay'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">收款支付宝账号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" class="form-control" name="pay[alipay_account]" value="{{ @$set['alipay_account'] }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">合作者身份</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" class="form-control" name="pay[alipay_partner]" value="{{ @$set['alipay_partner'] }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">校验密钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" class="form-control"  name="pay[alipay_secret]" value="{{ @$set['alipay_secret'] }}" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">易宝支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[yeepay]' value='1' @if ( $set['yeepay'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[yeepay]' value='0' @if ( $set['yeepay'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div id='yeepay_set' @if (empty($set['yeepay'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户编号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantaccount]" value="{{ $set['yeepay_merchantaccount'] }}"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户密钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantKey]" value="{{ $set['yeepay_merchantKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户私钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantPrivateKey]" value="{{ $set['yeepay_merchantPrivateKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户RSA公钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_merchantPublicKey]" value="{{ $set['yeepay_merchantPublicKey'] }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">易宝RSA公钥</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[yeepay_PublicKey]" value="{{ $set['yeepay_PublicKey'] }}"/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额支付</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[credit]' value='1' @if ( $set['credit'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[credit]' value='0' @if ( $set['credit'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">货到付款</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[cash]' value='1' @if ( $set['cash'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[cash]' value='0' @if ( $set['cash'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝提现</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[alipay_withdrawals]' value='1' @if ( $set['alipay_withdrawals'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[alipay_withdrawals]' value='0' @if ( $set['alipay_withdrawals'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
                <div id='alipay_withdrawals' @if ( empty($set['alipay_withdrawals'])) style="display:none" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款账号:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[alipay_number]" value="{{ $set['alipay_number'] }}"/>
                        </div>  
                    </div>   
                    <div class="form-group">              
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款账户名：</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="col-sm-6" type="text" name="pay[alipay_name]" value="{{ $set['alipay_name'] }}"/>
                  
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信红包提现</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='pay[weixin_withdrawals]' value='1' @if ( $set['weixin_withdrawals'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='pay[weixin_withdrawals]' value='0' @if ( $set['weixin_withdrawals'] == 0) checked @endif /> 关闭</label>
                    </div>
                </div>
            <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary"  />
                     </div>
            </div>

            </div>
            <script language="javascript">
                $(function () {
                    $(":radio[name='pay[weixin]']").click(function () {
                        if ($(this).val() == 1) {
                            $("#certs").show();
                        }
                        else {
                            $("#certs").hide();
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
                    $(":radio[name='pay[paypal_status]']").click(function () {
                        if ($(this).val() == 1) {
                            $("#paypal").show();
                        }
                        else {
                            $("#paypal").hide();
                        }
                    })
                    $(":radio[name='pay[alipay]']").click(function () {
                        if ($(this).val() == 1) {
                            $("#alipay_block").show();
                        }
                        else {
                            $("#alipay_block").hide();
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
    </form>
</div>
</div>
@endsection