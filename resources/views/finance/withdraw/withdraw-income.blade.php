<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到余额</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][balance]' value='1' @if($set['balance'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][balance]' value='0' @if($set['balance'] == 0) checked @endif />
                关闭
            </label>
            <span class='help-block'>可开启独立手续费设置，独立手续费：提现到余额的收入(不含收银台)，计算比例按照独立手续费中的比例计算【优先级高于插件独立设置】</span>
        </div>
    </div>
</div>



<div id='withdraw_income_balance' @if(empty($set['balance']))style="display:none"@endif>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <div class="alipay" >
                <label class='radio-inline' >独立手续费</label>
            </div>
            <div class="switch">
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[income][balance_special]' value='1' @if($set['balance_special'] == 1) checked @endif />
                    开启
                </label>
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[income][balance_special]' value='0' @if($set['balance_special'] == 0) checked @endif />
                    关闭
                </label>
            </div>
        </div>
    </div>

    <div id='balance_special' @if(empty($set['balance_special']))style="display:none"@endif>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <div class="alipay" >
                    <label class='radio-inline' >提现手续费</label>
                </div>
                <div class="cost" >
                    <label class='radio-inline'>
                        <div class="input-group">
                            <input type="text" name="withdraw[income][special_poundage]" class="form-control" value="{{ $set['special_poundage'] or '' }}" placeholder="提现至余额独立手续费比例"/>
                            <div class="input-group-addon">%</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <div class="alipay" >
                    <label class='radio-inline' style="padding-left:0px">提现劳务税</label>
                </div>
                <div class="cost" >
                    <label class='radio-inline'>
                        <div class="input-group">
                            <input type="text" name="withdraw[income][special_service_tax]" class="form-control" value="{{ $set['special_service_tax'] or '' }}" placeholder="提现至余额独立劳务税比例"/>
                            <div class="input-group-addon">%</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到微信</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][wechat]' value='1' @if($set['wechat'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][wechat]' value='0' @if($set['wechat'] == 0) checked @endif />
                关闭
            </label>
        </div>
    </div>
</div>

<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到支付宝</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][alipay]' value='1' @if($set['alipay'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][alipay]' value='0' @if($set['alipay'] == 0) checked @endif />
                关闭
            </label>
        </div>
    </div>
</div>

<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">手动提现</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][manual]' value='1' @if($set['manual'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][manual]' value='0' @if($set['manual'] == 0) checked @endif />
                关闭
            </label>
            <span class='help-block'>手动提现包含 银行卡、微信号、支付宝等三种类型，会员需要完善对应资料才可以提现</span>
        </div>
    </div>
</div>

<div id='manual_type' @if(empty($set['manual']))style="display:none"@endif>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <div class="switch">
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[income][manual_type]' value='1' @if($set['manual_type'] == 1 || $set['balance_special'] == 1 || empty($set['manual_type'])) checked @endif />
                    银行卡
                </label>
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[income][manual_type]' value='2' @if($set['manual_type'] == 2) checked @endif />
                    微信
                </label>
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[income][manual_type]' value='3' @if($set['manual_type'] == 3) checked @endif />
                    支付宝
                </label>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">劳务税比例</label>
        <div class="col-sm-9 col-xs-12">
            <div class="input-group">
                <input type="text" name="withdraw[income][servicetax_rate]" class="form-control" value="{{ $set['servicetax_rate'] or '' }}" placeholder="请输入提现劳务税比例"/>
                <div class="input-group-addon">%</div>
            </div>
        </div>
    </div>
</div>


<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">收入提现免审核</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][free_audit]' value='1' @if($set['free_audit'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][free_audit]' value='0' @if($set['free_audit'] == 0) checked @endif />
                关闭
            </label>
            <span class='help-block'>收入提现自动审核、自动打款（自动打款只支持提现到余额、提现到微信两种方式！）</span>
        </div>
    </div>
</div>





<script language="javascript">
    $(function () {
        $(":radio[name='withdraw[income][balance]']").click(function () {
            if ($(this).val() == 1) {
                $("#withdraw_income_balance").show();
            }
            else {
                $("#withdraw_income_balance").hide();
            }
        });
        $(":radio[name='withdraw[income][balance_special]']").click(function () {
            if ($(this).val() == 1) {
                $("#balance_special").show();
            }
            else {
                $("#balance_special").hide();
            }
        });
        $(":radio[name='withdraw[income][manual]']").click(function () {
            if ($(this).val() == 1) {
                $("#manual_type").show();
            }
            else {
                $("#manual_type").hide();
            }
        });
    })
</script>