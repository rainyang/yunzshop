@extends('layouts.base')

@section('content')

    <div class="panel panel-default">
        <div class='panel-heading'>
            提现者信息
        </div>
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{tomedia($item['has_one_member']['avatar'])}}'
                     style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{$item['has_one_member']['nickname']}}
                    <b>姓名:</b>
                    {{$item['has_one_member']['realname']}}
                    <b>手机号:</b>
                    {{$item['has_one_member']['mobile']}}
                </p>
                <p><b>分销等级:</b> {{$item['has_one_agent']['agent_level']['name']}} (
                    @if($set['level']>=1)一级比例: <span style='color:blue'>{{$item['has_one_agent']['agent_level']['first_level']}}
                        %</span>
                    @endif
                    @if($set['level']>=2)二级比例: <span style='color:blue'>{{$item['has_one_agent']['agent_level']['second_level']}}
                        %</span>
                    @endif
                    @if($set['level']>=3)三级比例: <span style='color:blue'>{{$item['has_one_agent']['agent_level']['third_level']}}
                        %</span>
                    @endif
                     )
                </p>
                <p>
                    <b>累计收入: </b><span style='color:red'>{{$item['has_one_agent']['commission_total']}}</span> 元
                </p>
                <p>
                    <b>提现金额: </b><span style='color:red'>{{$item['amounts']}}</span> 元
                <p>
                <p>
                    <b>收入类型: </b>{{$item['type_name']}}
                <p>
                <p>
                    <b>提现方式: </b>{{$item['pay_way_name']}}
                </p>
                <p>
                    <b>状态: </b>{{$item['status_name']}}
                </p>
                <p>
                    <b>申请时间: </b>{{$item['created_at']}}
                </p>
                @if($item['audit_at'])
                    <p>
                        <b>审核时间: </b>{{$item['audit_at']}}
                    </p>
                @endif
                @if($item['pay_at'])
                    <p>
                        <b>打款时间: </b>{{$item['pay_at']}}
                    </p>
                @endif
                @if($item['arrival_at'])
                    <p>
                        <b>到账时间: </b>{{$item['arrival_at']}}
                    </p>
                @endif

            </div>
        </div>

        <div class='panel-heading'>
            收入提现申请信息 共计 <span style="color:red; ">{{$item['type_data']['income_total']}}</span> 条收入
        </div>
        <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item['id']])}}" method='post' class='form-horizontal'>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <td></td>
                        <th>收入ID</th>
                        <th>收入类型</th>
                        <th>收入金额</th>
                        <th>收入状态</th>
                        <th>打款状态</th>
                        <th>收入时间</th>
                        <td>收入详情</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($item['type_data']['incomes'] as $k=>$row)
                        <tr style="background: #eee">
                            <td>
                                @if($item['status'] == '0' || $item['status'] == '-1')
                                    <label class="radio-inline">
                                        <input type="radio" name="audit[{{$row['id']}}]" value="1" checked="checked"/>
                                        通过
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="audit[{{$row['id']}}]" value="0"/> 无效
                                    </label>
                                @endif
                                @if($item['status'] == '1' || $item['status'] == '2')
                                    {{$row['pay_status_name']}}
                                @endif

                            </td>
                            <td>{{$row['id']}}</td>
                            <td>{{$row['type_name']}}</td>
                            <td>{{$row['amount']}}</td>
                            <td>{{$row['status_name']}}</td>
                            <td>{{$row['pay_status_name']}}</td>
                            <td>{{$row['created_at']}}</td>
                            <td>
                                <a class="btn btn-danger btn-sm" href="javascript:;" data-toggle="modal"
                                   data-target="#modal-refund{{$k}}">详情</a>
                            </td>
                        </tr>

                        <div id="modal-refund{{$k}}" class="modal fade" tabindex="-1" role="dialog"
                             style="width:600px;margin:0px auto;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×
                                        </button>
                                        <h3>收入信息</h3>
                                        @foreach($row['detail'] as $key1 => $value1)
                                            <div class="form-group">{{$key1}}</div>
                                            @foreach($value1 as $key2 => $value2)
                                                @if(is_object($value2))
                                                    @foreach($value2 as $key3 => $value3)
                                                        <div class="modal-body" style="background: #eee">

                                                            <div class="form-group">
                                                                <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$key3}}</label>
                                                                <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                    {{$value3}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="modal-body" style="background: #eee">
                                                        <div class="form-group">
                                                            <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$key2}}</label>
                                                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                @if($key2 === '订单号')
                                                                    {{$value2}}
                                                                    <a target="_blank"
                                                                       href="{{yzWebUrl('order.list',['search'=>['ambiguous'=>['field'=>'order','string'=>$value2]]])}}">订单详情</a>
                                                                @else
                                                                    {{$value2}}
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


            <div class="form-group col-sm-12">
                @if($item['status'] == '0')
                    <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg-1"
                           onclick='return check()'/>
                @endif

                @if($item['status'] == '1')

                    <div class='panel-heading'>
                        打款信息
                    </div>
                    <div class='panel-body'>
                        审核金额: <span style='color:red'>{{$item['actual_amounts'] + $item['actual_poundage']}}</span> 元
                        手续费: <span style='color:red'>{{$item['actual_poundage']}}</span> 元
                        应打款：<span style='color:red'>{{$item['actual_amounts']}}</span>元

                    </div>
                    @if($item['pay_way'] == 'balance')
                        <input type="hidden" name="pay_way" value="3">
                        <input type="submit" name="submit_pay" value="打款到余额" class="btn btn-primary col-lg-1"
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'wecht')
                        <input type="hidden" name="pay_way" value="1">
                        <input type="submit" name="submit_pay" value="打款到微信钱包" class="btn btn-primary col-lg-1"
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'alipay')
                        <input type="hidden" name="pay_way" value="2">
                        <input type="submit" name="submit_pay" value="打款到支付宝"
                               class="btn btn-primary col-lg-1" style='margin-left:10px;'
                               onclick='return '/>
                    @endif
                @endif

                @if($item['status'] == '-1')
                    <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default col-lg-1"
                           onclick='return '/>
                @endif


                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回"
                       style='margin-left:10px;'/>
            </div>
        </form>

    </div>

@endsection