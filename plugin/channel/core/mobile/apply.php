<?php
global $_W, $_GPC;
$set = $this->getSet();
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$member 					= m('member')->getMember($openid);
	$channelinfo 				= $this->model->getInfo($openid);
	$commission_ok 				= $channelinfo['channel']['commission_ok'] + $channelinfo['channel']['lower_order_money'] - $channelinfo['channel']['dispatchprice'];
	$cansettle 					= $commission_ok >= floatval($set['setapplyminmoney']);
	$member['commission_ok'] 	= number_format($commission_ok, 2);
	$setapplycycle				= $set['setapplycycle'] *3600;
	$time 						= time();
	$last_apply					= pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_apply') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND (apply_time+{$setapplycycle}>{$time}) ORDER BY id DESC");
	if ($_W['ispost']) {
		if ($commission_ok <= 0) {
			show_json(0, '没有可提现金额');
		}
		$time = time();
		//出货单
		$channel_goods = pdo_fetchall("SELECT og.id FROM " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) WHERE og.uniacid={$_W['uniacid']} AND og.channel_id={$member['id']} AND o.status=3 AND og.channel_apply_status=0");
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply_ordergoods_ids ='';
		if (!empty($channel_goods)) {
			$apply_ordergoods_ids =array();
			foreach ($channel_goods as $key => $value) {
	            $apply_ordergoods_ids[] = $value['id'];
	        }
	        $apply_ordergoods_ids = implode(',', $apply_ordergoods_ids);
		}
        //推荐单
        $info = $this->model->getInfo($openid);
        $apply_cmaorders_ids = $info['channel']['lower_order_ids'];
        $cma_orders = $info['channel']['cma_orders'];
        /*$cma_orders = array();
        if (!empty($channelinfo['channel']['lower_openids'])) {
        	$cma_orders = pdo_fetchall("SELECT o.id FROM " . tablename('sz_yi_order') . " o LEFT JOIN " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid={$_W['uniacid']} AND o.status>=3 AND og.ischannelpay=1 AND o.openid in ({$channelinfo['channel']['lower_openids']})");
        	if (!empty($cma_orders)) {
        		$apply_cmaorders_ids = array();
        		foreach ($cma_orders as $value) {
	        		$apply_cmaorders_ids[] = $value['id'];
	        	}
	        	$apply_cmaorders_ids = implode(',', $apply_cmaorders_ids);
        	}
        }*/
        //自提运费单
        $apply_selforders_ids = '';
        $selforders = pdo_fetchall("SELECT id FROM " . tablename('sz_yi_order') . " WHERE uniacid={$_W['uniacid']} AND status>=3 AND openid='{$openid}' AND ischannelself=1");
        if (!empty($selforders)) {
        	$apply_selforders_ids = array();
        	foreach ($selforders as $value) {
	        	$apply_selforders_ids[] = $value['id'];
	        }
	        $apply_selforders_ids = implode(',', $apply_selforders_ids);
        }
        $order_ids = array_merge($cma_orders, $selforders);
		$apply = array(
			'openid'				=> $openid,
			'mid'					=> $member['id'],
			'type'					=> $_GPC['type'],
			'applyno'				=> $applyno,
			'apply_money'			=> $commission_ok,
			'apply_time'			=> $time,
			'status' 				=> 1,
			'uniacid'				=> $_W['uniacid'],
			'apply_ordergoods_ids' 	=> $apply_ordergoods_ids,
			'apply_cmaorders_ids'	=> $apply_cmaorders_ids,
			'apply_selforders_ids'	=> $apply_selforders_ids
			);
		pdo_insert('sz_yi_channel_apply', $apply);
		@file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($apply, 1), FILE_APPEND);
		if( pdo_insertid() ) {
			foreach ($order_ids as $key => $value) {
				pdo_update('sz_yi_order', array('iscmas' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value));
			}
			foreach ($channel_goods as $key => $value) {
				pdo_update('sz_yi_order_goods', array('channel_apply_status' => 1), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
			}
			$tmp_sp_goods 				= $channel_goods;
			$tmp_sp_goods['applyno'] 	= $applyno;
			@file_put_contents(IA_ROOT . "/addons/sz_yi/data/channel_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
		}

		$returnurl 	= urlencode($this->createPluginMobileUrl('channel/orderj'));
		$infourl 	= $this->createPluginMobileUrl('channel/orderj', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 0 ? '余额' : '微信'), TM_COMMISSION_APPLY);
		show_json(1, '已提交,请等待审核!');
	}
	$returnurl 	= urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl 	= $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'last_apply' => $last_apply, 'set' => $this->set, 'channel_info' => $channelinfo, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('apply');
