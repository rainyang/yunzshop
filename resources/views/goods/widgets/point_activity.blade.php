@if($is_open == 1)
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分活动奖励</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[point_activity][status]' value='1' @if($data->status == '1') checked="checked" @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[point_activity][status]' value='0' @if(empty($data->status))  checked="checked" @endif/>
            关闭
        </label>
    </div>
</div>
@endif