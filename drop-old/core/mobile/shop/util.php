<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'category') {
	$category = m('shop')->getCategory();
	$category2 = m('shop')->getCategory2();
	return show_json(1, array('category' => $category,'category2'=>$category2));
} else if($operation == 'category2'){
	$category = m('shop')->getCategory2();
	return show_json(1, array('category' => $category));
} else if ($operation == 'areas') {
	$areas = m('cache')->getArray('areas', 'global');
	if (!is_array($areas)) {
		require_once SZ_YI_INC . 'json/xml2json.php';
		$file = IA_ROOT . "/addons/sz_yi/static/js/dist/area/Area.xml";
		$content = file_get_contents($file);
		$json = xml2json::transformXmlStringToJson($content);
		$areas = json_decode($json, true);
		m('cache')->set('areas', $areas, 'global');
	}
	die(json_encode($areas));
} else if ($operation == 'search') {
	$keywords = trim($_GPC['keywords']);
	$goods = m('goods')->getList(array('pagesize' => 100000, 'keywords' => trim($_GPC['keywords'])));
	return show_json(1, array('list' => $goods));
} else if ($operation == 'comment') {
	$goodsid = intval($_GPC['goodsid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 5;
	$condition = ' and uniacid = :uniacid and goodsid=:goodsid and deleted=0';
	$params = array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid);
	$sql = 'SELECT id,nickname,headimgurl,level,content,createtime, images,append_images,append_content,reply_images,reply_content,append_reply_images,append_reply_content ' . ' FROM ' . tablename('sz_yi_order_comment') . ' where 1 ' . $condition . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$row) {
		$row['headimgurl'] = tomedia($row['headimgurl']);
		$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		$images = unserialize($row['images']);
		$row['images'] = is_array($images) ? set_medias($images) : array();
		$append_images = unserialize($row['append_images']);
		$row['append_images'] = is_array($append_images) ? set_medias($append_images) : array();
		$reply_images = unserialize($row['reply_images']);
		$row['reply_images'] = is_array($reply_images) ? set_medias($reply_images) : array();
		$append_reply_images = unserialize($row['append_reply_images']);
		$row['append_reply_images'] = is_array($append_reply_images) ? set_medias($append_reply_images) : array();
	}
	unset($row);
	return show_json(1, array('list' => $list, 'pagesize' => $psize));
} else if ($operation == 'recommand') {
	$goods = m('goods')->getList(array('pagesize' => 4, 'isrecommand' => true, 'random' => true));
	return show_json(1, array('list' => $goods));
} else if ($operation == 'benqi') {
	$sql = 'SELECT ic.*, m.realname, m.avatar FROM ' . tablename('sz_yi_indiana_consumerecord') . ' ic 
	 left join ' . tablename('sz_yi_member') . ' m on (ic.openid = m.openid)  
	 where ic.uniacid = :uniacid and ic.period_num=:period_num ORDER BY ic.id DESC ';
	$params = array(
		":uniacid" => $_W['uniacid'],
		":period_num" => $_GPC['period_num']
	);
	$list = set_medias(pdo_fetchall($sql, $params),'avatar');
	foreach ($list as &$row) {
		$row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
	}
	unset($row);

	return show_json(1, array('list' => $list));
} else if ($operation == 'wangqi') {
	$sql = 'SELECT * FROM ' . tablename('sz_yi_indiana_period') . ' where uniacid = :uniacid and goodsid=:goodsid and status = 3 ORDER BY period ASC ';
	$params = array(
		":uniacid" => $_W['uniacid'],
		":goodsid" => $_GPC['goodsid']
	);
	$list = set_medias(pdo_fetchall($sql, $params),'avatar');
	foreach ($list as &$row) {
		$row['jiexiao_time'] = date('Y-m-d H:i:s', $row['jiexiao_time']);
	}
	unset($row);
	return show_json(1, array('list' => $list));
}
