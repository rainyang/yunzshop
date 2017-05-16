<div class='panel-body'>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送标题</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_title]" class="form-control" value="{{$coupon['resp_title']}}"  />
            <span class="help-block">
                例如: 恭喜您获取了优惠券
                <br>变量: [nickname] 为"会员昵称", [couponname] 为"优惠券名称", [validtime] 为"有效期",样式类似这样"2018/11/11 - 2018/11/18"
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
                默认: 亲爱的 [nickname], 您已经获取 1 张优惠券 "[couponname]", 有效期是 [validtime], 请及时使用".
                <br>变量: [nickname] 为"会员昵称", [couponname] 为"优惠券名称", [validtime] 为"有效期",样式类似这样"2018/11/11 - 2018/11/18"
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送链接</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_url]" class="form-control" value="{{$coupon['resp_url']}}"  />
            {{--<span class='help-block'>消息推送点击的连接，为空默认为优惠券详情</span>--}}
        </div>
    </div>
</div>