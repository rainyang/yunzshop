<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/14
 * Time: 下午5:41
 */

/**
 * 生成Ping++客户端支付凭证
 *
 **/

if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;
$uniacid        = $_W['uniacid'];

$setdata = m("cache")->get("sysset");

$set     = unserialize($setdata['sets']);

$setting = uni_setting($_W['uniacid'], array('payment'));
$pay = $setting['payment'];

require_once('../addons/sz_yi/plugin/pingpp/init.php');

    //$input_data = $this->para;
    /*if (empty($this->para)) {
        $input_data = array(
            'channel' => 'alipay',//alipay,upmp,bfb,upacp,wx
            'amount' => '101',
            'subject' => 'ddddd',
            'body' => 'dddd',
            'app_id' => 'app_unrfnH1qH8KOf14K',
            'order_no' => '22910252521',
        );
    }*/

  $input_data = array(
      'channel' => $_POST['channel'],
      'amount' => $_POST['amount'],
      'order_no' => $_POST['ordersn'],
      'openid' => $_POST['token']
  );

    if (empty($input_data['openid'])) {
        $openid = m('user')->getOpenid();

        $input_data['openid'] = $openid;
    }

    if (empty($input_data['channel'])) {
        echo 'channel is empty';
        exit();
    }
    $channel = strtolower($input_data['channel']);

    $api_key = $pay['ping']['secret'];//'sk_test_88ynL0SG8SCKOm5K00z9ufD0';

    $orderNo = $input_data['order_no'];

    if (substr($orderNo,0,2) == 'RC') { //充值
        $log = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid limit 1', array(
            ':uniacid' => $uniacid,
            ':logno' => $orderNo
        ));
        if (empty($log)) {
            return show_json(0, '充值出错!');
        }
        if (!empty($log['status'])) {
            return show_json(0, '已经充值成功,无需重复支付!');
        }

        $amount = (int)($log['money'] * 100);
        $subject = $log['title'];
        $body = $log['title'];
    } else {
        $ordersn_general          = pdo_fetchcolumn('select ordersn_general from ' . tablename('sz_yi_order') . ' where (ordersn=:ordersn or ordersn_general=:ordersn) and uniacid=:uniacid and openid=:openid limit 1', array(
            ':ordersn' => $orderNo,
            ':uniacid' => $uniacid,
            ':openid' => $input_data['openid']
        ));


        $order_price          = pdo_fetchcolumn('select sum(price) from ' . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid limit 1', array(
            ':ordersn_general' => $ordersn_general,
            ':uniacid' => $uniacid,
            ':openid' => $input_data['openid']
        ));

        //统一订单号
        $orderNo = $ordersn_general;

        $amount = (int)($order_price * 100);

        $subject = '商品订单';
        $body = '商品订单';
    }



    $app_id = $pay['ping']['partner'];
    //$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
    $extra = array();
    switch ($channel) {
        case 'alipay_wap':
            $extra = array(
                'success_url' => 'http://www.yourdomain.com/success',
                'cancel_url' => 'http://www.yourdomain.com/cancel'
            );
            break;
        case 'upmp_wap':
            $extra = array(
                'result_url' => 'http://www.yourdomain.com/result?code='
            );
            break;
        case 'bfb_wap':
            $extra = array(
                'result_url' => 'http://www.yourdomain.com/result?code=',
                'bfb_login' => true
            );
            break;
        case 'upacp_wap':
            $extra = array(
                'result_url' => 'http://www.yourdomain.com/result'
            );
            break;
        case 'wx_pub':
            $extra = array(
                'open_id' => 'Openid'
            );
            break;
        case 'wx_pub_qr':
            $extra = array(
                'product_id' => 'Productid'
            );
            break;
        case 'yeepay_wap':
            $extra = array(
                'product_category' => '1',
                'identity_id' => 'your identity_id',
                'identity_type' => 1,
                'terminal_type' => 1,
                'terminal_id' => 'your terminal_id',
                'user_ua' => 'your user_ua',
                'result_url' => 'http://www.yourdomain.com/result'
            );
            break;
        case 'jdpay_wap':
            $extra = array(
                'success_url' => 'http://www.yourdomain.com',
                'fail_url' => 'http://www.yourdomain.com',
                'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf'
            );
            break;
    }

    \Pingpp\Pingpp::setApiKey($api_key);
    try {
        $ch = \Pingpp\Charge::create(
            array(
                'subject' => $subject,
                'body' => $body,
                'amount' => $amount,
                'order_no' => $orderNo,
                'currency' => 'cny',
                'extra' => $extra,
                'channel' => $channel,
                'client_ip' => $_SERVER['REMOTE_ADDR'],
                'app' => array('id' => $app_id)
            )
        );

        echo $ch;
    } catch (\Pingpp\Error\Base $e) {
        // 捕获报错信息
        if ($e->getHttpStatus() != NULL) {
            header('Status: ' . $e->getHttpStatus());
            echo $e->getHttpBody();
        }
    }


