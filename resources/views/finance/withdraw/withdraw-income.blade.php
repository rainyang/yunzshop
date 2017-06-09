<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <div class="wechat">
            <label class='radio-inline' style="padding-left:0px">提现到余额</label>
        </div>
        <div class="switch">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][balance]' value='1' @if($set['balance'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][balance]' value='0' @if($set['balance'] == 0) checked @endif />
                关闭
            </label>
        </div>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <div class="wechat">
            <label class='radio-inline' style="padding-left:0px">提现到微信</label>
        </div>
        <div class="switch">
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
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <div class="alipay">
            <label class='radio-inline'>提现到支付宝</label>
        </div>
        <div class="switch">
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

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <div class="alipay">
            <label class='radio-inline'>手动提现</label>
        </div>
        <div class="switch">
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][manual]' value='1' @if($set['manual'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='withdraw[income][manual]' value='0' @if($set['manual'] == 0) checked @endif />
                关闭
            </label>
        </div>
    </div>
</div>