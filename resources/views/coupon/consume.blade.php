<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠方式</label>
    <div class="col-sm-9 col-xs-12">
        <input type="hidden" name="coupontype" value="0"/>
        <label class="radio-inline " ><input type="radio" name="backtype" onclick='showbacktype(0)' value="0" @if($coupon['backtype']==0)checked @endif>立减</label>
        <label class="radio-inline"><input type="radio" name="backtype" onclick='showbacktype(1)' value="1" @if($coupon['backtype']==1)checked @endif>打折</label>
        <label class="radio-inline "><input type="radio" name="backtype" onclick='showbacktype(2)' value="2" @if($coupon['backtype']==2)checked @endif>返利</label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

    <div class="col-sm-2 backtype backtype0" @if($coupon['backtype']!=0)style='display:none' @endif>
    <div class='input-group'>
        <span class='input-group-addon'>立减</span>
        <input style="width: 120px" type='text' class='form-control' name='deduct' value="{{$coupon['deduct']}}"/>
        <span class='input-group-addon'>元</span>
    </div>
</div>
<div class="col-sm-2 backtype backtype1"  @if($coupon['backtype']!=1)style='display:none' @endif>
<div class='input-group'>
    <span class='input-group-addon'>打</span>
    <input type='text' class='form-control' name='discount'  placeholder='0.1-10' value="{{$coupon['discount']}}"/>
    <span class='input-group-addon'>折</span>
</div>   </div>
<div class="col-sm-5 backtype backtype2"  @if($coupon['backtype']!=2)style='display:none' @endif>
<div class='input-group'>
    <span class='input-group-addon'>返</span>
    <input type='text' class='form-control' name='backmoney' value="{{$coupon['backmoney']}}"/>
    <span class='input-group-addon'>余额 返</span>
    <input type='text' class='form-control' name='backcredit' value="{{$coupon['backcredit']}}"/>
    <span class='input-group-addon'>积分 返</span>
    <input type='text' class='form-control'  name='backredpack'  value="{{$coupon['backredpack']}}"/>
    <span class='input-group-addon'>现金</span>
</div>
　<span class='help-block'>带%为返消费金额的百分比: 如10% ，消费200元，返20元，反现金，需要商户平台有钱，并需要上传微信证书</span>
</div>

</div>


<div class="form-group backtype backtype2"  @if($coupon['backtype']!=2)style='display:none' @endif>
<label class="col-xs-12 col-sm-3 col-md-2 control-label">返利方式</label>
<div class="col-sm-9 col-xs-12" >
    <label class="radio-inline" >
        <input type="radio" name="backwhen" value="0" @if($coupon['backwhen'] == 0)checked="true" @endif /> 交易完成后（过退款期限自动返利）
    </label>

    <label class="radio-inline"'>
    <input type="radio" name="backwhen" value="1" @if($coupon['backwhen'] == 1)checked="true" @endif /> 订单完成后（收货后）
    </label>
    <label   class="radio-inline" >
        <input type="radio" name="backwhen" value="2" @if($coupon['backwhen'] == 2)checked="true" @endif  /> 订单付款后
    </label>

</div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">退还方式</label>
    <div class="col-sm-9 col-xs-12" >
        <label class="radio-inline">
            <input type="radio" name="returntype" value="0" @if($coupon['returntype'] == 0)checked="true" @endif  onclick="$('.returntype').hide()"/> 不可退还
        </label>
        <label class="radio-inline">
            <input type="radio" name="returntype" value="1" @if($coupon['returntype'] == 1)checked="true" @endif onclick="$('.returntype').show()" /> 下单取消可退还
        </label>

        <span class='help-block'>会员使用过的优惠券在订单取消或退款后是否自动退回到会员账户</span>

    </div>
</div>				         