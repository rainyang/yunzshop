
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ SZ_YI_INTEGRAL }}抵扣</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <span class="input-group-addon">最多抵扣</span>
            <input type="text" name="widgets[sale][max_point_deduct]"  value="{{ $item->max_point_deduct }}" class="form-control" />
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">如果设置0，则不支持{{ SZ_YI_INTEGRAL }}抵扣</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额抵扣</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <span class="input-group-addon">最多抵扣</span>
            <input type="text" name="widgets[sale][max_balance_deduct]"  value="{{ $item->max_balance_deduct }}" class="form-control" />
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">如果设置0，则支持全额抵扣，设置-1 不支持余额抵扣</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满件包邮</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <span class="input-group-addon">满</span>
            <input type="text" name="widgets[sale][ed_num]"  value="{{ $item->ed_num }}" class="form-control" />
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">如果设置0或空，则不支持满件包邮</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满额包邮</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <span class="input-group-addon">满</span>
            <input type="text" name="widgets[sale][ed_money]"  value="{{ $item->ed_money }}" class="form-control" />
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">如果设置0或空，则不支持满额包邮</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不参与单品包邮地区</label>
    <div class="col-sm-9 col-xs-12">
        <div id="areas" class="form-control-static">{{ $item->ed_areas }}</div>
        <a href="javascript:;" class="btn btn-default selectareas" onclick="selectAreas()" >添加不参加满包邮的地区</a>
        <input type="hidden" id='selectedareas' name="widgets[sale][ed_areas]" value="{{ $item->ed_areas }}" />
        <span class="help-block">如果设置0或空，则不支持满件包邮</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送积分</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <input type="text" name="widgets[sale][point]"  value="{{ $item->point }}" class="form-control" />
            <span class="input-group-addon">积分</span>
        </div>
        <span class="help-block">如果设置0，则不赠送</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">红包</label>
    <div class="col-sm-4">
        <div class='input-group'>
            <input type="text" name="widgets[sale][bonus]"  value="{{ $item->bonus }}" class="form-control" />
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">如果设置0，则不发放红包</span>
    </div>
</div>

@include('area.selectprovinces')