@extends('layouts.base')

@section('content')
@section('title', trans('手机归属地统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">手机归属地统计</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            {{--<table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>--}}
                {{--<tr class='trhead'>--}}
                    {{--<td colspan='8' style="text-align: left;">--}}
                        {{--<p>数量: <span id="total">{{$total}}</span>&nbsp;&nbsp;&nbsp;订单总金额: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;订单成本总额: <span id="total">{{$total}}</span></p>--}}
                        {{--<p>分销佣金: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;经销商提成: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;区域分红: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;微店分红: <span id="total">{{$total}}元</span></p>--}}
                        {{--<p>招商员分红: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;招商中心分红: <span id="total">{{$total}}元</span>&nbsp;&nbsp;&nbsp;积分奖励: <span id="total">{{$total}}</span>&nbsp;&nbsp;&nbsp;爱心值奖励: <span id="total">{{$total}}</span></p>--}}
                        {{--<p>预计总利润: <span id="total">{{$total}}元</span></p>--}}
                        {{--<p>1、订单成本：平台订单为商品成本+运费，供应商、门店订单为供应商、门店结算金额；</p>--}}
                        {{--<p>2、分销佣金、经销商提成、区域分红、微店分红、招商员分红、招商中心分红为该订单在这种方式的总分红金额求和。</p>--}}
                        {{--<p>3、预计收益=订单金额-订单成本-分销佣金-经销商提成-区域分红-微店分红-招商员分红-招商中心分红</p>--}}
                        {{--<p>4、状态：未完成为已支付但未完成的订单，已完成订单完成的状态，已退款的时候更新状态。</p>--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--</table>--}}

            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th>11</th>
                            <th>11</th>
                            {{--<th class="col-md-2 " style="width: 100% !important;display: block !important;">北京</th>--}}
                            {{--<th class="col-md-2 ">上海</th>--}}
                            {{--<th class="col-md-2 text-center">天津</th>--}}
                            {{--<th class="col-md-2 text-center">重庆</th>--}}
                            {{--<th class="col-md-2 text-center">辽宁</th>--}}
                            {{--<th class="col-md-2 text-center">吉林</th>--}}
                            {{--<th class="col-md-2 text-center">黑龙江</th>--}}
                            {{--<th class="col-md-2 text-center">河北</th>--}}
                            {{--<th class="col-md-2 text-center">山西</th>--}}
                            {{--<th class="col-md-2 text-center">陕西</th>--}}
                            {{--<th class="col-md-2 text-center">甘肃</th>--}}
                            {{--<th class="col-md-2 text-center">青海</th>--}}
                            {{--<th class="col-md-2 text-center">山东</th>--}}
                            {{--<th class="col-md-2 text-center">安徽</th>--}}
                            {{--<th class="col-md-2 text-center">江苏</th>--}}
                            {{--<th class="col-md-2 text-center">浙江</th>--}}
                            {{--<th class="col-md-2 text-center">河南</th>--}}
                            {{--<th class="col-md-2 text-center">湖北</th>--}}
                            {{--<th class="col-md-2 text-center">湖南</th>--}}
                            {{--<th class="col-md-2 text-center">江西</th>--}}
                            {{--<th class="col-md-2 text-center">台湾</th>--}}
                            {{--<th class="col-md-2 text-center">福建</th>--}}
                            {{--<th class="col-md-2 text-center">云南</th>--}}
                            {{--<th class="col-md-2 text-center">海南</th>--}}
                            {{--<th class="col-md-2 text-center">四川</th>--}}
                            {{--<th class="col-md-2 text-center">贵州</th>--}}
                            {{--<th class="col-md-2 text-center">广东</th>--}}
                            {{--<th class="col-md-2 text-center">内蒙古</th>--}}
                            {{--<th class="col-md-2 text-center">新疆</th>--}}
                            {{--<th class="col-md-2 text-center">广西</th>--}}
                            {{--<th class="col-md-2 text-center">西藏</th>--}}
                            {{--<th class="col-md-2 text-center">宁夏</th>--}}
                            {{--<th class="col-md-2 text-center">香港</th>--}}
                            {{--<th class="col-md-2 text-center">澳门</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>22</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td>22</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td>22</td>
                            <td>22</td>
                        </tr>
                        @foreach($list['data'] as $row)
                            <tr style="height: 40px; text-align: center">
                                <td>{{date('Y-m-d H:i',$row['created_at'])}}<br>{{ $row['order_sn'] }}</td>
                                <td style="word-wrap:break-word; white-space: pre-wrap">{{$row['address']['address']}}</td>
                                <td >{{$row['belongs_to_member']['nickname']}}<br>{{ $row['belongs_to_recommender']['recommender'] ? $row['belongs_to_recommender']['recommender']['nickname'] : '总店'}}</td>
                                <td>@if($row['is_plugin'] == 1)供应商：{{$row['has_one_supplier']['supplier']['username']}}
                                    @elseif($row['plugin_id'] == 31)门店：{{ $row['has_one_cashier']['cashier']['store_name'] }}
                                    @elseif($row['plugin_id'] == 32)门店：{{ $row['has_one_store']['store']['store_name'] }}
                                    @else 平台自营
                                    @endif</td>
                                <td>{{$row['price']}}<br>{{ $row['has_one_order_goods']['total_cost_price'] }}</td>
                                <td>{{$row['has_one_commission']['total_price'] ?: 0}}<br>{{ $row['has_one_team_dividend']['total_price'] ?: 0 }}</td>
                                <td>{{$row['has_one_area_dividend']['total_price'] ?: 0}}<br>{{ $row['has_one_micro_shop']['total_price'] ?: 0 }}</td>
                                <td>{{$row['has_one_merchant']['total_price'] ?: 0}}<br>{{ $row['has_one_merchant_center']['total_price'] ?: 0 }}</td>
                                <td>{{$row['has_one_point']['total_point'] ?: 0}}<br>{{ $row['has_one_love']['total_love'] ?: 0 }}</td>
                                <td>{{$row['has_one_commission']['total_price'] + $row['has_one_team_dividend']['total_price'] + $row['has_one_area_dividend']['total_price'] + $row['has_one_micro_shop']['total_price'] + $row['has_one_merchant']['total_price'] + $row['has_one_merchant_center']['total_price']}}</td>
                                <td>
                                    @if($row['status'] == '3')
                                        已完成
                                    @else
                                        未完成
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @include('order.modals')
            <div id="pager">{!! $pager !!}</div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
{{--<script language='javascript'>--}}
    {{--var province_id = $('#province_id').val();--}}
    {{--var city_id = $('#city_id').val();--}}
    {{--var district_id = $('#district_id').val();--}}
    {{--var street_id = $('#street_id').val();--}}
    {{--cascdeInit(province_id, city_id, district_id, street_id);--}}
{{--</script>--}}
@endsection
