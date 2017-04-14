@extends('layouts.base')

@section('content')

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
                    <input type="hidden" name="c" value="site" />
                    <input type="hidden" name="a" value="entry" />
                    <input type="hidden" name="m" value="yun_shop" />
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
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $detailList->total() }}</div>
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
                        @foreach($detailList as $list)
                            <tr style="text-align: center">
                                <td style="text-align: center;">{{ date('Y-m-d H:i:s', $list->created_at) }}</td>
                                <td style="text-align: center;">{{ $list->member_id }}</td>
                                <td style="text-align: center;">
                                    @if($list->member->avatar)
                                        <img src='{{ $list->member->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    {{ $list->member->nickname or '' }}
                                </td>
                                <td>{{ $list->member->realname }}<br/>{{ $list->member->mobile }}</td>
                                <td><label class="label label-danger">余额：{{ $list->new_money }}</label></td>
                                <td>{{ $list->type_name }}</td>
                                <td>{{ $list->change_money }}</td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('finance.balance.lookBalanceDetail', array('id' => $list->id )) }}" style="margin-bottom: 2px">查看详情</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}

                </div>
            </div>
        </div>

@endsection