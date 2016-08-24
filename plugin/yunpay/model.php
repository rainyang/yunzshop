<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('YunpayModel')) {
    class YunpayModel extends PluginModel
    {
        function getYunpay(){
            global $_W;
            $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
            $set     = unserialize($setdata['sets']);

            return $set['pay']['yunpay'];
        }
        function isYunpayNotify($gpc) {
            global $_W;
            
            $yunpay = $this->getYunpay();
        
            if (!isset($yunpay) or !$yunpay['switch']) {
                return false;
            }

            $prestr = $gpc['i1'] . $gpc['i2'].$yunpay['partner'].$yunpay['secret'];
            $mysgin = md5($prestr);

           if($mysgin != $gpc['i3']) {
                return false;
           }
           else{
               return true;
           }
        }

        public function yunpay_build($params, $yunpay = array(), $type = 0, $openid = '')
        {
            global $_W;
            $tid                   = $params['tid'].':'.$_W['uniacid'] . ':' . $type;
           
            if (empty($type)) {
                $nourl = $_W['siteroot'] . "addons/sz_yi/plugin/yunpay/notify.php";
                $reurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=order&p=pay&op=returnyunpay&openid=" . $openid;
            } else {
                $nourl = $_W['siteroot'] . "addons/sz_yi/plugin/yunpay/notify.php";
                $reurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=member&p=recharge&op=returnyunpay&openid=" . $openid;
            }
            
           //商户订单号
            $out_trade_no = $tid;//商户网站订单系统中唯一订单号，必填

            //订单名称
            $subject = $params['title'];//必填

            //付款金额
            $total_fee = $params['fee'];//必填 需为整数

            //订单描述
            $body = $_W['uniacid'] . ':' . $type;
           
            //商品展示地址
            $orurl = "";
            //需http://格式的完整路径，不能加?id=123这类自定义参数，如原网站带有 参数请彩用伪静态或短网址解决

            //商品形象图片地址
            $orimg = "";
            //需http://格式的完整路径，必须为图片完整地址
            $parameter = array(
            "partner" => trim($yunpay['partner']),
            "seller_email"	=> $yunpay['account'],
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "nourl"	=> $nourl,
            "reurl"	=> $reurl,
            "orurl"	=> $orurl,
            "orimg"	=> $orimg
            );
            
            foreach ($parameter as $pars) {
                $myparameter.=$pars;
            }

            $sign=md5($myparameter.'i2eapi'.$yunpay['secret']);
            $mycodess="<form name='yunsubmit' action='http://www.cyh.org.cn/i2eorder/yunpay/newapi.php' accept-charset='utf-8' method='get'><input type='hidden' name='body' value='".$parameter['body']."'/><input type='hidden' name='out_trade_no' value='".$parameter['out_trade_no']."'/><input type='hidden' name='partner' value='".$parameter['partner']."'/><input type='hidden' name='seller_email' value='".$parameter['seller_email']."'/><input type='hidden' name='subject' value='".$parameter['subject']."'/><input type='hidden' name='total_fee' value='".$parameter['total_fee']."'/><input type='hidden' name='nourl' value='".$parameter['nourl']."'/><input type='hidden' name='reurl' value='".$parameter['reurl']."'/><input type='hidden' name='orurl' value='".$parameter['orurl']."'/><input type='hidden' name='orimg' value='".$parameter['orimg']."'/><input type='hidden' name='sign' value='".$sign."'/></form><script>document.forms['yunsubmit'].submit();</script>";
            return $mycodess;
        }

        
        function perms()
        {
            return array(
                'yunpay' => array(
                    'text' => $this->getName(), 
                    'isplugin' => true, 
                    'child' => array(
                        'yunpay' => array(
                            'text' => '云支付'
                            )
                        )
                ));
        }
    }
}
