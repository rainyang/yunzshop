<?php
global $_W, $_GPC;

$set = $this->getSet();
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$_GPC["page"] = $_GPC["page"]?$_GPC["page"]:'1';
//print_R($set);exit;
if ($_GPC['quantity']) {
	$quantity = intval($_GPC['quantity']);
	if ($quantity > 0) {

		$last = pdo_fetch("select * from" . tablename('sz_yi_beneficence') . " where uniacid = '" .$_W['uniacid'] . "' order by create_time desc ");
		$last['create_time'] = !empty($last['create_time'])?$last['create_time']:time()-86400;
		for ($i=1; $i <= $quantity; $i++) { 
			$name = getRandChar(2)."****".getRandChar(2);
			$money = rand($_GPC['low'],$_GPC['high']);
			$create_time = rand($last['create_time'],time());
			
			$data = array(
				'uniacid' 		=> $_W['uniacid'],
			    'name' 			=> $name,
			    'money' 		=> $money,
				'create_time'	=> $create_time
			);
			pdo_insert('sz_yi_beneficence', $data);
		}
		message('添加成功!', referer(), 'success');
	} else {
		message('添加失败!', referer(), 'error');
	}
	
}

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $total = pdo_fetchall("select * from" . tablename('sz_yi_beneficence') . " where uniacid = '" .$_W['uniacid'] . "' ");
    $total = count($total);
    $list_group=pdo_fetchall("select *  from" . tablename('sz_yi_beneficence') . "  where uniacid = '" .$_W['uniacid'] . "' order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    foreach ($list_group as &$row) {
        $row['create_time']     = date("Y-m-d H:i:s",$row['create_time']);
    }
    unset($row);
    $pager = pagination($total, $pindex, $psize);

load()->func('tpl');
include $this->template('beneficence');

function getRandChar($length){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }

   return $str;
  }