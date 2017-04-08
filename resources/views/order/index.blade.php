@extends('layouts.base')

@section('content')

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <script type="text/javascript" src="../addons/sz_yi/static/js/dist/jquery.gcjs.js"></script>
        <script type="text/javascript" src="../addons/sz_yi/static/js/dist/jquery.form.js"></script>
        <script type="text/javascript" src="../addons/sz_yi/static/js/dist/tooltipbox.js"></script>

        <div class="rightlist">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">订单管理</a></li>
                </ul>
            </div>
            <div class="panel panel-info" >
                <div class="panel-body">
                    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="sz_yi"/>
                        <input type="hidden" name="do" value="order" id="form_do"/>
                        <input type="hidden" name="route" value="{{$url}}" id="form_p"/>

                            <div class="">
                                @section('search_bar')
                                    <div class='form-group col-sm-8 col-lg-5 col-xs-12'>
                                        <select name="search[ambiguous][field]" id="ambiguous-field"
                                                class="form-control" >
                                            <option value="order"
                                                    @if(array_get($requestSearch,'ambiguous.field','') =='order')  selected="selected"@endif >
                                                订单号/支付号
                                            </option>
                                            <option value="member"
                                                    @if( array_get($requestSearch,'ambiguous.field','')=='member')  selected="selected"@endif>
                                                用户姓名/ID/昵称/手机号
                                            </option>
                                            <option value="order_goods"
                                                    @if( array_get($requestSearch,'ambiguous.field','')=='order_goods')  selected="selected"@endif>
                                                商品名称/ID
                                            </option>
                                            <option value="dispatch"
                                                    @if( array_get($requestSearch,'ambiguous.field','')=='dispatch')  selected="selected"@endif>
                                                快递单号
                                            </option>
                                        </select>
                                        <input  class="form-control" name="search[ambiguous][string]" type="text"
                                               value="{{array_get($requestSearch,'ambiguous.string','')}}"
                                               placeholder="订单号/支付单号">
                                    </div>
                                    <div class='form-group form-group col-sm-8 col-lg-2 col-xs-12'>

                                        <select name="search[pay_type]" class="form-control">
                                            <option value=""
                                                    @if( array_get($requestSearch,'pay_type',''))  selected="selected"@endif>
                                                支付方式
                                            </option>
                                            <option value="1"
                                                    @if( array_get($requestSearch,'pay_type','') == '1')  selected="selected"@endif>
                                                在线支付
                                            </option>
                                            <option value="2"
                                                    @if( array_get($requestSearch,'pay_type','') == '2')  selected="selected"@endif>
                                                货到付款
                                            </option>
                                            <option value="3"
                                                    @if( array_get($requestSearch,'pay_type','') == '3')  selected="selected"@endif>
                                                余额支付
                                            </option>
                                        </select>
                                    </div>
                                    <div class='form-group col-sm-8 col-lg-5 col-xs-12'>

                                        <select name="search[time_range][field]" class="form-control form-time">
                                            <option value=""
                                                    @if( array_get($requestSearch,'time_range.field',''))selected="selected"@endif >
                                                操作时间
                                            </option>
                                            <option value="create_time"
                                                    @if( array_get($requestSearch,'time_range.field','')=='create_time')  selected="selected"@endif >
                                                下单
                                            </option>
                                            <option value="pay_time"
                                                    @if( array_get($requestSearch,'time_range.field','')=='pay_time')  selected="selected"@endif>
                                                付款
                                            </option>
                                            <option value="send_time"
                                                    @if( array_get($requestSearch,'time_range.field','')=='send_time')  selected="selected"@endif>
                                                发货
                                            </option>
                                            <option value="finish_time"
                                                    @if( array_get($requestSearch,'time_range.field','')=='finish_time')  selected="selected"@endif>
                                                完成
                                            </option>
                                        </select>
                                        {!! tpl_form_field_daterange(
                                            'search[time_range]',
                                            array(
                                                'starttime'=>array_get($requestSearch,'time_range.start',0),
                                                'endtime'=>array_get($requestSearch,'time_range.end',0),
                                                'start'=>0,
                                                'end'=>0
                                            ),
                                            true
                                            )!!}

                                    </div>
                                @show
                            </div>

                        <div class="form-group">

                            <div class="col-sm-7 col-lg-9 col-xs-12">
                                <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                                <input type="hidden" name="token" value="{{$var['token']}}"/>
                                <button type="button" name="export" value="1" id="export" class="btn btn-primary">导出
                                    Excel
                                </button>
                                @if( $requestSearch['plugin'] != "fund")
                                    <a class="btn btn-warning"
                                       href="{php echo $this->createWebUrl('order/export')}">自定义导出</a>
                                @endif
                            </div>

                        </div>

                    </form>
                </div>
            </div>


            <div class="panel panel-default">
                <table class='table'
                       style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                    <tr class='trhead'>
                        <td colspan='8' style="text-align: left;">
                            订单数: <span id="total">{{$list['total']}}</span>
                            订单金额: <span id="totalmoney" style="color:red">{{$total_price}}</span>元&nbsp;
                            @section('supplier_apply')

                            @show
                        </td>
                    </tr>
                </table>

                @foreach ($list['data'] as $order_index => $order)
                <div class="order-info">
                    <table class='table order-title' >
                        <tr>
                            <td class="left" colspan='8' >
                                <b>订单编号:</b> {{$order['order_sn']}}
                                @if( 0&&$order['pay_ordersn']=0)
                                    <b>支付单号:</b>  {{$order['pay_ordersn']=0}}
                                @endif
                                <b>下单时间: </b>{!! date('Y-m-d H:i:s', $order['create_time']) !!}
                                @if( 0&&!empty($order['refundstate']))<label
                                        class='label label-danger'>{{$r_type[$order['rtype']]}}申请</label>@endif
                                @if( 0&&$order['rstatus'] == 4)<label class='label label-primary'>客户已经寄出快递</label>@endif
                                @section('shop_name')
                                    <label class="label label-info">总店</label>
                                @show
                                @if( 0&&!empty($order['storename']))
                                    <label class="label label-primary">所属门店：{{$order['storename']}}</label>
                            @endif
                            <td class="right" >
                                @if( 0&&empty($order['statusvalue']))
                                    <a class="btn btn-default btn-sm" href="javascript:;"
                                       onclick="$('#modal-close').find(':input[name=id]').val('{{$order['id']}}')"
                                       data-toggle="modal" data-target="#modal-close">关闭订单</a>
                                @endif

                            </td>

                            @if( 0&&empty($var['isagent']) && $order['isempty'] == 1 && $order['ismaster'] == 1)
                                <td >
                                    <input class='itemid' type='hidden' value="{{$order['id']}}"/>
                                    <a class="btn btn-primary btn-sm" href="javascript:;" onclick="sendagent(this)"
                                       data-toggle="modal" data-target="#modal-changeagent">选择门店</a>
                                </td>
                            @endif


                        </tr>
                    </table>
                    <table class='table order-main' >

                        @foreach( $order['has_many_order_goods'] as $order_goods_index => $order_goods)
                            <tr class='trbody'>
                                <td class="goods_info">
                                    <img src="@if( 0&&$order['cashier']==1){{$order['name']['thumb']}}@else{!! tomedia($order_goods['thumb']) !!}@endif">
                                </td>
                                <td class="top" valign='top' >
                                    {{$order_goods['title']}}
                                    @if( !empty($order_goods['optiontitle']))<br/><span
                                            class="label label-primary sizebg">{{$order_goods['optiontitle']}}</span>
                                    @endif
                                    <br/>{{$order_goods['goods_sn']}}
                                </td>
                                <td class="price">@if( $requestSearch['plugin'] != "fund")
                                        原价: {!! number_format($order_goods['goods_price']/$order_goods['total'],2)!!} @endif
                                    <br/>应付: {!! number_format($order_goods['price']/$order_goods['total'],2) !!}
                                    <br/>数量: {{$order_goods['total']}}
                                </td>


                                @if( $order_goods_index == 0)
                                    <td rowspan="{!! count($order['has_many_order_goods']) !!}">
                                        <a href="{!! yzWebUrl('member.member.detail',array('id'=>$order['belongs_to_member']['uid'])) !!}"> {{$order['belongs_to_member']['nickname']}}</a>
                                        <br/>
                                        {{$order['belongs_to_member']['realname']}}
                                        <br/>{{$order['belongs_to_member']['mobile']}}
                                    </td>

                                    <td rowspan="{!! count($order['has_many_order_goods']) !!}">
                                        <label class='label label-info'>{{$order['pay_type_name']}}</label>
                                        <br/>

                                        {{$order['has_one_dispatch_type']['name']}}
                                        @if( 0&&$order['addressid']!=0 && $order['statusvalue']>=2)<br/>
                                        <button type='button' class='btn btn-default btn-sm'
                                                onclick='express_find(this,"{{$order['id']}}")'>查看物流
                                        </button>
                                        @endif
                                    </td>
                                    <td rowspan="{php echo count($order['has_many_order_goods'])}" style='width:18%;'>
                                        <table class="goods-price" >
                                            <tr>
                                                <td style=''>商品小计：</td>
                                                <td style=''>￥{!! number_format(
                                                $order['goods_price'] ,2) !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style=''>运费：</td>
                                                <td style=''>￥{!! number_format(
                                                $order['dispatch_price'],2) !!}
                                                </td>
                                            </tr>


                                            @if( empty($order['statusvalue']))
                                                <tr>
                                                    <td ></td>
                                                    @if(0)
                                                        @if( 0&&$order['ischangePrice'] == 1)
                                                            <td style='color:green;'>
                                                                <a href="javascript:;" class="btn btn-link "
                                                                   onclick="changePrice('{{$order['id']}}')">修改价格</a>
                                                            </td>
                                                        @endif
                                                    @endif
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                    <td rowspan="{{count($order['has_many_order_goods'])}}"><label
                                                class='label label-info'>{{$order['status_name']}}</label><br/>
                                        <a href="{!! yzWebUrl('order.detail',['id'=>$order['id']])!!}">查看详情</a>
                                    </td>
                                    <td rowspan="{{count($order['has_many_order_goods'])}}" width="10%">
                                        @section('operation'.$order_index)
                                            @include('order.ops')
                                        @show
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
                @endforeach
                @include('order.modals')
                <div id="pager">{!! $pager !!}</div>


            </div>
        </div>
    </div>
    <script>
        $(function () {
            $("#ambiguous-field").on('change', function () {

                $(this).next('input').attr('placeholder', $(this).find(':selected').text().trim())
            });
        })
        $('#export').click(function () {
            $('#form_p').val("order.list.export");
            $('#form1').submit();
            $('#form_p').val("order.list");
        });
    </script>
@endsection('content')