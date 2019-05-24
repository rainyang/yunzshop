
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否支持退换货</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<label class='radio-inline'>--}}
                {{--<input type='radio' name='widgets[service][is_refund]' value='1' @if($service->is_refund == '1') checked @endif/>--}}
                {{--开启--}}
            {{--</label>--}}
            {{--<label class='radio-inline'>--}}
                {{--<input type='radio' name='widgets[service][is_refund]' value='0' @if($service->is_refund == '0') checked @endif/>--}}
                {{--关闭--}}
            {{--</label>--}}
            {{--<!-- <span class='help-block'></span> -->--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">服务费设置</label>
        <div class="col-xs-12 col-sm-9 col-md-10">
            <div class='input-group col-md-3'>
                @if($open == 1)
                <input type="text" name="widgets[service][service_fee]" value="{{ $service->serviceFee }}" class="form-control"/>
                <span class="input-group-addon">元</span>
                @else
                    <input type="text" name="widgets[service][service_fee]" value="{{ $service->serviceFee }}" class="form-control" readonly/>
                    {{--<span class="input-group-addon">元</span>--}}
                    <p>已关闭服务费</p>
                @endif
            </div>
        </div>
    </div>
<script language='javascript'>

</script>


