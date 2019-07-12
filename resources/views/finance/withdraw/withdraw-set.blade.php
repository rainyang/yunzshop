@extends('layouts.base')

@section('content')
@section('title', trans('提现设置'))
<script>
    $(function () {
        $("#myTab li.active>a").css("background", "#ccc");
    })
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            $(this).css("background", "#ccc").parent().siblings().children().css("background", "none")
        })
    });
</script>
<style> .add-snav > li > a {
        height: 46px !important
    }</style>
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="main rightlist">
    {{--<div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>提现设置</a></li>
        </ul>
    </div>--}}
    <div>
        <ul class="add-shopnav" id="myTab">
            <li class="active"><a href="#tab_balance">余额提现</a></li>

            @foreach(Config::get('widget.withdraw') as $key=>$value)
                <li><a href="#{{$key}}">{{$value['title']}}</a></li>
            @endforeach

        </ul>
    </div>
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">


            <div class='panel-body'></div>

            <div class='panel-body'>

                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_balance">
                        {{--余额提现 start--}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额提现</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='withdraw[balance][status]' value='1'
                                           @if($set['status'] == 1) checked @endif />
                                    开启
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='withdraw[balance][status]' value='0'
                                           @if($set['status'] == 0) checked @endif />
                                    关闭
                                </label>
                                <span class='help-block'>是否允许用户将余额提出</span>
                            </div>
                        </div>
                        <div id='withdraw' @if(empty($set['status']))style="display:none"@endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="wechat">
                                        <label class='radio-inline' style="padding-left:0px">提现到微信</label>
                                    </div>
                                    <div class="switch">
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][wechat]' value='1'
                                                   @if($set['wechat'] == 1) checked @endif />
                                            开启
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][wechat]' value='0'
                                                   @if($set['wechat'] == 0) checked @endif />
                                            关闭
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline'>提现到支付宝</label>
                                    </div>
                                    <div class="switch">
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][alipay]' value='1'
                                                   @if($set['alipay'] == 1) checked @endif />
                                            开启
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][alipay]' value='0'
                                                   @if($set['alipay'] == 0) checked @endif />
                                            关闭
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @if(app('plugins')->isEnabled('huanxun'))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay">
                                            <label class='radio-inline'>提现到环迅支付</label>
                                        </div>
                                        <div class="switch">
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][huanxun]' value='1'
                                                       @if($set['huanxun'] == 1) checked @endif />
                                                开启
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][huanxun]' value='0'
                                                       @if($set['huanxun'] == 0) checked @endif />
                                                关闭
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(app('plugins')->isEnabled('eup-pay'))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay">
                                            <label class='radio-inline'>提现到EUP</label>
                                        </div>
                                        <div class="switch">
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][eup_pay]' value='1'
                                                       @if($set['eup_pay'] == 1) checked @endif />
                                                开启
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][eup_pay]' value='0'
                                                       @if($set['eup_pay'] == 0) checked @endif />
                                                关闭
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(app('plugins')->isEnabled('converge_pay'))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay">
                                            <label class='radio-inline'>提现到汇聚支付</label>
                                        </div>
                                        <div class="switch">
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][converge_pay]' value='1'
                                                       @if($set['converge_pay'] == 1) checked @endif />
                                                开启
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][converge_pay]' value='0'
                                                       @if($set['converge_pay'] == 0) checked @endif />
                                                关闭
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group" style="margin-bottom: 30px;">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline'>手动提现</label>
                                    </div>
                                    <div class="switch">
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][balance_manual]' value='1'
                                                   @if($set['balance_manual'] == 1) checked @endif />
                                            开启
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][balance_manual]' value='0'
                                                   @if($set['balance_manual'] == 0) checked @endif />
                                            关闭
                                        </label>
                                        <span class='help-block'>手动提现包含 银行卡、微信号、支付宝等三种类型，会员需要完善对应资料才可以提现</span>
                                    </div>
                                </div>
                            </div>

                            <div id='balance_manual' @if(empty($set['balance_manual']))style="display:none"@endif>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay">
                                            <label class='radio-inline'></label>
                                        </div>
                                        <div class="switch">
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][balance_manual_type]'
                                                       value='1'
                                                       @if(empty($set['balance_manual_type']) || $set['balance_manual_type'] == 1) checked @endif />
                                                银行卡
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][balance_manual_type]'
                                                       value='2' @if($set['balance_manual_type'] == 2) checked @endif />
                                                微信
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][balance_manual_type]'
                                                       value='3' @if($set['balance_manual_type'] == 3) checked @endif />
                                                支付宝
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline'>提现手续费</label>
                                    </div>
                                    <div class="switch">
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][poundage_type]' value='1'
                                                   @if($set['poundage_type'] == 1) checked @endif />
                                            固定金额
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][poundage_type]' value='0'
                                                   @if(empty($set['poundage_type'])) checked @endif />
                                            手续费比例
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline'></label>
                                    </div>
                                    <div class="cost">
                                        <label class='radio-inline'>
                                            <div class="input-group">
                                                <div class="input-group-addon" id="poundage_hint"
                                                     style="width: 120px;">@if($set['poundage_type'] == 1) 固定金额 @else
                                                        手续费比例 @endif</div>
                                                <input type="text" name="withdraw[balance][poundage]"
                                                       class="form-control" value="{{ $set['poundage'] or '' }}"
                                                       placeholder="请输入提现手续费计算值"/>
                                                <div class="input-group-addon" id="poundage_unit">@if($set['poundage_type'] == 1) 元 @else
                                                        % @endif</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline'></label>
                                    </div>
                                    <div class="cost">
                                        <label class='radio-inline'>
                                            <div class="input-group">
                                                <div class="input-group-addon" style="width: 120px;">满额减免手续费</div>
                                                <input type="text" name="withdraw[balance][poundage_full_cut]"
                                                       class="form-control"
                                                       value="{{ $set['poundage_full_cut'] or '' }}"
                                                       placeholder="提现金额达到 N元 减免手续费"/>
                                                <div class="input-group-addon">元</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="alipay">
                                        <label class='radio-inline' style="padding-left:0px">余额提现限制</label>
                                    </div>
                                    <div class="cost">
                                        <label class='radio-inline'>
                                            <div class="input-group">
                                                <input type="text" name="withdraw[balance][withdrawmoney]"
                                                       class="form-control" value="{{ $set['withdrawmoney'] or '' }}"
                                                       placeholder="余额提现最小金额值"/>
                                                <div class="input-group-addon">元</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane  active">
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现免审核</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][audit_free]' value='1' @if($set['audit_free'] == 1) checked @endif />
                                            开启
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' name='withdraw[balance][audit_free]' value='0' @if($set['audit_free'] == 0) checked @endif />
                                            关闭
                                        </label>
                                        <span class='help-block'>余额提现自动审核、自动打款（自动打款只支持提现到提现到汇聚支付一种方式！）</span>
                                    </div>
                                </div>
                            </div>


                        </div>
                        {{--余额提现 end--}}

                    </div>

                    @foreach(Config::get('widget.withdraw') as $key=>$value)
                        <div class="tab-pane" id="{{$key}}">{!! widget($value['class'])!!}</div>
                    @endforeach

                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="success-btn col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
<script language="javascript">


    $('.diy-notice').select2();

    $(function () {
        $(":radio[name='withdraw[balance][status]']").click(function () {
            if ($(this).val() == 1) {
                $("#withdraw").show();
            }
            else {
                $("#withdraw").hide();
            }
        });

        $(":radio[name='withdraw[balance][poundage_type]']").click(function () {
            if ($(this).val() == 1) {
                $("#poundage_unit").html('元');
                $("#poundage_hint").html('固定金额');
            }
            else {
                $("#poundage_unit").html('%');
                $("#poundage_hint").html('手续费比例')
            }
        });
        $(":radio[name='withdraw[balance][balance_manual]']").click(function () {
            if ($(this).val() == 1) {
                $("#balance_manual").show();
            }
            else {
                $("#balance_manual").hide();
            }
        });
    })
</script>

@endsection