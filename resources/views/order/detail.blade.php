@extends('layouts.base')

@section('js')
    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <script language="javascript">
        function sub() {
            var order_id = $('.order_id').val();
            var remark = $('#remark').val();
            $.post("{!! yzWebUrl('order.remark.update-remark') !!}", {
                order_id: order_id,
                remark: remark
            }, function (json) {
                var json = $.parseJSON(json);
                if (json.status == 1) {
                    location.href = location.href;
                }
            });
        }
        function showDiyInfo(obj) {
            var hide = $(obj).attr('hide');
            if (hide == '1') {
                $(obj).next().slideDown();
            }
            else {
                $(obj).next().slideUp();
            }
            $(obj).attr('hide', hide == '1' ? '0' : '1');
        }

        //cascdeInit("{!! isset($user['province'])?$user['province']:'' !!}", "{!! isset($user['city'])?$user['city']:'' !!}", "{!! isset($user['area'])?$user['area']:'' !!}");

        $('#editaddress').click(function () {
            show_address(1);
        });

        $('#backaddress').click(function () {
            show_address(0);
        });

        $('#editexpress').click(function () {
            show_express(1);
        });

        $('#backexpress').click(function () {
            show_express(0);
        });


        function show_address(flag) {
            if (flag == 1) {
                $('.ad1').hide();
                $('.ad2').show();
            } else {
                $('.ad1').show();
                $('.ad2').hide();
            }
        }
        function show_express(flag) {
            if (flag == 1) {
                $('.ex1').hide();
                $('.ex2').show();
            } else {
                $('.ex1').show();
                $('.ex2').hide();
            }
        }

    </script>
@stop

@section('content')
    <div class="w1200 m0a">

        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">订单管理 &nbsp; <i class="fa fa-angle-double-right"></i> &nbsp; 订单详情</a>
                    </li>

                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="main">

                <input type="hidden" class="order_id" value="{{$order['id']}}"/>
                <input type="hidden" name="token" value="{{$var['token']}}"/>
                <input type="hidden" name="dispatchid" value="{{$dispatch['id']}}"/>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$order['be_longs_to_member']['avatar']}}'
                                     style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                {{$order['be_longs_to_member']['nickname']}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员信息 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>ID: {{$order['be_longs_to_member']['uid']}}
                                    姓名: {{$order['be_longs_to_member']['realname']}} /
                                    手机号: {{$order['be_longs_to_member']['mobile']}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单编号 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$order['order_sn']}} </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单金额 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="form-control-static">
                                    <table cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style='border:none;text-align:right;'>商品小计：</td>
                                            <td style='border:none;text-align:right;;'>
                                                ￥{{number_format( $order['goods_price'] ,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td style='border:none;text-align:right;'>应收款：</td>
                                            <td style='border:none;text-align:right;color:green;'>
                                                ￥{{number_format($order['price'],2)}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if ($order['status'] == 0)<span class="label label-info">待付款</span>@endif
                                    @if ($order['status'] == 1)<span class="label label-info">待发货</span>@endif
                                    @if ($order['status'] == 2)<span class="label label-info">待收货</span>@endif
                                    @if ($order['status'] == 3)<span class="label label-success">已完成</span>@endif
                                    @if ($order['status'] == -1)
                                        @if (!empty($refund) && $refund['status'] == 1)
                                            <span class="label label-default">已{{$r_type[$refund['rtype']]}}</span>
                                            @if (!empty($refund['refundtime']))
                                                退款时间: {{$refund['refundtime']}}
                                            @endif
                                        @else
                                            <span class="label label-default">已关闭</span>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注 :</label>
                            <div class="col-sm-9 col-xs-12"><textarea style="height:150px;" class="form-control"
                                                                      id="remark" name="remark"
                                                                      cols="70">{{$order['has_one_order_remark']['remark']}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <br/>
                                <button name='saveremark' onclick="sub()" class='btn btn-default'>保存备注</button>
                            </div>
                        </div>
                        @if (!empty($refund) && $refund['status'] == 1)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="form-control-static">{{$order['refundtime']}}</div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$order['create_time']}}</p>
                            </div>
                        </div>
                        @if ($order['status'] >= 1)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$order['pay_time']}}</p>
                                </div>
                            </div>
                        @endif
                        @if ($order['status'] == 3)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$order['finish_time']}}</p>
                                </div>
                            </div>
                        @endif

                    </div>

                    @if (!empty($order['has_one_refund_apply']))
                        @include('refund.index')
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            商品信息
                        </div>
                        <div class="panel-body table-responsive">
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    <th class="col-md-5 col-lg-1">ID</th>
                                    <th class="col-md-5 col-lg-3">商品标题</th>
                                    <th class="col-md-5 col-lg-2">商品编号</th>
                                    <th class="col-md-5 col-lg-2">现价/原价/成本价</th>
                                    <th class="col-md-5 col-lg-1">购买数量</th>
                                    <th class="col-md-5 col-lg-2" style="color:red;">折扣前<br/>折扣后</th>
                                    <th class="col-md-5 col-lg-1">操作</th>
                                </tr>
                                </thead>
                                @foreach ($order['has_many_order_goods'] as $goods)
                                    <tr>
                                        <td>{{$goods['belongs_to_good']['id']}}</td>
                                        <td>{{$goods['belongs_to_good']['title']}}</td>
                                        <td>{{$goods['belongs_to_good']['goods_sn']}}</td>
                                        <td>{{$goods['belongs_to_good']['price']}}
                                            /{{$goods['belongs_to_good']['market_price']}}
                                            /{{$goods['belongs_to_good']['cost_price']}}元
                                        </td>
                                        <td>{{$goods['total']}}</td>
                                        <td style='color:red;font-weight:bold;'>{{$order['goods_price']}}
                                            <br/>{{$order['price']}}
                                        </td>
                                        <td>
                                            <a href="{!! yzWebUrl('goods.goods.edit', array('id' => $goods['belongs_to_good']['id'])) !!}"
                                               class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                    <tr style="text-align: right;padding: 6px 0;border-top:none;">
                                        <td colspan="8">
                                            @if ($goods['belongs_to_good']['status'] == 1)
                                                <label data="1"
                                                       class="label label-default text-default label-info text-pinfo">上架</label>
                                            @else
                                                <label data="1"
                                                       class="label label-default text-default label-info text-pinfo">下架</label>
                                            @endif
                                            <label data="1"
                                                   class="label label-default text-default label-info text-pinfo">
                                                @if ($goods['belongs_to_good']['type'] == 1)
                                                    实体商品
                                                @else
                                                    虚拟商品
                                                @endif
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">
                                        @include('order.modals')
                                        @include('order.ops')
                                    </td>
                                    <td colspan="8">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

@endsection('content')

