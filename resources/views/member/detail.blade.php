@extends('layouts.base')

@section('content')
<link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;会员详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('member.member.update', ['id'=> $member['uid']])}}" method='post' class='form-horizontal'>
                <input type="hidden" name="id" value="{{$member['uid']}}">
                <input type="hidden" name="op" value="detail">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="yun_shop" />
                <input type="hidden" name="do" value="member" />
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$member['avatar']}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                                {{$member['nickname']}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='data[level_id]' class='form-control'>
                                    @foreach ($levels as $level)
                                    <option value='{{$level['id']}}'
                                            @if($member['yz_member']['level_id']==$level['id'])
                                            selected
                                    @endif>{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员分{{ $member['yz_member']['group_id'] }}组</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='data[group_id]' class='form-control'>
                                    <option value='0' selected>无1分组</option>
                                    @foreach($groups as $group)
                                    <option value='{{$group['id']}}' @if($member['yz_member']['group_id'] == $group['id']) selected @endif >{{ $group['group_name'] }}{{ $group['id'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[realname]" class="form-control" value="{{$member['realname']}}"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$member['mobile']}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[alipayname]" class="form-control" value="{{$member['yz_member']['alipayname']}}"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝帐号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[alipay]" class="form-control" value="{{$member['yz_member']['alipay']}}"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分</label>
                            <div class="col-sm-3">
                                <div class='input-group'>
                                    <div class=' input-group-addon' >{{$member['credit1']}}</div>
                                    <div class='input-group-btn'>
                                        <a class='btn btn-success' href="{{yzWebUrl('finance.point-recharge',['id'=>$member['uid']])}}">充值</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额</label>
                            <div class="col-sm-3">
                                <div class='input-group'>
                                    <div class=' input-group-addon' >{{$member['credit2']}}</div>
                                    <div class='input-group-btn'><a class='btn btn-success' href="{{yzWebUrl('finance.balance.recharge', ['member_id'=>$member['uid']])}}">充值</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成交订单数</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if($member['has_one_order']['total'])
                                    {{$member['has_one_order']['total']}}
                                    @else
                                        0
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成交金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if($member['has_one_order']['sum'])
                                    {{$member['has_one_order']['sum']}}
                                        @else
                                        0
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册时间</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{date('Y-m-d H:i:s', $member['createtime'])}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">关注状态</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if(!$member['has_one_fans']['followed'])
                                    @if(empty($member['uid']))
                                    <label class='label label-default'>未关注</label>
                                    @else
                                    <label class='label label-warning'>取消关注</label>
                                    @endif
                                    @else
                                    <label class='label label-success'>已关注</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">黑名单</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" name="data[is_black]" value="1" @if($member['yz_member']['is_black']==1)
                                    checked
                                    @endif>是</label>
                                <label class="radio-inline" ><input type="radio" name="data[is_black]" value="0" @if($member['yz_member']['is_black']==0)
                                    checked
                                    @endif>否</label>
                                <span class="help-block">设置黑名单后，此会员无法访问商城</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="data[content]" class='form-control'>{{$member['yz_member']['content']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success" />
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <input type="button" class="btn btn-default back" name="submit" onclick="location.href='{{yzWebUrl('member.member.index')}}'" value="返回列表"  />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection