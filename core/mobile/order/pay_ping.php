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
  );

    if (empty($input_data['channel'])) {
        echo 'channel is empty';
        exit();
    }
    $channel = strtolower($input_data['channel']);

    $api_key = 'sk_live_DW1Wr5TO0e940ufDqH4S08K0';//'sk_test_88ynL0SG8SCKOm5K00z9ufD0';

    $orderNo = $input_data['order_no'];
    $order_info = array('total'=>1,'name'=>'测试订单');
    $amount = (int)($order_info['total'] * 100);
    $subject = $order_info['name'];
    $body = $order_info['name'];

    $app_id = 'app_unrfnH1qH8KOf14K';
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
        } else {
            echo $e->getMessage();
        }
    }


