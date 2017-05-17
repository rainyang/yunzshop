<div class='panel-body'>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送标题</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_title]" class="form-control" value="{{$coupon['resp_title']}}"  />
            <span class="help-block">
                例如: 亲爱的 [nickname], 恭喜您获得了优惠券
                <br>变量: [nickname] 为"会员昵称", [couponname] 为"优惠券名称"
                <br><span style="font-weight:bold">注意:</span> 如果标题为空则不推送消息
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送封面</label>
        <div class="col-sm-9 col-xs-12">
            {!! app\common\helpers\ImageHelper::tplFormFieldImage('coupon[resp_thumb]', $coupon['resp_thumb']) !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送说明</label>
        <div class="col-sm-9 col-xs-12">
            <textarea name="coupon[resp_desc]" class='form-control'>{{$coupon['resp_desc']}}</textarea>
            <span class="help-block">
                默认: 亲爱的 [nickname], 您获得了 1 张 "[couponname]" 优惠券.
                <br>变量: [nickname] 为"会员昵称", [couponname] 为"优惠券名称"
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送链接</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_url]" class="form-control" value="{{$coupon['resp_url']}}"  />
            <span class='help-block'>默认为商城首页链接</span>
        </div>
    </div>
</div>