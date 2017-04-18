@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <div class="panel panel-default">
                <div class='panel-body'>
                    <div style='height:100px;width:110px;float:left;'>
                        <img src='{$member['avatar']}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
                    </div>
                    <div style='float:left;height:100px;overflow: hidden'>
                        昵称: {$member['nickname']}<br/>
                        姓名: {$member['realname']} <br/>
                        手机号: {$member['mobile']} /  微信号: {$member['weixin']}<br/>
                        下级会员(非分销商): <span style='color:red'>{$level11}</span> 人    <br/>
                        下级分销商: 总共 <span style='color:red'>{$member['agentcount']}</span> 人
                        {if $this->set['level']>=1}一级: <span style='color:red'>{$level1} </span>  人{/if}
                        {if $this->set['level']>=2}二级: <span style='color:red'>{$level2}</span>  人{/if}
                        {if $this->set['level']>=3}三级: <span style='color:red'>{$level3}</span> 人{/if}
                        点击:  <span style='color:red'>{$member['clickcount']}</span> 次
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
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">成为代理时间</label>
                                <div class="col-sm-7 col-lg-9 col-xs-12">
                                    <div class="col-sm-3">
                                        <label class='radio-inline'>
                                            <input type='radio' value='0' name='searchtime' {if $_GPC['searchtime']=='0'}checked{/if}>不搜索
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' value='1' name='searchtime' {if $_GPC['searchtime']=='1'}checked{/if}>搜索
                                        </label>
                                    </div>
                                    {php echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d  H:i', $endtime)),true);}
                                </div>
                            </div>
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
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐人</label>
                                <div class="col-sm-3">
                                    <select name='parentid' class='form-control'>
                                        <option value=''></option>
                                        <option value='0' {if $_GPC['parentid']=='0'}selected{/if}>总店</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text"  class="form-control" name="parentname" value="{$_GPC['parentname']}" placeholder='推荐人昵称/姓名/手机号'/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销商等级</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <select name='agentlevel' class='form-control'>
                                        <option value=''></option>
                                        {loop $agentlevels $level}
                                        <option value='{$level['id']}' {if $_GPC['agentlevel']==$level['id']}selected{/if}>{$level['levelname']}</option>
                                        {/loop}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">下级层级</label><div class="col-sm-8 col-lg-9 col-xs-12"><select name='level' class='form-control'>
                                        <option value=''>所有下线</option>
                                        {if $this->set['level']>=1}<option value='1' {if $_GPC['level']=='1'}selected{/if}>一级下线</option>{/if}
                                        {if $this->set['level']>=2}<option value='2' {if $_GPC['level']=='2'}selected{/if}>二级下线</option>{/if}
                                        {if $this->set['level']>=3}<option value='3' {if $_GPC['level']=='3'}selected{/if}>三级下线</option>{/if}
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>

                                <div class="col-sm-3">
                                    <select name='isagent' class='form-control'>
                                        <option value=''>是否分销商</option>
                                        <option value='0' {if $_GPC['isagent']=='0'}selected{/if}>不是</option>
                                        <option value='1' {if $_GPC['isagent']=='1'}selected{/if}>是</option>
                                    </select>
                                </div>
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
    </div>

@endsection