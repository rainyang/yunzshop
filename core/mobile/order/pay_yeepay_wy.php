<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/5
 * Time: 下午1:27
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
if (empty($openid)) {
    $openid = $_GPC['openid'];
}
$member  = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
$logid   = intval($_GPC['logid']);
$shopset = m('common')->getSysset('shop');

if (!empty($orderid)) {
    $order = pdo_fetch("select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
    if (empty($order)) {
        show_json(0, '订单未找到!');
    }
    $order_price = pdo_fetchcolumn("select price from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn_general' => $order['ordersn_general'], ':uniacid' => $uniacid, ':openid' => $openid));
    $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
        ':uniacid' => $uniacid,
        ':module' => 'sz_yi',
        ':tid' => $order['ordersn_general']
    ));
    if (!empty($log) && $log['status'] != '0') {
        show_json(0, '订单已支付, 无需重复支付!');
    }
    $param_title     = $shopset['name'] . "订单: " . $order['ordersn_general'];
    $yeepay         = array(
        'success' => false
    );
    $params          = array();
    $params['tid']   = $log['tid'];
    $params['user']  = $openid;
    $params['fee']   = $order_price;
    $params['title'] = $param_title;
    $params['name'] = $shopset['name'];
    load()->func('communication');
    load()->model('payment');

    yeepay_build($params, array(), $openid);
}

function yeepay_build($params, $yeepay = array(), $openid = '')
{
    global $_W;

    include(IA_ROOT . "/addons/sz_yi/core/inc/plugin/vendor/yeepay/wy/yeepayCommon.php");

    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    $set     = unserialize($setdata['sets']);
    $merchantaccount= $set['pay']['merchantaccount'];
    $merchantPublicKey= $set['pay']['merchantPublicKey'];
    $merchantPrivateKey= $set['pay']['merchantPrivateKey'];
    $yeepayPublicKey= $set['pay']['yeepayPublicKey'];

    $p1_MerId		= $set['pay']['merchantaccount'];
    $merchantKey	= $set['pay']['merchantKey'];

    $tid                   = $params['tid'];

    $source = "../addons/sz_yi/payment/yeepay/wy_notify.php";
    $dest =  "../addons/sz_yi/payment/yeepay/{$_W['uniacid']}/wy_notify.php";

    moveFile($source, $dest);

    $reurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=order&p=pay&op=returnyeepay_wy&openid=" . $openid;

#	商家设置用户购买商品的支付信息.
##易宝支付平台统一使用GBK/GB2312编码方式,参数如用到中文，请注意转码
    $data = array();
#业务类型
    $data['p0_Cmd']				= "Buy";
#商户编号
    $data['p1_MerId']           = $p1_MerId;
    #	商户订单号,选填.
##若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
    $data['p2_Order']			= $tid;
#	支付金额,必填.
##单位:元，精确到分.
    $data['p3_Amt']			    = floatval($params['fee']);
#	交易币种,固定值"CNY".
    $data['p4_Cur']				= "CNY";
#	商品名称
##用于支付时显示在易宝支付网关左侧的订单产品信息.
    $data['p5_Pid']			  = '';
#	商品种类
    $data['p6_Pcat']		  = '';
#	商品描述
    $data['p7_Pdesc']		  = '';
#	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
    $data['p8_Url']			  = $reurl;
#	送货地址
    $data['p9_SAF']			  = '';
#	商户扩展信息
##商户可以任意填写1K 的字符串,支付成功时将原样返回.
    $data['pa_MP']			 = '';
#	支付通道编码
##默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.
    $data['pd_FrpId']		 = '';
#	订单有效期
    $data['pm_Period']	     = '7';
#	订单有效期单位
##默认为"day": 天;
    $data['pn_Unit']	     = 'day';
#	应答机制
    $data['pr_NeedResponse'] = '1';
#	用户姓名
    $data['pt_UserName']	 = '';
#	身份证号
    $data['pt_PostalCode']	 = '';
#	地区
    $data['pt_Address']		 = '';
#	银行卡号
    $data['pt_TeleNo']		 = '';
#	手机号
    $data['pt_Mobile']		 = '';
# 邮件地址
    $data['pt_Email']		 = '';
# 用户标识
    $data['pt_LeaveMessage'] = '';
#签名串
    $hmac                    = HmacMd5(implode($data),$merchantKey);

    $sHtml = "<form id='yeepay' name='yeepay' action='{$reqURL_onLine}' method='post'>";


    foreach($data as $k => $v) {
        $sHtml.= "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />\n";
    }
    $sHtml.= "<input type=\"hidden\" name=\"hmac\" value=\"{$hmac}\" />\n";
    $sHtml = $sHtml."</form>";

    $sHtml = $sHtml."<script>document.forms['yeepay'].submit();</script>";
    //$sHtml = $sHtml;
    echo $sHtml;exit;

}

/**
 * 复制支付通知文件
 *
 * @param $source
 * @param $dest
 */
function moveFile($source, $dest)
{
    if (!is_dir(dirname($dest))) {
        (@mkdir(dirname($dest), 0777, true));
    }
    @copy($source, $dest);
}