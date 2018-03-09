@extends('layouts.base')
@section('title', '会员详情')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;收入详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('member.member.update', ['id'=> $member['uid']])}}" method='post'
                  class='form-horizontal'>
                <input type="hidden" name="id" value="{{$member['uid']}}">
                <input type="hidden" name="op" value="detail">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="member"/>
                <div class="panel panel-default">
                    <div class='panel-body'>
                        <div style='height:auto;width:120px;float:left;'>
                            <img src='{{$member['avatar']}}'
                                 style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
                        </div>
                        <div style='float:left;height:auto;overflow: hidden'>
                            <p>
                                <b>会员id:</b>
                                {{$member['uid']}}
                            </p>
                            <p>
                                <b>昵称:</b>
                                {{$member['nickname']}}
                            </p>
                            <p>
                                <b>姓名:</b>
                                {{$member['realname']}}
                            </p>
                            <p>
                                <b>累计收入: </b><span style='color:red'>{{$incomeAll['income']}}</span> 元
                            </p>
                            <p>
                                <b>累计提现: </b><span style='color:red'>{{$incomeAll['withdraw']}}</span> 元
                            </p>
                            <p>
                                <b>未提现: </b><span style='color:red'>{{$incomeAll['no_withdraw']}}</span> 元
                            </p>
                            {{--<p>--}}
                                {{--<b>提现金额: </b><span style='color:red'>{{$item->amounts}}</span> 元--}}
                            {{--<p>--}}
                        </div>
                    </div>

                    {{--<div class='panel-heading'>--}}
                        {{--收入提现申请信息 共计 <span style="color:red; ">{{$item->type_data['income_total']}}</span> 条收入--}}
                    {{--</div>--}}
                    <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item->id])}}" method='post' class='form-horizontal'>
                        <div class='panel-body'>
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    {{--<th>收入ID</th>--}}
                                    <th>收入类型</th>
                                    <th>收入金额</th>
                                    <th>已提现金额</th>
                                    <th>未提现金额</th>
                                    {{--<th>收入时间</th>--}}
                                    {{--<td>收入详情</td>--}}
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($item as $k=>$row)
                                    <tr style="background: #eee">
                                        {{--<td>{{$row['id']}}</td>--}}
                                        <td>{{$row['type_name']}}</td>
                                        <td>{{$row['income']}}</td>
                                        <td>{{$row['withdraw']}}</td>
                                        <td>{{$row['no_withdraw']}}</td>
                                        {{--<td>{{$row['created_at']}}</td>--}}
                                        {{--<td>--}}
                                            {{--<a class="btn btn-danger btn-sm" href="javascript:;" data-toggle="modal"--}}
                                               {{--data-target="#modal-refund{{$k}}">详情</a>--}}
                                        {{--</td>--}}
                                    </tr>

                                    <div id="modal-refund{{$k}}" class="modal fade" tabindex="-1" role="dialog"
                                         style="width:600px;margin:0px auto;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×
                                                    </button>
                                                    <h3>收入信息</h3>

                                                    @foreach(json_decode($row['detail'],true) as $data)
                                                        <div class="form-group">{{$data['title']}}</div>
                                                        @foreach($data['data'] as $value)
                                                            @if(!isset($value['title']))
                                                                @foreach($value as $v)
                                                                    <div class="modal-body" style="background: #eee">
                                                                        <div class="form-group">
                                                                            <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$v['title']}}</label>
                                                                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                                {{$v['value']}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else

                                                                <div class="modal-body" style="background: #eee">
                                                                    <div class="form-group">
                                                                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$value['title']}}</label>
                                                                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                            @if($value['title'] === '订单号')
                                                                                {{$value['value']}}
                                                                                <a target="_blank"
                                                                                   href="{{yzWebUrl('order.list',['search'=>['ambiguous'=>['field'=>'order','string'=>$value['value']]]])}}">订单详情</a>
                                                                            @else
                                                                                {{$value['value']}}
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        @endforeach
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </table>
                        </div>
                        {{--<div class='panel-heading'>--}}
                            {{--打款信息--}}
                        {{--</div>--}}
                        {{--<div class='panel-body'>--}}
                            {{--审核金额: <span style='color:red'>{{$item->actual_amounts + $item->actual_poundage + $item->actual_servicetax}}</span> 元--}}
                            {{--手续费: <span style='color:red'>{{$item->actual_poundage}}</span> 元--}}
                            {{--劳务税:<span style='color:red'>{{$item->actual_servicetax}}</span> 元--}}
                            {{--应打款：<span style='color:red'>{{$item->actual_amounts}}</span>元--}}

                        {{--</div>--}}

                        <div class="form-group col-sm-12">
                            @if($item->status == '0')
                                <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg-1"
                                       onclick='return check()'/>
                            @endif

                            @if($item->status == '1')

                                @if($item->pay_way == 'balance')
                                    <input type="hidden" name="pay_way" value="3">
                                    <input type="submit" name="submit_pay" value="打款到余额" class="btn btn-primary col-lg-1"
                                           style='margin-left:10px;' onclick='return '/>
                                @elseif($item->pay_way == 'wechat')
                                    <input type="hidden" name="pay_way" value="1">
                                    <input type="submit" name="submit_pay" value="打款到微信钱包" class="btn btn-primary col-lg-1"
                                           style='margin-left:10px;' onclick='return '/>
                                @elseif($item->pay_way == 'alipay')
                                    <input type="hidden" name="pay_way" value="2">
                                    <input type="submit" name="submit_pay" value="打款到支付宝"
                                           class="btn btn-primary " style='margin-left:10px;'
                                           onclick='return '/>
                                @elseif($item->pay_way == 'manual')
                                    <input type="hidden" name="pay_way" value="4">
                                    <input type="submit" name="submit_pay" value="手动打款"
                                           class="btn btn-primary " style='margin-left:10px;'
                                           onclick='return '/>

                                @endif
                            @endif

                            @if($item->status == '-1')
                                <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default "
                                       onclick='return '/>
                            @endif


                            <input type="button" class="btn btn-default" name="submit" onclick="goBack()" value="返回"
                                   style='margin-left:10px;'/>
                        </div>
                    </form>

                </div>
            </form>
        </div>
    </div>


    <script language='javascript'>
        function goBack() {
            window.location.href="{!! yzWebUrl('member.member.index') !!}";
        }
    </script>
@endsection