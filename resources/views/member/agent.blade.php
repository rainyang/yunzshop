@extends('layouts.base')

@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <div class="panel panel-default">
                <div class='panel-body'>
                    <div style='height:100px;width:110px;float:left;'>
                        <img src='{{$member->avatar}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
                    </div>
                    <div style='float:left;height:100px;overflow: hidden'>
                        昵称: {{$member->nickname}}<br/>
                        姓名: {{$member->realname}} <br/>
                        手机号: {{$member->mobile}}<br/>
                        余额: {{$member->credit2}} / 积分：{{$member->credit1}}<br/>
                    </div>
                </div>
            </div>

                <div class="panel panel-info">
                    <div class="panel-heading">筛选</div>
                    <div class="panel-body">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <input type="hidden" name="route" value="member.member.agent" id="route" />
                            <input type="hidden" name="id" value="{{$request->id}}" />
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <input type="text" class="form-control"  name="mid" value="{$_GPC['mid']}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <input type="text" class="form-control"  name="realname" value="{$_GPC['realname']}" placeholder='可搜索昵称/名称/手机号'/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <select name='followed' class='form-control'>
                                        <option value=''></option>
                                        <option value='0' {if $_GPC['followed']=='0'}selected{/if}>未关注</option>
                                        <option value='1' {if $_GPC['followed']=='1'}selected{/if}>已关注</option>
                                        <option value='2' {if $_GPC['followed']=='2'}selected{/if}>取消关注</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
                                <div class="col-sm-3">
                                    <select name='status' class='form-control'>
                                        <option value=''>状态</option>
                                        <option value='0' {if $_GPC['status']=='0'}selected{/if}>未审核</option>
                                        <option value='1' {if $_GPC['status']=='1'}selected{/if}>已审核</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name='agentblack' class='form-control'>
                                        <option value=''>黑名单状态</option>
                                        <option value='0' {if $_GPC['agentblack']=='0'}selected{/if}>否</option>
                                        <option value='1' {if $_GPC['agentblack']=='1'}selected{/if}>是</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                                <div class="col-sm-3"><button class="btn btn-default">
                                        <i class="fa fa-search"></i> 搜索</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            <div class="panel panel-default">
                <div class="panel-heading">总数：{{$total}}</div>
                <div class="panel-body">
                    <table class="table table-hover"   style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:5%;'>会员ID</th>
                            <th style='width:18%;text-align: center;'>推荐人</th>
                            <th style='width:10%;text-align: center;'>粉丝</th>
                            <th style='width:12%;'>姓名</th>
                            <th style='width:12%;'>手机号码</th>
                            <th style='width:12%;text-align: center;'>状态</th>
                            <th style='width:14%;'>时间</th>
                            <th style='width:10%;text-align: center;'>关注</th>
                            <th style='width:8%'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                        <tr>
                            <td>{{$row['uid']}}</td>
                            <td  style="text-align: center;" @if(!empty($row['yz_member']['parent_id']))title='ID: {{$row['yz_member']['parent_id']}}'@endif>
                            @if(empty($row['yz_member']['parent_id']))
                                @if($row['yz_member']['is_agent']==1)
                            <label class='label label-primary'>总店</label>
                                @else
                            <label class='label label-default'>暂无</label>
                                @endif
                            @else
                            <img src='{{$row['yz_member']['agent']['avatar']}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /><br/> {{$row['yz_member']['agent']['nickname']}}
                            @endif
                            </td>
                            <td  style="text-align: center;">
                                @if(!empty($row['yz_member']['agent']['avatar']))
                                    @if(!empty($row['avatar']))
                                        <img src='{{$row['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($row['nickname']))
                                        未更新
                                    @else
                                        {{$row['nickname']}}
                                    @endif
                                @endif

                            </td>

                            <td>{{$row['realname']}}</td>
                            <td>{{$row['mobile']}}</td>

                            <td>
                                @if($row['yz_member']['is_agent']==1)
                                    @if($row['yz_member']['status']==0)
                                        <span class="label label-default">未审核</span>
                                    @elseif ($row['yz_member']['status']==1)
                                        <span class="label label-default">审核中</span>
                                    @else
                                    <span class="label label-success">已审核</span>
                                    @endif
                                @else
                                -
                                @endif

                            </td>
                            <td>注册时间：{{date('Y-m-d H:i',$row['createtime'])}}</td>
                            <td>
                                @if(empty($row['has_one_fans']['followed']))
                                    @if(empty($row['has_one_fans']['uid']))
                                        <label class='label label-default'>未关注</label>
                                    @else
                                        <label class='label label-warning'>取消关注</label>
                                    @endif
                                @else
                                    <label class='label label-success'>已关注</label>
                                @endif
                            </td>
                            <td  style="overflow:visible;">

                                <div class="btn-group btn-group-sm" >
                                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                        <li><a href="{{yzWebUrl('member.member.detail', ['id' => $row['uid']])}}" title="会员详情"><i class='fa fa-edit'></i> 会员详情</a></li>
                                        <li><a  href="{{yzWebUrl('order.list', ['search[ambiguous][field]' => 'order','search[ambiguous][string'=>'uid:'.$row['uid']])}}" title='会员订单'><i class='fa fa-list'></i> 会员订单</a></li>
                                        <li><a href="{{yzWebUrl('finance.point-recharge',['id'=>$row['uid']])}}" title='充值积分'><i class='fa fa-credit-card'></i> 充值积分</a></li>
                                        <li><a href="{{yzWebUrl('finance.balance.recharge', ['id'=>$row['uid']])}}" title='充值余额'><i class='fa fa-money'></i> 充值余额 </a></li>
                                      <li><a href="{{yzWebUrl('member.member.agent', ['id'=>$row['uid']])}}" title='我的下线'><i class='fa fa-money'></i> 推广下线 </a></li>
                                        @if($row['yz_member']['is_black']==1)
                                            <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['uid'],'black'=>0])}}" title='取消黑名单'><i class='fa fa-minus-square'></i> 取消黑名单</a></li>
                                        @else
                                            <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['uid'],'black'=>1])}}" title='设置黑名单'><i class='fa fa-minus-circle'></i> 设置黑名单</a></li>
                                        @endif
                                        <li><a  href="{{yzWebUrl('member.member.delete', ['id' => $row['uid']])}}" title='删除会员' onclick="return confirm('确定要删除该会员吗？');"><i class='fa fa-remove'></i> 删除会员</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>

@endsection