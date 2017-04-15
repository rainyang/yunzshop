@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method='post' class='form-horizontal'>
            <input type="hidden" name="op" value="detail">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="yun_shop" />
            <input type="hidden" name="p" value="coupon" />
            <input type="hidden" name="method" value="coupon" />
            <input type="hidden" name="op" value="post" />

            <div class='panel panel-default'>
                <ul class="add-shopnav" id="myTab">
                    <li class="active" ><a href="#tab_basic">基本信息</a></li>
                    <li><a href="#tab_coupon_type">优惠方式</a></li>
                    <li><a href="#tab_desc">使用说明</a></li>
                    <li><a href="#tab_message">推送消息</a></li>
                </ul>


                <div class='panel-body'>
                    <div class="tab-content">
                        <div class="tab-pane  active" id="tab_basic">@include('coupon.basic')</div>
                        <div class="tab-pane" id="tab_coupon_type">@include('coupon.coupontype')</div>
                        <div class="tab-pane" id="tab_desc">@include('coupon.desc')</div>
                        <div class="tab-pane" id="tab_message">@include('coupon.message')</div>
                    </div>

    <div class="form-group"></div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
            <input type="hidden" name="token" value="{{$var['token']}}" />
            <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default" />
        </div>
    </div>

{{--
    {template 'web/sysset/selectgoods'}
    {template 'web/sysset/selectcategory'}--}}
                    <script>
                        $(function() {
                            $("#chkoption").click(function() {
                                var obj = $(this);
                                if (obj.get(0).checked) {
                                    $("#tboption").show();
                                    $(".trp").hide();
                                }
                                else {
                                    $("#tboption").hide();
                                    $(".trp").show();
                                }
                            });
                        })

                        function addParam(type) {
                            var url = "{php echo $this->createWebUrl('shop/tpl')}&tpl="+type;
                            $.ajax({
                                "url": url,
                                success: function(data) {
                                    $('#param-items'+type).append(data);
                                }
                            });
                            return;
                        }
                        function deleteParam(o) {
                            $(o).parent().parent().remove();
                        }
                        function saveadd(o) {
                            $(o).parent().parent().remove();
                        }

                        function showbacktype(type){

                            $('.backtype').hide();
                            $('.backtype' + type).show();
                        }

                        function showusetype(type){
                            $('.usetype').hide();
                            $('.usetype' + type).show();
                        }
                        $(function(){
                            require(['bootstrap'], function () {
                                $('#myTab a').click(function (e) {
                                    e.preventDefault();
                                    $(this).tab('show');
                                })
                            });
                            $('form').submit(function(){

                                if($(':input[name=couponname]').isEmpty()){
                                    Tip.focus($(':input[name=couponname]'),'请输入优惠券名称!');
                                    return false;
                                }
                                var coupon_method = $(':radio[name=coupon_method]:checked').val();
                                if(coupon_method=='1'){
                                    if($(':input[name=deduct]').isEmpty()){
                                        Tip.focus($(':input[name=deduct]'),'请输入立减多少!');
                                        return false;
                                    }
                                }else if(coupon_method=='2'){
                                    if($(':input[name=discount]').isEmpty()){
                                        Tip.focus($(':input[name=discount]'),'请输入折扣多少!');
                                        return false;
                                    }
                                }else if(coupon_method=='3'){
                                    if($(':input[name=backcredit]').isEmpty() && $(':input[name=backmoney]').isEmpty() && $(':input[name=backredpack]').isEmpty()){
                                        Tip.focus($(':input[name=backcredit]'),'至少输入一种返利!');
                                        return false;
                                    }
                                }
                                return true;
                            })

                        })

                        {{--下面是新增的js--}}
                        function search_goods() {
                            if ($.trim($('#search-kwd-goods').val()) == '') {
                                Tip.focus('#search-kwd-goods', '请输入关键词');
                                return;
                            }
                            $("#module-menus-goods").html("正在搜索....");
                            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                                    keyword: $.trim($('#search-kwd-goods').val())
                                }, function (dat) {
                                    $('#module-menus-goods').html(dat);
                                }
                            )
                            ;
                        }
                        function select_good(o) {
                            $("#goodsid").val(o.id);
                            $("#goodsthumb").show();
                            $("#goodsthumb").find('img').attr('src', o.thumb);
                            $("#goods").val("[" + o.id + "]" + o.title);
                            $("#modal-module-menus-goods .close").click();
                        }
                    </script>
    </div>
    </div>
    </form>
    </div>
    </div>

@endsection('content')
