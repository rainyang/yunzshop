
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现申请通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw'])) value="{{$set['income_withdraw']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_check_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现审核通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_check]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_check'])) value="{{$set['income_withdraw_check']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_check'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_check" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_check']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_pay_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现打款通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_pay]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_pay'])) value="{{$set['income_withdraw_pay']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_pay'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_pay" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_pay']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_arrival_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到账通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_arrival]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_arrival'])) value="{{$set['income_withdraw_arrival']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_arrival'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_arrival" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_arrival']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
<script>
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "withdraw.notice";
        var select_name = "select[name='withdraw[notice][" + name + "]']"
        var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
        var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
        var postdata = {
            notice_name: name,
            setting_name: setting_name
        };
        if ($(id).is(':checked')) {
            //开
            $.post(url_open,postdata,function(data){
                if (data) {
                    $(select_name).find("option:selected").val(data.id)
                    showPopover($(id),"开启成功")
                }
            }, "json");
        } else {
            //关
            $.post(url_close,postdata,function(data){
                $(select_name).val('');
                showPopover($(id),"关闭成功")
            }, "json");
        }
    }
    function showPopover(target, msg) {
        target.attr("data-original-title", msg);
        $('[data-toggle="tooltip"]').tooltip();
        target.tooltip('show');
        target.focus();
        //2秒后消失提示框
        setTimeout(function () {
                target.attr("data-original-title", "");
                target.tooltip('hide');
            }, 2000
        );
    }
</script>

