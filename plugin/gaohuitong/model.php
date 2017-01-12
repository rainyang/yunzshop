<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('gaohuitongModel')) {
    class gaohuitongModel extends PluginModel
    {
      public function gaohuitong_build($params, $info = array(), $type = 0, $openid = '')
      {
          global $_W;

          require_once (dirname(__FILE__) . "/vendor/GatewaySubmit.class.php");


          /* 业务代码 */
          $busi_code = "PAY";
          /* 商户号 */
          $merchant_no = $info['merchant_no'];
          /* 终端号 */
          $terminal_no = $info['terminal_no'];
          /* 商户密钥KEY */
          $key = $info['merchant_key'];

          //商户订单号，这里用当前时间毫秒数作为订单号，商户应当保持订单号在商户系统的唯一性
          $order_no = $params['tid'];

          /* 商品金额,以元为单位   */
          $amount = $params['fee'];

          $resource = "../addons/sz_yi/plugin/gaohuitong/core/mobile/returnUrl.php";
          $redest =  "../addons/sz_yi/plugin/gaohuitong/core/mobile/{$_W['uniacid']}/returnUrl.php";

          $this->_moveFile($resource, $redest);

          /* 交易完成后页面即时通知跳转的URL  */
          $return_url = $_W['siteroot'] . "addons/sz_yi/plugin/gaohuitong/core/mobile/{$_W['uniacid']}/returnUrl.php";

          $source = "../addons/sz_yi/plugin/gaohuitong/core/mobile/notifyUrl.php";
          $dest =  "../addons/sz_yi/plugin/gaohuitong/core/mobile/{$_W['uniacid']}/notifyUrl.php";

          $this->_moveFile($source, $dest);

          /* 接收后台通知的URL */
          $notify_url = $_W['siteroot'] . "addons/sz_yi/plugin/gaohuitong/core/mobile/{$_W['uniacid']}/notifyUrl.php";

          /* 货币代码，人民币：CNY    */
          $currency_type = 'CNY';

          /*创建订单的客户端IP（消费者电脑公网IP，用于防钓鱼支付）   */
          //$client_ip = $_SERVER['REMOTE_ADDR'];
          $client_ip = '';

          /* 签名算法（暂时只支持MD5）   */
          $sign_type = 'SHA256';

           //直连银行参数
          //$bank_code = "ICBC";  //直连招商银行参数值
          $bank_code = "";

          //订单备注，该信息使用64位编码提交服务器，并将在支付完成后随支付结果原样返回
          $memo = $_W['uniacid'];
          $base64_memo = base64_encode($memo);

          $sett_currency_type = 'CNY';

          $product_name = $params['title'];

          /* 支付请求对象 */
          $gatewaySubmit = new GatewaySubmit();
          $gatewaySubmit->setKey($key);
          $gatewaySubmit->setGateUrl($info['server']);   //测试服务器

          //设置支付参数
          $gatewaySubmit->setParameter("busi_code", $busi_code);		        //业务代码
          $gatewaySubmit->setParameter("merchant_no", $merchant_no);		    //商户号
          $gatewaySubmit->setParameter("terminal_no", $terminal_no);		    //终端号
          $gatewaySubmit->setParameter("order_no", $order_no);	   			//商户订单号
          $gatewaySubmit->setParameter("amount", $amount);			   		//商品金额,以元为单位
          $gatewaySubmit->setParameter("return_url", $return_url);		   	//交易完成后页面即时通知跳转的URL
          $gatewaySubmit->setParameter("notify_url", $notify_url);		  	//接收后台通知的URL
          $gatewaySubmit->setParameter("currency_type", $currency_type);	   	//货币种类
          $gatewaySubmit->setParameter("client_ip",$client_ip); 	//创建订单的客户端IP（消费者电脑公网IP，用于防钓鱼支付）
          $gatewaySubmit->setParameter("sign_type", $sign_type);			   	//签名算法（暂时只支持SHA256）

          $gatewaySubmit->setParameter("sett_currency_type", $sett_currency_type);
          $gatewaySubmit->setParameter("product_name", $product_name);

          //业务可选参数
          $gatewaySubmit->setParameter("bank_code", $bank_code);	        	//直连银行参数，例子是直接转跳到招商银行时的参数
          $gatewaySubmit->setParameter("base64_memo", $base64_memo);		   	//订单备注的BASE64编码

          //请求的URL
          $requestUrl = $gatewaySubmit->getRequestURL();

          return array(
              'url' => $requestUrl,
          );
      }

       /**
       * 复制支付通知文件
       *
       * @param $source
       * @param $dest
       */
      private function _moveFile($source, $dest)
      {
          if (!is_dir(dirname($dest))) {
              @mkdir(dirname($dest), 0777, true);
          }
          @copy($source, $dest);
      }

      public function refund($info, $pay_ordersn, $out_refund_no,$refundmoney = 0)
      {
          require dirname(__FILE__) . "/vendor/GatewaySubmit.class.php";

          load()->func('communication');

          /* 业务代码 */
          $busi_code = "REFUND";
          /* 商户号 */
          $merchant_no = $info['merchant_no'];
          /* 终端号 */
          $terminal_no = $info['terminal_no'];
          /* 密钥 */
          $key = $info['merchant_key'];

//商户订单号，这里用当前时间毫秒数作为订单号，商户应当保持订单号在商户系统的唯一性
          $order_no = $pay_ordersn;

          $refund_no = $out_refund_no;

          /* 商品金额,以元为单位   */
          $amount = $refundmoney;

          $refund_amount = $refundmoney;

          /* 签名算法（暂时只支持MD5）   */
          $sign_type = 'SHA256';

          /* 支付请求对象 */
          $gatewaySubmit = new GatewaySubmit();
          $gatewaySubmit->setKey($key);
          $gatewaySubmit->setGateUrl($info['server']);                          //测试服务器

          //设置支付参数
          $gatewaySubmit->setParameter("busi_code", $busi_code);		        //业务代码
          $gatewaySubmit->setParameter("merchant_no", $merchant_no);		    //商户号
          $gatewaySubmit->setParameter("terminal_no", $terminal_no);		    //终端号
          $gatewaySubmit->setParameter("order_no", $order_no);	   			    //商户订单号
          $gatewaySubmit->setParameter("refund_no", $refund_no);	   			//商户退款单号
          $gatewaySubmit->setParameter("amount", $amount);			   	        //订单总金额,以元为单位
          $gatewaySubmit->setParameter("refund_amount", $refund_amount);	    //退款金额,以元为单位
          $gatewaySubmit->setParameter("sign_type", $sign_type);			   	//签名算法（暂时只支持SHA256）

          $requestUrl = $gatewaySubmit->getRequestURL();

          $res = ihttp_get($requestUrl);

          if ($res['code'] == 200) {
              $xml_data = $res['content'];
              $xml = simplexml_load_string($xml_data);

              $paylog = "\r\n-------------------------------------------------\r\n";
              $paylog .= "orderno: " . $order_no . "\r\n";
              $paylog .= "paytype: gaohuitong\r\n";
              $paylog .= "type: refund\r\n";
              $paylog .= "data: " . json_encode($xml) . "\r\n";
              m('common')->paylog($paylog);

              if ($xml->resp_code != '00' && $xml->resp_desc != 'Success') {
                  message("退款失败");
              }
          }
      }
    }
}
