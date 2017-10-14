@extends('layouts.base')
@section('content')

    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <div class="rightlist" id="member-blade">

        @include('layouts.tabs')

            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">

                        <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                            <div class="">
                                <div class='input-group'>

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员ID</p>
                                        <input class="form-control price" style="width: 135px;" type="text" name="search[min_love]" value="{{ $search['min_love'] or ''}}">
                                    </div>

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员信息</p>
                                        <input class="form-control price" style="width: 135px;" type="text" name="search[min_love]" value="{{ $search['min_love'] or ''}}">
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                            <div class="">
                                <input type="submit" class="btn btn-block btn-success" value="{{ trans('Yunshop\Love::member_love.button.search') }}">
                            </div>

                        </div>
                    </form>
                </div>



                <div class='panel-body'>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style='width:80px;'>排行</th>
                            <th>粉丝</th>
                            <th>姓名</th>
                            <th>手机号</th>
                            <th>等级</th>
                            <th>消费金额</th>
                            <th>订单数</th>
                        </tr>
                        </thead>
                        <tbody>
                        {loop $list $key $item}
                        <tr>
                            <td>{if  ($pindex -1)* $psize + $key + 1<=3}
                                <labe class='label label-danger' style='padding:8px;'>&nbsp;{php echo ($pindex -1)* $psize + $key + 1}&nbsp;</labe>
                                {else}
                                <labe class='label label-default'  style='padding:8px;'>&nbsp;{php echo ($pindex -1)* $psize + $key + 1}&nbsp;</labe>
                                {/if}
                            </td>
                            <td><img src="{$item['avatar']}" style='padding:1px;width:30px;height:30px;border:1px solid #ccc' />
                                {$item['nickname']}</td>
                            <td>{$item['realname']}</td>
                            <td>{$item['membermobile']}</td>
                            <td>{if empty($item['levelname'])} {php echo empty($shop['levelname'])?'普通会员':$shop['levelname']} {else}{$item['levelname']}{/if}</td>
                            <td>{$item['ordermoney']}</td>
                            <td>{$item['ordercount']}</td>
                        </tr>
                        {/loop}
                    </table>
                    {$pager}
                </div>
            </div>
        </div>
    </div>
@endsection
