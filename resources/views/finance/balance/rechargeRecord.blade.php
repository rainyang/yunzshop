@extends('layouts.base')

@section('content')

    <div class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action=" " method="post" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="sz_yi"/>
                    <div class="form-group">
                        <div class="col-sm-8 col-lg-12 col-xs-12">
                            @section('search_bar')
                                <div class='input-group'>
                                    <select name="search[ambiguous][field]" id="ambiguous-field" class="form-control">
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
                                    <input class="form-control" name="search[ambiguous][string]" type="text"
                                           value="{{array_get($requestSearch,'ambiguous.string','')}}"
                                           placeholder="订单号/支付单号">
                                </div>
                                <div class='input-group'>

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
                                <div class='input-group'>

                                    <select name="search[time_range][field]" class="form-control">
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
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 col-lg-12 col-xs-12">
                            <div class='input-group'>
                                <div class='input-group-addon'>会员信息</div>
                                <input class="form-control" name="search[realname]" type="text" value="{{ $search['realname'] or ''}}" placeholder="会员姓名／昵称／手机号">
                                <div class='input-group-addon'>充值单号</div>
                                <input class="form-control" name="search[ordersn]" type="text" value="{{ $search['realname'] or ''}}" placeholder="充值单号">

                                <div class='input-group-addon'>会员等级</div>
                                <select name="search[level]" class="form-control">
                                    <option value="" selected>不限</option>
                                    @foreach($memberLevel as $level)
                                        <option value="{{ $level['id'] }}" @if($search['level'] == $level['id']) selected @endif>{{ $level['level_name'] }}</option>
                                    @endforeach
                                </select>
                                <div class='input-group-addon'>会员分组</div>
                                <select name="search[groupid]" class="form-control">
                                    <option value="" selected >不限</option>
                                    @foreach($memberGroup as $group)
                                        <option value="{{ $group['id'] }}" @if($search['groupid'] == $group['id']) selected @endif>{{ $group['group_name'] }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <input type="submit" class="btn btn-default" value="搜索">
                            <!--<button type="submit" name="export" value="1" class="btn btn-primary">导出 Excel</button>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">总数：{{ $recordList->total() }}</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                        <tr>
                            <th style='width:15%; text-align: center;'>充值单号</th>
                            <th style='width:10%; text-align: center;'>粉丝</th>
                            <th style='width:14%; text-align: center;'>会员信息<br/>微信号</th>
                            <th style='width:12%; text-align: center;' class='hidden-xs'>等级/分组</th>
                            <th style='width:12%; text-align: center;'>充值时间</th>
                            <th style='width:12%; text-align: center;'>充值方式</th>
                            <th style='width:12%; text-align: center;'>充值金额<br/>状态</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                    </thead>
                @foreach($recordList as $list)
                    <tr style="text-align: center;">
                        <td>{{ $list->ordersn }}</td>
                        <td>
                            <img src='{{ $list->member->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                            <br/>
                            {{ $list->member->nickname }}
                        </td>
                        <td>
                            {{ $list->member->realname }}
                            <br/>
                            {{ $list->member->mobile }}
                        </td>

                        <td class='hidden-xs'>
                            {{ $list->member->yzMember->level->level_name or '普通会员'}}
                            <br />
                            {{ $list->member->yzMember->group->group_name or '无分组' }}
                        </td>

                        <td>{{ $list->created_at }}</td>
                        <td>
                            @if($list->type == 1)
                                <span class='label label-default'>后台充值</span>
                            @elseif($list->type ==2)
                                <span class='label label-success'>微信支付</span>
                            @elseif($lsit->type == 3)
                                <span class='label label-warning'>支付宝</span>
                            @else
                                <span class='label label-primary'>其他支付</span>
                            @endif

                        </td>
                        <td>
                            {{ $list->money }}
                            <br/>
                            @if($list->status == 1)
                                <span class='label label-success'>充值成功</span>
                            @elseif($list->status == '-1')
                                <span class='label label-warning'>充值失败</span>
                            @else
                                <span class='label label-default'>申请中</span>
                            @endif

                        </td>

                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('member.member.detail', array('uid' => $list->member_id)) }}" style="margin-bottom: 2px">用户信息</a>
                        </td>
                    </tr>
                @endforeach
                </table>
                {!! $pager !!}
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $("#ambiguous-field").on('change',function(){

                $(this).next('input').attr('placeholder',$(this).find(':selected').text().trim())
            });
        })
        $('#export').click(function () {
            $('#form_p').val("order.list.export");
            $('#form1').submit();
            $('#form_p').val("order.list");
        });
    </script>

@endsection