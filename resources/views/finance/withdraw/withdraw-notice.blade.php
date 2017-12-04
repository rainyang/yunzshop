{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">任务处理通知</label>--}}
    {{--<div class="col-sm-9 col-xs-12">--}}
        {{--<input type="text" name="withdraw[notice][template_id]" class="form-control" value="{{$set['template_id']}}"/>--}}
    {{--</div>--}}
{{--</div>--}}

@if(YunShop::notice()->getNotSend('withdraw.incone_withdraw_title'))
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现申请通知</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<input type="text" name="withdraw[notice][incone_withdraw_title]" class="form-control"--}}
                   {{--value="{{$set['incone_withdraw_title']}}"/>--}}
            {{--<div class="help-block">标题，默认"提现申请通知"</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<textarea name="withdraw[notice][incone_withdraw]"--}}
                      {{--class="form-control">{{$set['incone_withdraw']}}</textarea>--}}
            {{--模板变量: [昵称] [时间] [收入类型] [金额] [手续费] [提现方式]--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现申请通知</label>
            <div class="col-sm-9 col-xs-12">
                <select name='withdraw[notice][incone_withdraw]' class='form-control diy-notice'>
                    <option value="" @if(!$set['incone_withdraw']) selected @endif >
                        请选择消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['incone_withdraw'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif

@if(YunShop::notice()->getNotSend('withdraw.incone_withdraw_check_title'))
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现审核通知</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<input type="text" name="withdraw[notice][incone_withdraw_check_title]" class="form-control"--}}
                   {{--value="{{$set['incone_withdraw_check_title']}}"/>--}}
            {{--<div class="help-block">标题，默认"提现审核通知"</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
        {{--<textarea name="withdraw[notice][incone_withdraw_check]"--}}
                  {{--class="form-control">{{$set['incone_withdraw_check']}}</textarea>--}}
            {{--模板变量: [昵称] [时间] [收入类型] [状态] [金额] [手续费] [审核通过金额] [提现方式]--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现审核通知</label>
            <div class="col-sm-9 col-xs-12">
                <select name='withdraw[notice][incone_withdraw_check]' class='form-control diy-notice'>
                    <option value="" @if(!$set['incone_withdraw_check']) selected @endif >
                        请选择消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['incone_withdraw_check'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.incone_withdraw_pay_title'))
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现打款通知</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<input type="text" name="withdraw[notice][incone_withdraw_pay_title]" class="form-control"--}}
                   {{--value="{{$set['incone_withdraw_pay_title']}}"/>--}}
            {{--<div class="help-block">标题，默认"提现打款通知"</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
        {{--<textarea name="withdraw[notice][incone_withdraw_pay]"--}}
                  {{--class="form-control">{{$set['incone_withdraw_pay']}}</textarea>--}}
            {{--模板变量: [昵称] [时间] [收入类型] [状态] [金额] [提现方式]--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现打款通知</label>
            <div class="col-sm-9 col-xs-12">
                <select name='withdraw[notice][incone_withdraw_pay]' class='form-control diy-notice'>
                    <option value="" @if(!$set['incone_withdraw_pay']) selected @endif >
                        请选择消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['incone_withdraw_pay'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.incone_withdraw_arrival_title'))
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到账通知</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<input type="text" name="withdraw[notice][incone_withdraw_arrival_title]" class="form-control"--}}
                   {{--value="{{$set['incone_withdraw_arrival_title']}}"/>--}}
            {{--<div class="help-block">标题，默认"提现到账通知"</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
        {{--<textarea name="withdraw[notice][incone_withdraw_arrival]"--}}
                  {{--class="form-control">{{$set['incone_withdraw_arrival']}}</textarea>--}}
            {{--模板变量: [昵称] [时间] [收入类型] [金额] [提现方式]--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到账通知</label>
            <div class="col-sm-9 col-xs-12">
                <select name='withdraw[notice][incone_withdraw_arrival]' class='form-control diy-notice'>
                    <option value="" @if(!$set['incone_withdraw_arrival']) selected @endif >
                        请选择消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['incone_withdraw_arrival'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
