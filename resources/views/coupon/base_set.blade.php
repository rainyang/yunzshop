@extends('layouts.base')
@section('title', '优惠券设置')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')

        <form action="{{ yzWebUrl('coupon.base-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>


                <div class='panel-heading'>基础设置</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券使用限制：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[is_singleton]" value="1" @if ($coupon['is_singleton'] == 1) checked="checked" @endif />
                                单张
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[is_singleton]" value="0" @if ($coupon['is_singleton'] == 0) checked="checked" @endif />
                                多张
                            </label>
                            <div class="help-block">
                                选中单张时每个订单最多只能使用一张优惠券
                            </div>
                        </div>
                    </div>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券转让：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="1" @if ($coupon['transfer'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="0" @if ($coupon['transfer'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券转让：会员之间可以转让自己拥有的优惠券。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">抵扣奖励积分：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="1" @if ($coupon['award_point'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="0" @if ($coupon['award_point'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券抵扣金额奖励等值积分，如优惠券抵扣 10元则奖励 10积分。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知设置：</label>
                        <div class="col-sm-4 col-xs-6">
                            <select name='coupon[coupon_notice]' class='form-control diy-notice'>
                                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($coupon['coupon_notice'])) value="{{$coupon['coupon_notice']}}"
                                        selected @else value="" @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($coupon['coupon_notice'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2 col-xs-6">
                            <input class="mui-switch mui-switch-animbg" id="coupon_notice" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($coupon['coupon_notice']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>
                </div>





                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <script>
        function message_default(name) {
            var id = "#" + name;
            var setting_name = "coupon." + name;
            var select_name = "select[name='coupon[" + name + "]']"
            var url_open = "{!! yzWebUrl('setting.default-notice.store') !!}"
            var url_close = "{!! yzWebUrl('setting.default-notice.storeCancel') !!}"
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
    <script>
        $('.diy-notice').select2();
    </script>


@endsection

