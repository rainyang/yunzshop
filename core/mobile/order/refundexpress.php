<?php
//起点源码社区 http://www.qdyma.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
global $_W, $_GPC;
function sortByTime($a, $b)
{
	if ($a["ts"] == $b["ts"]) {
		return 0;
	} else {
		return $a["ts"] > $b["ts"] ? 1 : -1;
	}
}

function getList($id, $postid)
{
	$result = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$id}&fromWeb=null&postid={$postid}";
	load()->func("communication");
	$info = ihttp_request($result);
	$content = $info["content"];
	if (empty($content)) {
		return array();
	}
	preg_match_all("/\\<p\\>&middot;(.*)\\<\\/p\\>/U", $content, $list);
	if (!isset($list[1])) {
		return false;
	}
	return $list[1];
}

$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$openid = m("user")->getOpenid();
$uniacid = $_W["uniacid"];
$orderid = intval($_GPC["id"]);
if ($_W["isajax"]) {
	if ($operation == "display") {
		$order = pdo_fetch("select refundid from " . tablename("sz_yi_order") . " where id=:id and uniacid=:uniacid and openid=:openid limit 1", array(":id" => $orderid, ":uniacid" => $uniacid, ":openid" => $openid));
		if (empty($order)) {
return show_json(0);
		}
		$refundid = $order["refundid"];
		$refund = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id and uniacid=:uniacid  limit 1", array(":id" => $refundid, ":uniacid" => $uniacid));
		$set = set_medias(m("common")->getSysset("shop"), "logo");
return show_json(1, array("order" => $order, "refund" => $refund, "set" => $set));
	} else if ($operation == "step") {
		$express = trim($_GPC["express"]);
		$expresssn = trim($_GPC["expresssn"]);
		$arr = getList($express, $expresssn);
		if (!$arr) {
			$arr = getList($express, $expresssn);
			if (!$arr) {
	return show_json(1, array("list" => array()));
			}
		}
		$len = count($arr);
		$step1 = explode("<br />", str_replace("&middot;", "", $arr[0]));
		$step2 = explode("<br />", str_replace("&middot;", "", $arr[$len - 1]));
		for ($i = 0; $i < $len; $i++) {
			if (strtotime(trim($step1[0])) > strtotime(trim($step2[0]))) {
				$row = $arr[$i];
			} else {
				$row = $arr[$len - $i - 1];
			}
			$step = explode("<br />", str_replace("&middot;", "", $row));
			$list[] = array("time" => trim($step[0]), "step" => trim($step[1]), "ts" => strtotime(trim($step[0])));
		}
return show_json(1, array("list" => $list));
	}
}
include $this->template("order/refundexpress");