<div class="form-group coupon">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品赠送优惠券</label>
    <div class="col-sm-9 col-md-10">
        <input type='hidden' id='coupon_id' name='widgets[coupon][coupon_id]' value="{{ $item->coupon_id }}"/>
        <div class='input-group'>
            <input type="text" name="coupon" maxlength="30"
                   value="@if (!empty($coupon)) {{ $coupon->name }}  @endif"
                   id="coupon" class="form-control" readonly/>
            <div class='input-group-btn'>
                <button class="btn btn-default" type="button"
                        onclick="popwin = $('#modal-module-menus-coupon').modal();">选择优惠券
                </button>
                <button class="btn btn-danger" type="button"
                        onclick="$('#coupon_id').val('');$('#coupon').val('');">清除选择
                </button>
            </div>
        </div>
        <span class="help-block">单品下单赠送指定优惠券</span>

        <div id="modal-module-menus-coupon" class="modal fade" tabindex="-1">
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h3>选择优惠券</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd-coupon"
                                       placeholder="请输入优惠券名称"/>
                                <span class='input-group-btn'><button type="button" class="btn btn-default"
                                                                      onclick="search_coupons();">搜索</button></span>
                            </div>
                        </div>
                        <div id="module-menus-coupon" ></div>
                    </div>
                    <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"
                                                 aria-hidden="true">关闭</a></div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <label class="radio-inline">
                <input type="radio" name="widgets[coupon][send_times]" value="0" checked="checked" />
                <span>每月</span>
                <span>默认每月1号 0:00</span>
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发放次数</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <div class='input-group-addon'>连续发放</div>
            <input type='text' name='widgets[coupon][send_num]' class="form-control"
                   value="{{$set['delayed']}}"/>
            <div class='input-group-addon'>月</div>
        </div>
    </div>
</div>



<script language='javascript'>

    function search_coupons() {
        if ($('#search-kwd-coupon').val() == '') {
            Tip.focus('#search-kwd-coupon', '请输入关键词');
            return;
        }
        $("#module-menus-coupon").html("正在搜索....");
        $.get("{!! yzWebUrl('coupon.coupon.get-search-coupons') !!}", {
            keyword: $.trim($('#search-kwd-coupon').val())
        }, function (dat) {
            $('#module-menus-coupon').html(dat);
        });
    }
    function select_coupon(o) {
        $("#coupon_id").val(o.id);
        $("#coupon").val(o.name);
        $("#modal-module-menus-coupon .close").click();
    }

</script>


