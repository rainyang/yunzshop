@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
<div class="right-titpos">
	<ul class="add-snav">
		<li class="active"><a href="#">交易设置</a></li>
	</ul>
</div>
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
			<div class="panel-heading">
				自动关闭未付款订单
			</div>
			<div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动关闭未付款订单天数</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="trade[close_order_days]" class="form-control" value="{{ $set['close_order_days'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单下单未付款，n天后自动关闭，空为不自动关闭</span>
                    </div>
                </div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自动关闭未付款订单执行间隔时间</label>
					<div class="col-sm-5">
						<div class="input-group">
							<input type="text" name="trade[close_order_time]" class="form-control" value="{{ $set['close_order_time'] }}" />
							<div class="input-group-addon">分钟</div>
						</div>
						<span class='help-block'>执行自动关闭未付款订单操作的间隔时间，如果为空默认为 60分钟 执行一次关闭到期未付款订单</span>
					</div>
				</div>
			</div>

			<div class="panel-heading">
				自动收货
			</div>
			<div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动收货天数</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="trade[receive]" class="form-control" value="{{ $set['receive'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单发货后，用户收货的天数，如果在期间未确认收货，系统自动完成收货，空为不自动收货</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动收货执行间隔时间</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="trade[receive_time]" class="form-control" value="{{ $set['receive_time'] }}" />
                            <div class="input-group-addon">分钟</div>
                        </div>
                        <span class='help-block'>执行自动收货操作的间隔时间，如果为空默认为 60分钟 执行一次自动收货</span>
                    </div>
                </div>
			</div>
			<div class="panel-heading">
				交易设置
			</div>	

            <div class='panel-body'>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成订单多少天内可申请退款</label>
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="trade[refund_days]" class="form-control" value="{{ $set['refund_days'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单完成后 ，用户在x天内可以发起退款申请，设置0天不允许完成订单退款</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款说明</label>
                    <div class="col-sm-5">
                        <textarea  name="trade[refund_content]" class="form-control" value="{{ $set['refund_content'] }}" >{{ $set['refund_content'] }}</textarea>
                        <span class='help-block'>用户在申请退款页面的说明</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示用户下单飘窗</label>
                    <div class="col-sm-5">
                        <label class='radio-inline'><input type='radio' name='trade[show_last_order]' value='1' @if ($set['show_last_order'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[show_last_order]' value='0' @if (empty($set['show_last_order'])) checked @endif /> 关闭</label>
                        <span class='help-block'>是否显示商城用户下单飘窗提示</span>
                    </div>
                </div>
			</div>
			<div class="panel-heading">
				余额设置
			</div>	

            <div class='panel-body'>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启账户充值</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='trade[close_recharge]' value='0' @if (empty($set['close_recharge'])) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[close_recharge]' value='1' @if ($set['close_recharge'] == '1') checked @endif/> 关闭</label>
                        <span class='help-block'>是否允许用户对账户余额进行充值</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额转账</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='trade[transfer]' value='0' @if (empty($set['transfer']))    checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[transfer]' value='1' @if ($set['transfer']=='1') checked @endif /> 关闭</label>
                        <span class='help-block'>是否允许用户对账户余额进行转账</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额提现</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='trade[withdraw]' value='1' @if ($set['withdraw'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[withdraw]' value='0' @if ($set['withdraw'] == 0) checked @endif /> 关闭</label>
                        <span class='help-block'>是否允许用户将余额提出</span>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class='input-group'>
                                <input type="text" name="trade[poundage]" class="form-control" value="{{ $set['poundage'] }}" onkeyup="value=value.replace(/[^\d.]/g,'');if(value >= 100){value=''}" onafterpaste="value=value.replace(/[^\d.]/g,'');if(value >= 100){value=''"/>
                                <span class='input-group-addon'>%</span>
                            </div>
                        </div>
                    </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现限制</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="trade[withdraw_limit]" class="form-control" value="{{ $set['withdraw_limit'] }}" />
                        <span class='help-block'>余额满多少才能提现,空或0不限制</span>
                    </div>
                </div>
			</div>
			<div class="panel-heading">
				积分比例
			</div>	

            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值积分比例</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class='input-group'>
                            <input type="text" name="trade[money]" class="form-control" value="{{ $set['money'] }}" />
                            <span class='input-group-addon'>元 增加</span>
                            <input type="text" name="trade[point]" class="form-control" value="{{ $set['point'] }}" />
                            <span class='input-group-addon'>分</span>
                        </div>
                        <span class='help-block'>用户充值获得的积分</span>
                    </div>
                </div>
			</div>
			<div class="panel-heading">
				收货地址
			</div>	

            <div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">获取微信共享收货地址</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='trade[share_address]' value='0' @if ($set['share_address'] == 0) checked @endif /> 关闭</label>
                        <label class='radio-inline'><input type='radio' name='trade[share_address]' value='1' @if ($set['share_address'] == 1) checked @endif/> 开启</label>
                        <span class='help-block'>是否在用户添加收货地址时候获取用户的微信收货地址</span>
                    </div>
                </div>
                <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启乡镇及街道地址选择
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <label class='radio-inline'><input type='radio' name='trade[is_street]' value='1' @if ($set['is_street'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[is_street]' value='0' @if ($set['is_street'] == 0) checked @endif /> 关闭</label>

                    </div>
                </div>
			</div>
			<div class="panel-heading">
				支付日志 
			</div>	

			<div class='panel-body'>
          
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付回调日志</label>
                    <div class="col-sm-5">

                        <label class='radio-inline'><input type='radio' name='trade[pay_log]' value='0' @if ($set['pay_log'] == 0) checked @endif /> 关闭</label>
                        <label class='radio-inline'><input type='radio' name='trade[pay_log]' value='1' @if ($set['pay_log'] == 1) checked @endif/> 开启</label>
                        <span class='help-block'>支付回调日志，如果出现手机付款而后台显示待付款状态，请开启日志，查错误</span>
                        <span class='help-block'>日志路径为 addon/sz_yi/data/paylog/[公众号ID]</span>

                    </div>
                </div>
      
			</div>
			<div class="form-group"></div>
            <div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-9 col-xs-12">
					<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
				</div>
            </div>



	 </div>     
</form>
</div>  
@endsection
