@extends('layouts.base')

@section('content')
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
                    </div>
                </div>
            </div>


            <form method='get' class='form-horizontal'>
                <div class="panel panel-info">
                    <div class="panel-heading">筛选</div>
                    <div class="panel-body">
                        <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                            <input type="hidden" name="c" value="site" />
                            <input type="hidden" name="a" value="entry" />
                            <input type="hidden" name="m" value="sz_yi" />
                            <input type="hidden" name="do" value="plugin" />
                            <input type="hidden" name="p" value="commission" />
                            <input type="hidden" name="method" value="agent" />
                            <input type="hidden" name="op" value="user" />
                            <input type="hidden" name="id" value="{$agentid}" />
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
            </form>

            <div class="panel panel-default">
                <div class="panel-heading">总数：{$total}</div>
                <div class="panel-body">
                    <table class="table table-hover"   style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:5%;'>会员ID</th>
                            <th style='width:18%;text-align: center;'>推荐人</th>
                            <th style='width:10%;text-align: center;'>粉丝</th>
                            <th style='width:12%;'>姓名<br/>手机号码</th>
                            <th style='width:12%;text-align: center;'>状态{ifp 'commission.agent.check'}<br/>（点击审核){/if}</th>
                            <th style='width:14%;'>时间</th>
                            <th style='width:10%;text-align: center;'>关注</th>
                            <th style='width:8%'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {loop $list $row}
                        <tr>
                            <td>{$row['id']}</td>
                            <td  style="text-align: center;" {if !empty($row['agentid'])}title='ID: {$row['agentid']}'{/if}>
                            {if empty($row['agentid'])}
                            {if $row['isagent']==1}
                            <label class='label label-primary'>总店</label>
                            {else}
                            <label class='label label-default'>暂无</label>
                            {/if}
                            {else}
                            <img src='{$row['parentavatar']}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /><br/> {$row['parentname']}
                            {/if}
                            </td>
                            <td  style="text-align: center;">
                                {if !empty($row['avatar'])}
                                <img src='{$row['avatar']}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /><br/>
                                {/if}
                                {if empty($row['nickname'])}未更新{else}{$row['nickname']}{/if}

                            </td>

                            <td>{$row['realname']} <br/> {$row['mobile']}</td>

                            <td>
                                {if $row['isagent']==1}
                                {if $row['status']==0}
                                {if $row['agentblack']==1}
                                <span class="label label-default" style='color:#fff;background:black'>黑名单</span>
                                {else}

                                {ifp 'commission.agent.check'}
                                <a class="label label-default" href="{php echo $this->createPluginWebUrl('commission/agent',array('id' => $row['id'],'op'=>'check'))}" onclick="return confirm('确认要审核此分销商吗?')">未审核</a>
                                {else}
                                <span class="label label-default">未审核</span>
                                {/if}
                                {/if}
                                {else}
                                <span class="label label-success">已审核</span>
                                {/if}
                                {else}
                                -
                                {/if}

                            </td>
                            <td>注册时间：{php echo date('Y-m-d H:i',$row['createtime'])}<br/>
                                代理时间：{if !empty($row['agenttime'])}{php echo date('Y-m-d H:i',$row['agenttime'])}{/if}
                            </td>
                            <td>  {if empty($row['followed'])}
                                {if empty($row['uid'])}
                                <label class='label label-default'>未关注</label>
                                {else}
                                <label class='label label-warning'>取消关注</label>
                                {/if}
                                {else}
                                <label class='label label-success'>已关注</label>
                                {/if}</td>
                            <td  style="overflow:visible;">

                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 99999'>
                                        {ifp 'member.member.view'}<li><a href="{php echo $this->createWebUrl('member',array('op'=>'detail', 'id' => $row['id']));}" title='会员信息'><i class='fa fa-user'></i> 会员信息</a></li>	{/if}
                                        {ifp 'commission.agent.view'}<li><a href="{php echo $this->createPluginWebUrl('commission/agent/detail',array('id' => $row['id']));}" title='详细信息'><i class='fa fa-edit'></i> 详细信息</a>	</li>	{/if}
                                        {ifp 'commission.agent.order'}<li><a  href="{php echo $this->createWebUrl('order',array('op'=>'display','agentid' => $row['id']));}" title='推广订单'><i class='fa fa-list'></i> 推广订单</a></li>{/if}
                                        {ifp 'commission.agent.user'}<li><a  href="{php echo $this->createPluginWebUrl('commission/agent/user',array('id' => $row['id']));}"  title='推广下线'><i class='fa fa-users'></i> 推广下线</a></li>{/if}
                                        {ifp 'commission.agent.agentblack'}
                                        {if $row['agentblack']==1}
                                        <li><a href="{php echo $this->createPluginWebUrl('commission/agent/agentblack',array('id' => $row['id'],'black'=>0));}" title='取消黑名单'><i class='fa fa-minus-square'></i> 取消黑名单</a></li>
                                        {else}
                                        <li><a href="{php echo $this->createPluginWebUrl('commission/agent/agentblack',array('id' => $row['id'],'black'=>1));}" title='设置黑名单'><i class='fa fa-minus-circle'></i> 设置黑名单</a></li>
                                        {/if}
                                        {/if}
                                        {ifp 'commission.agent.delete'}<li><a href="{php echo $this->createPluginWebUrl('commission/agent/delete',array('id' => $row['id']));}" title="删除" onclick="return confirm('确定要删除该会员吗？');"><i class='fa fa-remove'></i> &nbsp;删除分销商</a></li>{/if}

                                    </ul>
                                </div>


                            </td>
                        </tr>
                        {/loop}
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>

@endsection