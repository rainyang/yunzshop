<?php
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'display';
if ($op == 'post') {
    if (checksubmit('submit')) {
    	$total = intval($_GPC['total']);
    	$arr = array(
    		'uniacid'		=> $_W['uniacid'],
    		'total'			=> $total,
    		'money'			=> trim($_GPC['money']),
    		'createtime'	=> time()
    		);
    	pdo_insert('sz_yi_gift_card', $arr);
    	$gift_id = pdo_insertid();
    	for ($i=1; $i <= $total; $i++) { 
			$data = array();
			$data['uniacid'] 			= $_W['uniacid'];
	        $data['money'] 				= trim($_GPC['money']);
	        $data['gift_id']			= $gift_id;
	        $data['isday'] 				= $_GPC['isday'];
	        $data['timestart'] 			= strtotime($_GPC['timestart']);
	        $data['timeend'] 			= strtotime($_GPC['timeend']);
	        $data['validity_period']	= intval($_GPC['validity_period'])*60*60*24;
	        $data['cdkey'] 				= $this->model->getCdkey();
	        pdo_insert('sz_yi_card_data', $data);
		}
		message('生成代金卡'.$total.'张成功!', $this->createPluginWebUrl('card/index'), 'success');
    }
} elseif ($op == 'display') {
	$pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $sql = "SELECT * FROM " . tablename('sz_yi_gift_card') . " WHERE uniacid=:uniacid";
    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
    $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('sz_yi_gift_card') . " WHERE uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
    if ($_GPC['export'] == '1') {
        m('excel')->export($list, array(
            "title" => "代金卡发放期数-" . date('Y-m-d-H-i', time()),
            "columns" => array(
                array(
                    'title' => '期数',
                    'field' => 'id',
                    'width' => 12
                ),
                array(
                    'title' => '数量',
                    'field' => 'total',
                    'width' => 12
                ),
                array(
                    'title' => '面额',
                    'field' => 'money',
                    'width' => 12
                ),
                array(
                    'title' => '发放时间',
                    'field' => 'createtime',
                    'width' => 12
                )
            )
        ));
    }
    $pager = pagination($total, $pindex, $psize);
} else if ($op == 'detail') {
	$gift_id = intval($_GPC['gift_id']);
	$pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $parms = array(
    		':uniacid'	=> $_W['uniacid'],
    		':gift_id'	=> $gift_id
    	);
    $sql = "SELECT * FROM " . tablename('sz_yi_card_data') . " WHERE uniacid=:uniacid AND gift_id=:gift_id";
    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall($sql, $parms);
    $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('sz_yi_card_data') . " WHERE uniacid=:uniacid AND gift_id=:gift_id", $parms);
    foreach ($list as $key => &$value) {
    	if ($value['isday'] == 1) {
    		$value['time'] = $value['validity_period']/60/60/24 . "天";
    	} else if ($value['isday'] == 2) {
    		$value['time'] = date('Y-m-d H:i:s',$value['timestart']) . "至" . date('Y-m-d H:i:s',$value['timestart']);
    	}
    }
    unset($value);
    if ($_GPC['export'] == '1') {
    	$gift_id = intval($_GPC['gift_id']);
        m('excel')->export($list, array(
            "title" => "第{$gift_id}期代金卡-" . date('Y-m-d-H-i', time()),
            "columns" => array(
                array(
                    'title' => '期数',
                    'field' => 'gift_id',
                    'width' => 12
                ),
                array(
                    'title' => 'cdkey',
                    'field' => 'cdkey',
                    'width' => 20
                ),
                array(
                    'title' => '面额',
                    'field' => 'money',
                    'width' => 12
                ),
                array(
                    'title' => '有效期',
                    'field' => 'time',
                    'width' => 30
                )
            )
        ));
    }
    $pager = pagination($total, $pindex, $psize);
}
include $this -> template('index');
?>