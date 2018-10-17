@extends('layouts.base')

@section('content')
@section('title', trans('订单收益统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>--}}

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class='alert alert-info'>
                    <p>1、未分润金额：根据订单算出的分润金额减去实际分润金额</p>
                    <p>2、收益不包含分润出去的部分</p>
                </div>
                <div class="card">
                    <div class="card-content">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="会员ID"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="订单号查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="会员ID/昵称/姓名/手机"/>
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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-4 text-center" style="white-space: pre-wrap;">订单号</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">购买者</th>
                            <th class="col-md-2 text-center">订单金额</th>
                            <th class="col-md-4 text-center">订单类型</th>
                            <th class="col-md-4 text-center">商家</th>
                            <th class="col-md-2 text-center">手续费收益</th>
                            <th class="col-md-2 text-center">未被分润</th>
                            <th class="col-md-2 text-center">商城收益</th>
                            <th class="col-md-2 text-center">供应商收益</th>
                            <th class="col-md-2 text-center">门店收益</th>
                            <th class="col-md-2 text-center">收银台收益</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                            <tr style="height: 40px; text-align: center">
                                <td></td>
                                <td>
                                    @if(!empty($item['thumb_url']))
                                        <img src='{{ $item['thumb_url'] }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($item['name']))
                                        未更新
                                    @else
                                        {{ $item['name'] }}
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
@endsection
