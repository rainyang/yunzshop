@extends('layouts.base')
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist" id="member-blade">
            @include('layouts.tabs')
            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">

                        <div class="form-group col-sm-6 col-lg-6 col-xs-12">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="search[is_time]" value="1"
                                           @if($search['is_time'] == '1')checked="checked"@endif>
                                </span>
                                {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                        'starttime'=>$search['time']['start'] ?: date('Y-m-d H:i:s'),
                                                                        'endtime'=>$search['time']['end'] ?: date('Y-m-d H:i:s'),
                                                                        'start'=>0,
                                                                        'end'=>0
                                                                        ])!!}
                            </div>
                        </div>
                        <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                        </div>
                    </form>
                </div>

                <div class="panel panel-default">
                    <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                        <tr class='trhead'>
                            <td colspan='8' style="text-align: left;">
                                供应商数量: <span id="total">{{ $supplierTotal }}个</span>&nbsp;&nbsp;&nbsp;未提现收入: <span id="total">{{ $unWithdrawTotal }}元</span>&nbsp;&nbsp;&nbsp;提现中收入: <span id="total">{{ $withdrawingTotal }}元</span>&nbsp;&nbsp;&nbsp;已提现收入: <span id="total">{{ $withdrawTotal }}元</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='panel-body'>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style='width:80px;'>排行</th>
                            <th>供应商</th>
                            <th>交易完成总额</th>
                            <th>未提现收入</th>
                            <th>提现中收入</th>
                            <th>已提现收入</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($list as $key => $item)
                            <tr>
                                <td>
                                    @if($key <= 2)
                                        <label class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</label>
                                    @else
                                        <label class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</label>
                                    @endif
                                </td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['price'] ?: '0.00' }}</td>
                                <td>{{ $item['un_withdraw'] ?: '0.00' }}</td>
                                <td>{{ $item['withdrawing'] ?: '0.00' }}</td>
                                <td>{{ $item['withdraw'] ?: '0.00' }} </td>
                            </tr>
                        @endforeach

                    </table>
                    {{--{!! $page !!}--}}
                </div>
            </div>
        </div>
    </div>
@endsection
