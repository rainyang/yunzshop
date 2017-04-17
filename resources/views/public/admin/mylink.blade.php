<!-- mylink start -->
<div id="modal-mylink" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 720px;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 5px;">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <ul class="nav nav-pills" role="tablist">
                    <li role="presentation" class="active" style="display: block;"><a aria-controls="link_system" role="tab" data-toggle="tab" href="#link_system" aria-expanded="true">系统页面</a></li>
                    <li role="presentation" style="display: block;"><a aria-controls="link_goods" role="tab" data-toggle="tab" href="#link_goods" aria-expanded="false">商品链接</a></li>
                    <li role="presentation" style="display: block;"><a aria-controls="link_cate" role="tab" data-toggle="tab" href="#link_cate" aria-expanded="false">商品分类</a></li>
                   {{--  {!! my_link_extra('nav') !!} --}}
                    <li role="presentation" style="display: block;"><a aria-controls="link_other" role="tab" data-toggle="tab" href="#link_other" aria-expanded="false">自定义链接</a></li>
                </ul>
            </div>
            <div class="modal-body tab-content">
                <div role="tabpanel" class="tab-pane link_system active" id="link_system">
                    <div class="mylink-con">
                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 商城页面链接</h4>
                        </div>
                        <div id="fe-tab-link-li-11" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 11)" data-href="{{ yzApiUrl('home') }}">商城首页</div>
                        <div id="fe-tab-link-li-12" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 12)" data-href="{{ yzApiUrl('category') }}">分类导航</div>
                        <div id="fe-tab-link-li-13" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 13)" data-href="{php echo $this->createMobileUrl('shop/list')}">全部商品</div>
                        <div id="fe-tab-link-li-14" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 14)" data-href="{php echo $this->createMobileUrl('shop/notice')}">公告页面</div>
                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i> 会员中心链接</h4>
                        </div>
                        <div id="fe-tab-link-li-21" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 21)" data-href="{{ yzApiUrl('member') }}">会员中心</div>
                        <div id="fe-tab-link-li-22" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 22)" data-href="{{ yzApiUrl('member.orderList') }}">我的订单</div>
                        <div id="fe-tab-link-li-23" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 23)" data-href="{{ yzApiUrl('cart') }}">我的购物车</div>
                        <div id="fe-tab-link-li-24" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 24)" data-href="{{ yzApiUrl('member.collection') }}">我的收藏</div>
                        <div id="fe-tab-link-li-25" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 25)" data-href="{{ yzApiUrl('member.footprint') }}">我的足迹</div>
                        <div id="fe-tab-link-li-26" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 26)" data-href="{{ yzApiUrl('member.balance_recharge') }}">会员充值</div>
                        <div id="fe-tab-link-li-27" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 27)" data-href="{{ yzApiUrl('member.details') }}">余额明细</div>
                        <div id="fe-tab-link-li-28" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 28)" data-href="{{ yzApiUrl('member.balance_withdrawal') }}">余额提现</div>
                        <div id="fe-tab-link-li-29" class="btn btn-default mylink-nav" ng-click="chooseLink(1, 29)" data-href="{{ yzApiUrl('member.address') }}">我的收货地址</div>



                    </div>
                </div>
                <div role="tabpanel" class="tab-pane link_goods" id="link_goods">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-good-kw" placeholder="请输入商品名称进行搜索 (多规格商品不支持一键下单)">
                        <span class="input-group-btn"><button type="button" class="btn btn-default" id="select-good-btn">搜索</button></span>
                    </div>
                    <div class="mylink-con" id="select-goods" style="height:266px;"></div>
                </div>
                <div role="tabpanel" class="tab-pane link_cate" id="link_cate">
                    <div class="mylink-con">
                        @foreach (\app\backend\modules\goods\models\Category::getAllCategory() as $goodcate_parent)
                            @if (empty($goodcate_parent['parentid']))
                                <div class="mylink-line">
                                    {{ $goodcate_parent['name'] }}
                                    <div class="mylink-sub">
                                        <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id']))}">选择</a>
                                    </div>
                                </div>

                                @foreach (\app\backend\modules\goods\models\Category::getAllCategory() as $goodcate_chlid)
                                    @if ($goodcate_chlid['parentid'] == $goodcate_parent['id'])
                                        <div class="mylink-line">
                                            <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                            {{ $goodcate_chlid['name'] }}
                                            <div class="mylink-sub">
                                                <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id'],'ccate'=>$goodcate2['id']))}">选择</a>
                                            </div>
                                        </div>
                                        @foreach (\app\backend\modules\goods\models\Category::getAllCategory() as $goodcate_third)
                                            @if ($goodcate_third['parentid'] == $goodcate_chlid['id'])
                                                <div class="mylink-line">
                                                    <span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                                    {{ $goodcate_third['name'] }}
                                                    <div class="mylink-sub">
                                                        <a href="javascript:;" class="mylink-nav" data-href="{php echo $this->createMobileUrl('shop/list',array('pcate'=>$goodcate['id'],'ccate'=>$goodcate2['id'],'tcate'=>$goodcate3['id']))}">选择</a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>

                {!! my_link_extra('content') !!}


                <div role="tabpanel" class="tab-pane link_cate" id="link_other">
                    <div class="mylink-con" style="height: 150px;">
                        <div class="form-group" style="overflow: hidden;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="line-height: 34px;">链接地址</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="mylink_href" class="form-control" style="height: 90px; resize: none;" placeholder="请以http://开头"></textarea>
                            </div>
                        </div>
                        <div class="form-group" style="overflow: hidden; margin-bottom: 0px;i">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="line-height: 34px;"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="btn btn-primary col-lg-1 mylink-nav2" style="margin-left: 20px; width: auto; overflow: hidden; margin-left: 0px;"> 插入 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- mylink end -->
<script language="javascript">
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

    $(document).on("click",".nav-link",function(){
        var id = $(this).data("id");
        if(id){
            $("#modal-mylink").attr({"data-id":id});
            $("#modal-mylink").modal();
        }
    });
    $(document).on("click",".mylink-nav",function(){
        var href = $(this).data("href");
        var id = $("#modal-mylink").attr("data-id");
        if(id){
            $("input[data-id="+id+"]").val(href);
            $("#modal-mylink").attr("data-id","");
        }else{
            console.log(href);
            ue.execCommand('link', {href:href});
        }
        $("#modal-mylink .close").click();
    });
    $(".mylink-nav2").click(function(){
        var href = $("textarea[name=mylink_href").val();
        if(href){
            var id = $("#modal-mylink").attr("data-id");
            if(id){
                $("input[data-id="+id+"]").val(href);
                $("#modal-mylink").attr("data-id","");
            }else{
                ue.execCommand('link', {href:href});
            }
            $("#modal-mylink .close").click();
            $("textarea[name=mylink_href").val("");
        }else{
            $("textarea[name=mylink_href").focus();
            alert("链接不能为空!");
        }
    });
    // ajax 选择商品
    $("#select-good-btn").click(function(){
        var kw = $("#select-good-kw").val();
        $.ajax({
            type: 'POST',
            url: "{!! yzWebUrl('goods.goods.getMyLinkGoods') !!}",
            data: {kw:kw},
            dataType:'json',
            success: function(data){
                console.log(data);
                $("#select-goods").html("");
                if(data){
                    $.each(data,function(n,value){
                        var html = '<div class="good">';
                        html+='<div class="img"><img src="'+value.thumb+'"/></div>'
                        html+='<div class="choosebtn">';
                        html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('shop/detail')}&id="+value.id+'">详情链接</a><br>';
                        if(value.hasoption==0){
                            html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('order/confirm')}&id="+value.id+'">下单链接</a>';
                        }
                        html+='</div>';
                        html+='<div class="info">';
                        html+='<div class="info-title">'+value.title+'</div>';
                        html+='<div class="info-price">原价:￥'+value.market_price+' 现价￥'+value.price+'</div>';
                        html+='</div>'
                        html+='</div>';
                        $("#select-goods").append(html);
                    });
                }
            }
        });
    });

</script>