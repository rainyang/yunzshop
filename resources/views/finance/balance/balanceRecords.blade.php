@extends('layouts.base')
@section('title', '余额明细')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">财务／余额明细</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>

            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="route" value="finance.balance-records.index" id="route" />
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="{{ trans('Yunshop\Love::change_records.search_member_id') }}" class="form-control"  name="search[member_id]" value="{{$search['member_id']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control"  name="search[realname]" value="{{$search['realname']}}" placeholder="{{ trans('Yunshop\Love::change_records.search_member') }}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[member_level]' class='form-control'>
                                <option value=''>{{ trans('Yunshop\Love::change_records.search_member_level') }}</option>

                                @foreach($memberLevels as $list)
                                    <option value='{{ $list['id'] }}' @if($search['member_level'] == $list['id']) selected @endif>{{ $list['level_name'] }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员等级</label>-->
                        <div class="">
                            <select name='search[member_group]' class='form-control'>
                                <option value=''>{{ trans('Yunshop\Love::change_records.search_member_group') }}</option>
                                @foreach($memberGroups as $list)
                                    <option value='{{ $list['id'] }}' @if($search['member_group'] == $list['id']) selected @endif>{{ $list['group_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                        <div class="">
                            <select name='search[source]' class='form-control'>
                                <option value=''>{{ trans('Yunshop\Love::change_records.search_source') }}</option>
                                @foreach($sourceName as $key => $value)
                                    <option value='{{ $key }}' @if($search['source'] == $key) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">黑名单</label>-->
                        <div class="">
                            <select name='search[type]' class='form-control'>
                                <option value=''>{{ trans('Yunshop\Love::change_records.search_type') }}</option>
                                <option value='1' @if($search['type']=='1') selected @endif>{{ trans('Yunshop\Love::change_records.search_type_income') }}</option>
                                <option value='2' @if($search['type']=='2') selected @endif>{{ trans('Yunshop\Love::change_records.search_type_expend') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" placeholder="订单号" class="form-control"  name="search[order_sn]" value="{{$search['order_sn']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">

                        <div class="time">

                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>{{ trans('Yunshop\Love::change_records.search_time_off') }}</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>{{ trans('Yunshop\Love::change_records.search_time_on') }}</option>
                            </select>
                        </div>
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                            'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                            'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                        </div>
                    </div>

                    <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                        <div class="">
                            <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">{{ trans('Yunshop\Love::change_records.button.export') }}</button>
                            <input type="hidden" name="token" value="{{$var['token']}}" />
                            <button class="btn btn-success "><i class="fa fa-search"></i>{{ trans('Yunshop\Love::change_records.button.search') }}</button>

                        </div>
                    </div>

                </form>
            </div>

            {{--<div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group">
                        <div class="col-sm-8 col-lg-12 col-xs-12">
                            <div class='input-group'>
                                <div class='input-group-addon'>会员信息</div>
                                <input class="form-control" name="search[realname]" type="text" value="{{ $search['realname'] or ''}}" placeholder="会员姓名／昵称／手机号">
                                <div class='input-group-addon'>业务类型</div>
                                <select name="search[service_type]" class="form-control">
                                    <option value="" selected>不限</option>
                                    @foreach($serviceType as $key => $value)
                                        <option value="{{ $key }}" @if($search['service_type'] == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6 col-lg-6 search-time">
                        <div class="time-select" >
                            <select name='search[searchtime]' class='form-control'>
                                <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索充值时间</option>
                                <option value='1' @if($search['searchtime']==1) selected @endif >搜索充值时间</option>
                            </select>
                        </div>
                        <div class="time-btn">
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
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <input type="submit" class="btn btn-default" value="搜索">
                        </div>
                    </div>
                </form>
            </div>--}}
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:11%;text-align: center;'>时间</th>
                            <th style='width:8%;text-align: center;'>会员ID</th>
                            <th style='width:8%;text-align: center;'>粉丝</th>
                            <th style='width:12%;text-align: center'>姓名<br/>手机号码</th>
                            <th style='width:8%;text-align: center'>余额</th>
                            <th style='width:15%;text-align: center'>业务类型</th>
                            <th style='width:8%;text-align: center'>收入\支出</th>
                            <th style='width:8%;text-align: center'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center">
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                                <td style="text-align: center;">{{ $list->member_id }}</td>
                                <td style="text-align: center;">
                                    @if($list->member->avatar || $shopSet['headimg'])
                                        <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    {{ $list->member->nickname ? $list->member->nickname : '未更新' }}
                                </td>
                                <td>{{ $list->member->realname }}<br/>{{ $list->member->mobile }}</td>
                                <td><label class="label label-danger">余额：{{ $list->new_money }}</label></td>
                                <td>{{ $list->service_type_name }}</td>
                                <td>{{ $list->change_money }}</td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('finance.balance.lookBalanceDetail', array('id' => $list->id )) }}" style="margin-bottom: 2px">查看详情</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>
        <script language='javascript'>
            $(function () {
                $('#export').click(function(){
                    $('#route').val("finance.balance-records.export");
                    $('#form1').submit();
                    $('#route').val("finance.balance-records.index");
                });
            });
        </script>

@endsection