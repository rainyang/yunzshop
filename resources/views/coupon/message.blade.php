<div class='panel-body'>
    <div class="form-group">
        <div>
            推送消息 (用户从领券中心领取或者商城手动发放给用户后的消息推送，如果标题为空就不推送消息)
        </div>
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送标题</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_title]" class="form-control" value="{{$coupon['resp_title']}}"  />
            <span class="help-block">变量 [nickname] 会员昵称</span>
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
            <span class="help-block">变量 [nickname] 会员昵称</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送链接</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="coupon[resp_url]" class="form-control" value="{{$coupon['resp_url']}}"  />
            <span class='help-block'>消息推送点击的连接，为空默认为优惠券详情</span>
        </div>
    </div>
</div>