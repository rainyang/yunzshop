@extends('layouts.base')

@section('content')
@section('title', trans('订单分润'))

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
                        <h4 class="card-title">订单分润</h4>
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-4">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="订单号查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="店铺名称查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4" style="padding-bottom: 15px">
                                <input type="hidden" id="province_id" value="{{ $address->province_id?:0 }}"/>
                                <input type="hidden" id="city_id" value="{{ $address->city_id?:0 }}"/>
                                <input type="hidden" id="district_id" value="{{ $address->district_id?:0 }}"/>
                                <input type="hidden" id="street_id" value="{{ $address->street_id?:0 }}"/>
                                {!! app\common\helpers\AddressHelper::tplLinkedAddress(['address[province_id]','address[city_id]','address[district_id]','address[street_id]'], [])!!}
                            </div>
                            {{--<br><br><br>--}}
                            <div class="form-group col-xs-12 col-sm-3">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="购买者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3">
                                <input type="text" class="form-control"  name="search[recommend_name]" value="{{$search['recommend_name']?$search['recommend_name']:''}}" placeholder="推荐者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class='form-group col-xs-12 col-sm-3'>
                                <select name="search[status]" class="form-control">
                                    <option value=""
                                            @if($search['status'] == '')  selected="selected"@endif>
                                        订单状态
                                    </option>
                                    <option value="1"
                                            @if($search['status'] == '1')  selected="selected"@endif>
                                        未完成
                                    </option>
                                    <option value="3"
                                            @if($search['status'] == '3')  selected="selected"@endif>
                                        已完成
                                    </option>
                                </select>
                            </div>
                            <div class='form-group col-xs-12 col-sm-3'>
                                <select name="search[status]" class="form-control">
                                    <option value=""
                                            @if($search['status'] == '')  selected="selected"@endif>
                                        是否统计
                                    </option>
                                    <option value="1"
                                            @if($search['status'] == '1')  selected="selected"@endif>
                                        是
                                    </option>
                                    <option value="3"
                                            @if($search['status'] == '3')  selected="selected"@endif>
                                        否
                                    </option>
                                </select>
                            </div>
                            <div class='form-group col-xs-12 col-sm-6'>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="search[is_time]" value="1"
                                               @if($search['is_time'] == '1')checked="checked"@endif>
                                    </span>
                                    {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                            'starttime'=>$search['time']['start'],
                                                                            'endtime'=>$search['time']['end'],
                                                                            'start'=>0,
                                                                            'end'=>0
                                                                            ], true)!!}
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                <tr class='trhead'>
                    <td colspan='8' style="text-align: left;">
                        订单数: <span id="total">{{$total}}</span>
                    </td>
                </tr>
            </table>

            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-4 text-center" style="white-space: pre-wrap;">时间<br>订单号</th>
                            <th class="col-md-4 text-center">订单区域</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">购买者<br>推荐者</th>
                            <th class="col-md-4 text-center">店铺</th>
                            <th class="col-md-2 text-center">订单金额<br>订单成本</th>
                            <th class="col-md-2 text-center">分销拥挤<br>经销商提成</th>
                            <th class="col-md-2 text-center">区域分红<br>微店分红</th>
                            <th class="col-md-3 text-center">招商员分红<br>招商中心分红</th>
                            <th class="col-md-2 text-center">积分奖励<br>爱心值奖励</th>
                            <th class="col-md-2 text-center">预计利润</th>
                            <th class="col-md-2 text-center">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                            <tr style="height: 40px; text-align: center">
                                <td>{{date('Y-m-d H:i',$row['created_at'])}}<br>{{ $row['order_sn'] }}</td>
                                <td style="word-wrap:break-word; white-space: pre-wrap">{{$row['address']['address']}}</td>
                                <td >{{$row['belongs_to_member']['nickname']}}<br>{{ $row['belongs_to_member']['nickname'] }}</td>
                                <td>{{$row['created_at']}}</td>
                                <td>{{$row['price']}}<br>{{ $row['has_one_order_goods']['total_cost_price'] }}</td>
                                <td>{{$row['recommend_name']}}</td>
                                <td>{{$row['goods_price']}}</td>
                                <td>{{$row['price']}}</td>
                                <td>
                                    @if($row['status'] == '3')
                                        已完成
                                    @else
                                        未完成
                                    @endif
                                </td>
                                <td>{{$row['price']}}</td>
                                <td>{{$row['price']}}</td>
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
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);
</script>
@endsection
