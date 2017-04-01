@extends('layouts.base')

@section('content')
    <div class="panel panel-default">
        <div class='panel-heading'>
            提现者信息
        </div>
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{tomedia($item->hasOneMember->avatar)}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{$item->hasOneMember->nickname}}
                    <b>姓名:</b>
                    {{$item->hasOneMember->realname}}
                    <b>手机号:</b>
                    {{$item->hasOneMember->mobile}}
                </p>
                <p><b>分销等级:</b> {$agentLevel['levelname']} (
                    {if $this->set['level']>=1}一级比例: <span style='color:blue'>{$agentLevel['commission1']}%</span>{/if}
                    {if $this->set['level']>=2}二级比例: <span style='color:blue'>{$agentLevel['commission2']}%</span>{/if}
                    {if $this->set['level']>=3}三级比例: <span style='color:blue'>{$agentLevel['commission3']}%</span>{/if}
                    )</p>
                <p>
                    <b>下级:</b> 总共 <span style='color:red'>{$member['agentcount']}</span> 人
                    {if $this->set['level']>=1}<b>一级:</b><span style='color:red'>{$member['level1']}</span> 人{/if}
                    {if $this->set['level']>=2}<b>二级:</b> <span style='color:red'>{$member['level2']}</span> 人{/if}
                    {if $this->set['level']>=3}<b>三级: </b><span style='color:red'>{$member['level3']}</span> 人{/if}
                    点击: <span style='color:red'>{$member['clickcount']}</span> 次

                    <b>累计佣金: </b><span style='color:red'>{$member['commission_total']}</span> 元
                    <b>待审核佣金: </b><span style='color:red'>{$member['commission_apply']}</span> 元
                    <b>待打款佣金: </b><span style='color:red'>{$member['commission_check']}</span> 元
                    <b>结算期佣金: </b><span style='color:red'>{$member['commission_lock']}</span> 元 </p>
                <p>
                    <b>申请佣金: </b><span style='color:red'>{$apply['commission']}</span> 元
                    <b>打款方式: </b>
                    {if empty($apply['type'])}
                    <span class='label label-primary'>余额{if $apply['payauto']}(自动打款){/if}</span>
                    {else if !empty($apply['type']) && $apply['type']=='3'}
                    <span class='label label-success'>支付宝</span>
                    <span class='label' style="color:#000">支付宝账号：{$apply['alipay']}  姓名：{$apply['alipayname']}</span>
                    {else}
                    <span class='label label-success'>微信{if $apply['payauto']}(自动打款){/if}</span>
                    {/if}

                </p>
                <p>
                    <b>状态: </b>
                    {if $apply['status']==1}
                    <span class='label label-primary'>申请中</span>
                    {else if $apply['status']==2}
                    <span class='label label-success'>审核完毕，准备打款</span>
                    {else if $apply['status']==3}
                    <span class='label label-warning'>已打款</span>
                    {else if $apply['status']==4}
                    <span class='label label-warning'>已到款</span>
                    {/if}

                    {if $apply['status']>=1}<b>申请时间: </b> {php echo date('Y-m-d H:i', $apply['applytime'])}{/if}
                    {if $apply['status']>=2}<b>审核时间: </b> {php echo date('Y-m-d H:i', $apply['checktime'])}{/if}
                    {if $apply['status']>=3}<b>打款时间: </b> {php echo date('Y-m-d H:i', $apply['paytime'])}{/if}
                    {if $apply['status']>=4}<b>到款时间: </b> {php echo date('Y-m-d H:i', $apply['finshtime'])}{/if}
                </p>

            </div>
        </div>

        {{--<div class='panel-heading'>--}}
        {{--提现申请订单信息 共计 <span style="color:red; ">{$totalcount}</span> 个订单 , 金额共计 <span style="color:red; ">{$totalmoney}</span> 元 佣金总计 <span style="color:red; ">{$totalcommission}</span> 元{if $apply['credit20'] > 0}<span style="color:#FF5C03;">(包括已消费{$apply['credit20']})</span>{/if}--}}
        {{--{if $status==1 && cv('commission.apply.check')}--}}
        {{--<a href="javascript:;" onclick="checkall(true)" class="btn btn-primary">批量审核通过</a>--}}
        {{--{if $apply['credit20'] <= 0}--}}
        {{--<a href="javascript:;" onclick="checkall(false)" class="btn btn-danger">批量审核不通过</a>--}}
        {{--{/if}--}}
        {{--{/if}--}}
        {{--</div>--}}
        {{--<div class='panel-body'>--}}
        {{--<table class="table table-hover">--}}
        {{--<thead class="navbar-inner">--}}
        {{--<tr>--}}
        {{--<th>订单号</th>--}}
        {{--<th>总金额</th>--}}
        {{--<th>商品金额</th>--}}
        {{--<th>运费</th>--}}
        {{--<th>付款方式</th>--}}
        {{--<th>下单时间</th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}
        {{--{loop $list $row}--}}
        {{--<tr  style="background: #eee">--}}
        {{--<td>{$row['ordersn']}</td>--}}
        {{--<td>{$row['price']}</td>--}}
        {{--<td>{$row['goodsprice']}</td>--}}
        {{--<td>运费：{$row['dispatchprice']}</td>--}}
        {{--<td>{if $row['paytype'] == 1}--}}
        {{--<span class="label label-danger">余额支付</span>--}}
        {{--{elseif $row['paytype'] == 11}--}}
        {{--<span class="label label-default">后台付款</span>--}}
        {{--{elseif $row['paytype'] == 21}--}}
        {{--<span class="label label-success">在线支付</span>--}}
        {{--{elseif $row['paytype'] == 22}--}}
        {{--<span class="label label-danger">支付宝支付</span>--}}
        {{--{elseif $row['paytype'] == 22}--}}
        {{--<span class="label label-primary">银联支付</span>--}}
        {{--{elseif $row['paytype'] == 3}--}}
        {{--<span class="label label-primary">货到付款</span>--}}
        {{--{/if}--}}
        {{--</td>--}}

        {{--<td>{php echo date('Y-m-d H:i',$row['createtime'])}</td>--}}
        {{--</tr>--}}
        {{--<tr >--}}

        {{--<td colspan="6">--}}
        {{--<table width="100%">--}}
        {{--<thead class="navbar-inner">--}}
        {{--<tr>--}}
        {{--<th style='width:60px;'>商品</th>--}}
        {{--<th></th>--}}
        {{--<th>单价</th>--}}
        {{--<th>数量</th>--}}
        {{--<th>总价</th>--}}
        {{--<th>佣金</th>--}}

        {{--</tr>--}}
        {{--</thead>--}}
        {{--<tbody>--}}
        {{--{loop $row['goods'] $g}--}}
        {{--<tr>--}}
        {{--<td style='height:60px;'><img src="{php echo tomedia($g['thumb'])}" style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;"></td>--}}
        {{--<td><span>{$g['title']}</span><br/><span>{$g['optionname']}</span>--}}
        {{--</td>--}}
        {{--{if p('hotel') && $row['order_type']=='3'}--}}
        {{--<td>--}}
        {{--房价: {php echo $g['price']/$g['total']}<br/>--}}
        {{--折扣后 ：{php echo $g['realprice']/$g['total']}<br/>--}}
        {{--押金:{php echo $row['depositprice']}--}}
        {{--</td>--}}
        {{--<td>{$g['total']}</td>--}}
        {{--<td>--}}
        {{--<strong>--}}
        {{--进店日期:{php echo date('Y-m-d',$row['btime'])}<br/>--}}
        {{--离店日期:{php echo date('Y-m-d',$row['etime'])}</strong>--}}
        {{--</td>--}}
        {{--<td>--}}
        {{--{else}--}}
        {{--<td>:原价: {php echo $g['price']/$g['total']}<br/>折扣后:{php echo $g['realprice']/$g['total']}</td>--}}
        {{--<td>{$g['total']}</td>--}}
        {{--<td><strong>原价:{php echo round($g['price'],2)}<br/>折扣后:{php echo round($g['realprice'],2)}</strong></td>--}}
        {{--<td>--}}
        {{--{/if}--}}
        {{--{if $this->set['level']>=1 && $row['level']==1}<p>--}}
        {{--<div class='input-group'>--}}
        {{--<span class='input-group-addon'>一级佣金</span>--}}
        {{--<span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission1']}</span>--}}
        {{--<span class='input-group-addon'>状态</span>--}}
        {{--<span class='input-group-addon' style='background:#fff'>--}}
        {{--{if $g['status1']==-1}--}}
        {{--<span class='label label-default'>未通过</span>--}}
        {{--{elseif $g['status1']==1}--}}
        {{--{if $apply['credit20'] <= 0}--}}
        {{--<label class='radio-inline'><input type='radio'  class='status1' value='-1'  name="status1[{$g['id']}]" /> 不通过</label>--}}
        {{--{/if}--}}
        {{--<label class='radio-inline'><input type='radio'  value='2'   name="status1[{$g['id']}]" {if $apply['credit20'] > 0}checked="checked"{/if} /> 通过</label>--}}

        {{--{elseif $g['status1']==2}--}}
        {{--<span class='label label-success'>通过</span>--}}
        {{--{elseif $g['status1']==3}--}}
        {{--<span class='label label-warning'>已打款</span>--}}
        {{--{/if}--}}
        {{--</span>--}}
        {{--<span class='input-group-addon'>备注</span>--}}
        {{--<input type='text' class='form-control' name='content1[{$g['id']}]' style='width:200px;' value="{$g['content1']}">--}}

        {{--</div></p>--}}
        {{--{/if}--}}

        {{--{if $this->set['level']>=2  && $row['level']==2}<p>--}}

        {{--<div class='input-group'>--}}
        {{--<span class='input-group-addon'>二级佣金</span>--}}
        {{--<span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission2']}</span>--}}
        {{--<span class='input-group-addon'>状态</span>--}}
        {{--<span class='input-group-addon' style='background:#fff'>--}}
        {{--{if $g['status2']==-1}--}}
        {{--<span class='label label-default'>未通过</span>--}}
        {{--{elseif $g['status2']==1}--}}
        {{--{if $apply['credit20'] <= 0}--}}
        {{--<label class='radio-inline'><input type='radio' class='status2' value='-1'  name="status2[{$g['id']}]" /> 不通过</label>--}}
        {{--{/if}--}}
        {{--<label class='radio-inline'><input type='radio'  value='2'  name="status2[{$g['id']}]" {if $apply['credit20'] > 0}checked="checked"{/if} /> 通过</label>--}}

        {{--{elseif $g['status2']==2}--}}
        {{--<span class='label label-success'>通过</span>--}}
        {{--{elseif $g['status2']==3}--}}
        {{--<span class='label label-warning'>已打款</span>--}}
        {{--{/if}--}}
        {{--</span>--}}
        {{--<span class='input-group-addon'>备注</span>--}}
        {{--<input type='text' class='form-control' name='content2[{$g['id']}]' style='width:200px;' value="{$g['content2']}">--}}
        {{--</div>--}}
        {{--</p>--}}
        {{--{/if}--}}
        {{--{if $this->set['level']>=2  && $row['level']==3}<p>--}}

        {{--<div class='input-group'>--}}
        {{--<span class='input-group-addon'>三级佣金</span>--}}
        {{--<span class='input-group-addon' style='background:#fff;width:80px;'>{$g['commission3']}</span>--}}
        {{--<span class='input-group-addon'>状态</span>--}}
        {{--<span class='input-group-addon' style='background:#fff'>--}}
        {{--{if $g['status3']==-1}--}}
        {{--<span class='label label-default'>未通过</span>--}}
        {{--{elseif $g['status3']==1}--}}
        {{--{if $apply['credit20'] <= 0}--}}
        {{--<label class='radio-inline'><input type='radio' class='status3' value='-1' name="status3[{$g['id']}]" /> 不通过</label>--}}
        {{--{/if}--}}
        {{--<label class='radio-inline'><input type='radio' value='2' name="status3[{$g['id']}]" {if $apply['credit20'] > 0}checked="checked"{/if} /> 通过</label>--}}

        {{--{elseif $g['status3']==2}--}}
        {{--<span class='label label-success'>通过</span>--}}
        {{--{elseif $g['status3']==3}--}}
        {{--<span class='label label-warning'>已打款</span>--}}
        {{--{/if}--}}
        {{--</span>--}}
        {{--<span class='input-group-addon'>备注</span>--}}
        {{--<input type='text' class='form-control' name='content3[{$g['id']}]' style='width:200px;'  value="{$g['content3']}">--}}
        {{--</div>--}}
        {{--</p>--}}
        {{--{/if}--}}
        {{--</td>--}}
        {{--</tr>--}}
        {{--{/loop}--}}
        {{--</tbody></table>--}}
        {{--</td></tr>--}}
        {{--{/loop}--}}
        {{--</table>--}}
        {{--</div>--}}

        {{--{if $apply['status']==2}--}}
        {{--<div class='panel-heading'>--}}
        {{--打款信息--}}
        {{--</div>--}}
        {{--<div class='panel-body'>--}}
        {{--此次佣金总额:  <span style='color:red'>{$totalcommission}</span> 元 {if $apply['credit20'] > 0}已消费<span style='color:red'>{$apply['credit20']}</span> 元{/if}   应该打款：<span style='color:red'>{php echo $totalpay-$apply['credit20']}</span> 元--}}
        {{--</div>--}}
        {{--{/if}--}}

        {{--{if $apply['status']==3}--}}
        {{--<div class='panel-heading'>--}}
        {{--打款信息--}}
        {{--{if $apply['reason']!=''}--}}
        {{--<span style="color:red"> 失败原因：{$apply['reason']}</span>--}}
        {{--{/if}--}}
        {{--</div>--}}
        {{--<div class='panel-body'>--}}
        {{--此次佣金总额:  <span style='color:red'>{$totalcommission}</span> 元 {if $apply['credit20'] > 0}已消费<span style='color:red'>{$apply['credit20']}</span> 元{/if}   实际打款：<span style='color:red'>{php echo $totalpay-$apply['credit20']}</span> 元--}}
        {{--</div>--}}
        {{--{/if}--}}

    </div>
@endsection