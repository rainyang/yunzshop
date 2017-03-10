@extends('layouts.admin')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    @include('setting.shop.tabs')
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>  

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信设置</label>
                    <div class="col-sm-9 col-xs-12">
                        @if (!$set['type']) {{ $set['type'] = 1 }} @endif
                        <label class='radio-inline sms_type' type="1"><input type='radio' name='sms[type]' value='1' @if  ($set['type'] == 1) checked @endif/> 互亿无线</label>
                        <label class='radio-inline sms_type' type="2"><input type='radio' name='sms[type]' value='2' @if  ($set['type'] == 2) checked @endif /> 阿里大鱼</label>
                    </div>
                </div>

                <div id="sms1" @if ($set['type'] == 2) class="hide" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信账号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[account]" class="form-control" value="{{ $set['account'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信密码</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[password]" class="form-control" value="{{ $set['password'] }}" />
                        </div>
                    </div>
                </div>

                <div id="sms2"  @if ($set['type'] == 1) class="hide" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12 alert alert-info">
                            请到 <a href='http://www.alidayu.com/' taget="_blank">阿里大鱼</a> 去申请开通,短信模板中必须包含code和product,请参考默认用户注册验证码设置。
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppKey:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[appkey]" class="form-control" value="{{ $set['appkey'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">secret:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[secret]" class="form-control" value="{{ $set['secret'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信签名:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[signname]"
                            class="form-control"
                            value="{{ $set['signname'] }}" placeholder="例如: 注册验证" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册短信模板ID:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[templateCode]" class="form-control" value="{{ $set['templateCode'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册模板变量:</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name="sms[product]" class="form-control">
                            @if (!empty($set['product']))
                                {{ $set['product'] }}

                            @else
                                product=xx商城
                            @endif
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找回密码短信模板ID:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[templateCodeForget]" class="form-control" value="{{ $set['templateCodeForget'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找回密码变量:</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name="sms[forget]" class="form-control">
                            @if (!empty($set['forget']))
                                {{ $set['forget'] }}
                            @else
                                product=xx商城
                            @endif
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12 alert alert-info">
                            模板变量请以"变量名=变量值"形式填写,多个值请以回车换行。
                        </div>
                    </div>
                </div>
                <!--
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用于测试短信接口的手机号</label>
                    <div class="col-sm-9 col-xs-12">
                        {ifp 'sysset.save.sms'}
                        <input type="text" name="sms[password]" class="form-control" value="{$set['sms']['password']}" />
                        {else}
                        <input type="hidden" name="sms[password]" value="{$set['sms']['password']}"/>
                        <div class='form-control-static'>{$set['sms']['password']}</div>
                        {/if}
                    </div>
                </div>
                -->
              <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                     </div>
            </div>
                       
            </div>
        </div>     
    </form>
</div>
</div>
<script>
$(function(){
    $('.sms_type').click(function(){
        var type = $(this).attr('type');
        $('#sms1').hide();
        $('#sms2').hide();
        $('#sms'+type).removeClass('hide').show();
    });
});
</script>
@endsection
