<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$popenid        = m('user')->islogin();
$openid = $openid?$openid:$popenid;
$uniacid   = $_W['uniacid'];
$shopset   = m('common')->getSysset('shop');
if(empty($openid) || strstr($openid, 'http-equiv=refresh')){
    Header('Location:' . $this->createMobileUrl('member/login')); 
}
if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and f.uniacid = :uniacid and f.openid=:openid and f.deleted=0';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $openid);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_member_favorite') . " f where 1 {$condition}";
		$total = pdo_fetchcolumn($sql, $params);
		$list = array();
		if (!empty($total)) {
            if (!empty($_GPC['favorite_id'])) {
                $condition .= ' and f.id < :favorite_id';
                $params['favorite_id'] = $_GPC['favorite_id'];
            }
			$sql = 'SELECT f.id,f.goodsid,g.title,g.thumb,g.marketprice,g.productprice FROM ' . tablename('sz_yi_member_favorite') . ' f ' . ' left join ' . tablename('sz_yi_goods') . ' g on f.goodsid = g.id ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			$list = set_medias($list, 'thumb');
		}
		return show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
	} else if ($operation == 'set') {
		$id = intval($_GPC['id']);
		$goods = pdo_fetch('select id from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($goods)) {
			return show_json(0, '商品未找到');
		}
		$data = pdo_fetch('select id,deleted from ' . tablename('sz_yi_member_favorite') . ' where uniacid=:uniacid and goodsid=:id and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
		if (empty($data)) {
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'goodsid' => $id, 'createtime' => time());
			pdo_insert('sz_yi_member_favorite', $data);
			return show_json(1, array('isfavorite' => true));
		} else {
			if (empty($data['deleted'])) {
				pdo_update('sz_yi_member_favorite', array('deleted' => 1), array('id' => $data['id'], 'uniacid' => $_W['uniacid'], 'openid' => $openid));
				return show_json(1, array('isfavorite' => false));
			} else {
				pdo_update('sz_yi_member_favorite', array('deleted' => 0), array('id' => $data['id'], 'uniacid' => $_W['uniacid'], 'openid' => $openid));
				return show_json(1, array('isfavorite' => true));
			}
		}
	} else if ($operation == 'remove' && $_W['ispost']) {
		$ids = $_GPC['ids'];
        $goods_ids = $_GPC['goods_ids'];

        if(!is_array($ids)&&!empty($ids)){
            $ids = explode(',',$ids);
            $condition = 'and id in (' . implode(',', $ids) . ')';

        }
        if(!is_array($goods_ids)&&!empty($goods_ids)){
            $goods_ids = explode(',',$goods_ids);
            $condition = 'and goodsid in (' . implode(',', $goods_ids) . ')';
        }
        if (empty($ids) || !is_array($ids)) {
            if (empty($goods_ids) || !is_array($goods_ids)) {
                return show_json(0, '参数错误');
            }
        }
		$sql = "update " . tablename('sz_yi_member_favorite') . ' set deleted=1 where uniacid=:uniacid and openid=:openid '.$condition;
		pdo_query($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
        return show_json(1);
	}
}
include $this->template('shop/favorite');
