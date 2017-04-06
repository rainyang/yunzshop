
<div class='panel-body'>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否使用统一说明 </label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline" >
                <input type="radio" name="coupon[descnoset]" value="0" @if($coupon['descnoset'] == 0)checked="true" @endif /> 使用
            </label>

            <label class="radio-inline">
                <input type="radio" name="coupon[descnoset]" value="1" @if($coupon['descnoset'] == 1)checked="true" @endif /> 不使用
            </label>
            <span class='help-block'>统一说明在<a href="" target='_blank'>【基础设置】</a>中设置，如果使用统一说明，则在优惠券说明前面显示统一说明</span>
        </div>
    </div>


    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用说明</label>
        <div class="col-sm-9 col-xs-12">
            {!! app\common\helpers\UeditorHelper::tpl_ueditor('coupon[desc]', $coupon['desc']) !!}
        </div>
    </div>
</div>
<div class='panel-heading'>
    推送消息 (发放或用户从领券中心获得后的消息推送，如果标题为空就不推送消息)
</div>