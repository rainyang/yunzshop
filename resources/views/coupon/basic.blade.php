<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
    <div class="col-sm-5">
        <input type="text" name="display_order" class="form-control" value="{{$coupon['display_order']}}"  />
        <span class='help-block'>数字越大越靠前</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 优惠券名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="name" class="form-control" value="{{$coupon['name']}}"  />
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类</label>
    <div class="col-sm-9 col-xs-12">

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">缩略图</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('thumb', $coupon['thumb']) !!}
    </div>
</div>