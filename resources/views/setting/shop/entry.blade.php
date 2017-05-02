@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商城入口</a></li>
            </ul>
        </div>
        <!-- 商城入口二维码 -->
        <div class="panel-body">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#A" data-toggle="tab">商城页面链接</a></li>
                <li><a href="#B" data-toggle="tab">会员中心链接</a></li>
                <li><a href="#C" data-toggle="tab">我的推广链接</a></li>
            </ul>

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="A">
                    <ul class="row dimension">
                        <li>
                            <p>商城首页</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('home')) !!}
                        </li>
                        <li>
                            <p>分类导航</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('category')) !!}
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="B">
                    <ul class="dimension">
                        <li>
                            <p>会员中心</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member')) !!}
                        </li>
                        <li>
                            <p>我的订单</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/orderList/0')) !!}
                        </li>
                        <li>
                            <p>购物车</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('cart')) !!}
                        </li>
                        <li>
                            <p>我的收藏</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/collection')) !!}
                        </li>
                        <li>
                            <p>我的足迹</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/footprint')) !!}
                        </li>
                        <li>
                            <p>评价</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/myEvaluation')) !!}
                        </li>
                        <li>
                            <p>关系</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/myrelationship')) !!}
                        </li>
                        <li>
                            <p>收货地址</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/address')) !!}
                        </li>
                        <li>
                            <p>我的优惠券</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('coupon/coupon_index')) !!}
                        </li>
                        <li>
                            <p>领券中心</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('coupon/coupon_store')) !!}
                        </li>
                        <li>
                            <p>积分页面</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/integral')) !!}
                        </li>
                        <li>
                            <p>积分明细</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/integrallist')) !!}
                        </li>
                        <li>
                            <p>余额页面</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/balance')) !!}
                        </li>
                        <li>
                            <p>余额明细</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/detailed')) !!}
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="C">
                    <ul class="dimension">
                        <li>
                            <p>推广中心</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/extension')) !!}
                        </li>
                        <li>
                            <p>收入明细</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/incomedetails')) !!}
                        </li>
                        <li>
                            <p>收入提现</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/income')) !!}
                        </li>
                        <li>
                            <p>提现明细</p>
                            {!! QrCode::size(200)->generate(yzAppFullUrl('member/presentationRecord')) !!}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
 <style type="text/css">
    .panel-body>.nav-tabs>li{
        width:33%;
        float:left;
    }
    .panel-body>.nav-tabs>li>a{
        border:none;
        border-top:3px solid transparent;
        color:#333;
        background:#e0e0e0;
    }
    .panel-body>.nav-tabs>li.active>a{
        color:hsl(24,100%,50%);
        border:none;
        border-top:3px solid hsl(24,100%,50%);

     }
    .dimension>li{
        width:300px;
        float:left;
        text-align:center;
    }
    .dimension>li>img{
        width:200px;
        height:200px;
        border:1px solid #aaa;
    }
    .dimension>li>p{
        text-align:center;
        font-weight:bold;
     }
 </style>

@endsection