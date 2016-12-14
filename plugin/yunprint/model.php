<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('IP','api163.feieyun.com');
define('PORT','80');
define('HOSTNAME','/FeieServer/');
//以下2项是平台相关的设置，您不需要更改
define('FEYIN_HOST','my.feyin.net');
define('FEYIN_PORT', 80);
include_once 'HttpClient.class.php';
if (!class_exists('YunprintModel')) {
	class YunprintModel extends PluginModel
	{
        public $client;

        function __construct() {
            $this->client = new HttpClient(IP, PORT);
        }

        function feiyin_print($print_order,$member_code,$device_no,$key, $offers)
        {   
            $orderinfo = "";
            $shopname = '';
            $address = unserialize($print_order['address']);
            if (!empty($offers)) {
                if ($offers['createtime'] == 1) {
                    $createtime = date('Y-m-d H:i', $print_order['createtime']);
                    $orderinfo .= "订购时间：{$createtime}\n";
                }
                if ($offers['realname'] == 1) {
                    $orderinfo .= "客户姓名：{$address['realname']}\n";
                }
                if ($offers['mobile'] == 1) {
                    $orderinfo .= "联系方式：{$address['mobile']}\n";
                }
                if ($offers['address'] == 1) {
                    $orderinfo .= "配送地址：{$address['province']}{$address['city']}{$address['area']}{$address['address']}\n";
                }
                if ($offers['remark'] == 1) {
                    $orderinfo .= "订单备注：{$print_order['remark']}\n";
                }
                if ($offers['diy'] == 1) {
                    $diydata = unserialize($print_order['diyformdata']);
                    $diyformfields = unserialize($print_order['diyformfields']);
                    if (!empty($diyformfields)) {
                        foreach ($diyformfields as $k => $v) {
                            if (!empty($diydata)) {
                                $orderinfo .= "{$v['tp_name']}：{$diydata[$k]}\n";
                            } else {
                                $orderinfo .= "{$v['tp_name']}：{$v['tp_default']}\n";
                            }
                        }
                    }
                }
                if ($offers['goodsinfo'] == 1) {
                    $goods = "";
                    $num = 1;
                    foreach ($print_order['goods'] as $value) {
                        $goods .= " " . $num . " " . $value['title'] . "\n" . $value['marketprice'] . " " . $value['total'] . " " . $value['price'] . "\n";
                        $num++;
                    }
                    $orderinfo .= "-------------------------序号 商品名称 单价 数量  金额{$goods}\n-------------------------";
                }
                if ($offers['goodsprice'] == 1) {
                    $orderinfo .= "商品合计：         {$print_order['goodsprice']}\n";
                }
                if ($offers['discountprice'] == 1) {
                    $orderinfo .= "会员折扣：           {$print_order['discountprice']}\n";
                }
                if ($offers['deductcredit2'] == 1) {
                    $orderinfo .= "余额抵扣：           {$print_order['deductcredit2']}\n";
                }
                if ($offers['deductenough'] == 1) {
                    $orderinfo .= "满额优惠：           {$print_order['deductenough']}\n";
                }
                if ($offers['deductprice'] == 1) {
                    $orderinfo .= "积分抵扣：           {$print_order['deductprice']}\n";
                }
                if ($offers['couponprice'] == 1) {
                    $orderinfo .= "优惠项目：           {$print_order['couponprice']}\n";
                }
                if ($offers['dispatchprice'] == 1) {
                    $orderinfo .= "运费：              {$print_order['dispatchprice']}\n";
                }
                if ($offers['shopname'] == 1) {
                    $shopname = "商城：{$print_order['shopname']}\n-------------------------";
                }
            }
            $orderinfo .= "实际支付：         {$print_order['price']}\n";
            if (!empty($offers)) {
                if ($offers['usersign'] == 1) {
                    $orderinfo .= "-------------------------客户签收：";
                }
            }
            $msgNo = $print_order['ordersn'];
            $freeMessage = array(
                'memberCode'=>$member_code, 
                'msgDetail'=>
                "{$shopname}订单号:{$print_order['ordersn']}{$orderinfo}",
                'deviceNo'=>$device_no, 
                'msgNo'=>$msgNo
            );

             $this->sendFreeMessage($freeMessage,$key);

            return $msgNo;
        }

        function sendFreeMessage ($msg,$key) 
        {
            $msg['reqTime'] = number_format(1000*time(), 0, '', '');
            $content = $msg['memberCode'].$msg['msgDetail'].$msg['deviceNo'].$msg['msgNo'].$msg['reqTime'].$key;
            $msg['securityCode'] = md5($content);
            $msg['mode']=2;
            return $this->sendMessage($msg);
        }

        function sendMessage($msgInfo)
        {
            $clientt = new HttpClient(FEYIN_HOST,FEYIN_PORT);
            if(!$clientt->post('/api/sendMsg',$msgInfo)){ //提交失败
                return 'faild';
            }else{
                return $clientt->getContent();
            }
        }

        function feie_print($print_order,$printer_sn,$key,$times,$url, $offers)
        {
            //标签说明："<BR>"为换行符,"<CB></CB>"为居中放大,"<B></B>"为放大,"<C></C>"为居中,"<L></L>"为字体变高
            //"<W></W>"为字体变宽,"<QR></QR>"为二维码,"<CODE>"为条形码,后面接12个数字
            $orderinfo = "";
            $shopname = '';
            $address = unserialize($print_order['address']);
            if (!empty($offers)) {
                if ($offers['logo'] == 1) {
                    $orderinfo .= "<LOGO>";
                }
                if ($offers['shopname'] == 1) {
                    $orderinfo .= "<CB>{$print_order['shopname']}</CB>================================<BR>";
                }
            }
            $orderinfo .= "订单编号：{$print_order['ordersn']}<BR>";
            if (!empty($offers)) {
                if ($offers['createtime'] == 1) {
                    $createtime = date('Y-m-d H:i', $print_order['createtime']);
                    $orderinfo .= "订购时间：{$createtime}<BR>";
                }
                if ($offers['realname'] == 1) {
                    $orderinfo .= "客户姓名：{$address['realname']}<BR>";
                }
                if ($offers['mobile'] == 1) {
                    $orderinfo .= "联系方式：{$address['mobile']}<BR>";
                }
                if ($offers['address'] == 1) {
                    $orderinfo .= "配送地址：{$address['province']}{$address['city']}{$address['area']}{$address['address']}<BR>";
                }
                if ($offers['remark'] == 1 && !empty($print_order['remark'])) {
                    $orderinfo .= "订单备注：{$print_order['remark']}<BR>";
                }
                if ($offers['diy'] == 1) {
                    $diydata = unserialize($print_order['diyformdata']);
                    $diyformfields = unserialize($print_order['diyformfields']);
                    if (!empty($diyformfields)) {
                        foreach ($diyformfields as $k => $v) {
                            if (!empty($diydata)) {
                                $orderinfo .= "{$v['tp_name']}：{$diydata[$k]}<BR>";
                            } else {
                                $orderinfo .= "{$v['tp_name']}：{$v['tp_default']}<BR>";
                            }
                        }
                    }
                }
                if ($offers['goodsinfo'] == 1) {
                    $goods = "";
                    $num = 1;
                    foreach ($print_order['goods'] as $value) {
                        $goods .= " " . $num . " " . $value['title'] . "<BR>" . $value['marketprice'] . " " . $value['total'] . " " . $value['price'] . "<BR>";
                        $num++;
                    }
                    $orderinfo .= "================================<BR>序号 商品名称 单价 数量  金额<BR>{$goods}<BR>================================<BR>";
                }
                if ($offers['goodsprice'] == 1) {
                    $orderinfo .= "商品合计：           {$print_order['goodsprice']}<BR>";
                }
                if ($offers['discountprice'] == 1 && ($print_order['discountprice'] > 0)) {
                    $orderinfo .= "会员折扣：           {$print_order['discountprice']}<BR>";
                }
                if ($offers['deductcredit2'] == 1 && ($print_order['deductcredit2'] > 0)) {
                    $orderinfo .= "余额抵扣：           {$print_order['deductcredit2']}<BR>";
                }
                if ($offers['deductenough'] == 1 && ($print_order['deductenough'] > 0)) {
                    $orderinfo .= "满额优惠：           {$print_order['deductenough']}<BR>";
                }
                if ($offers['deductprice'] == 1 && ($print_order['deductprice'] > 0)) {
                    $orderinfo .= "积分抵扣：           {$print_order['deductprice']}<BR>";
                }
                if ($offers['couponprice'] == 1 && ($print_order['couponprice'] > 0)) {
                    $orderinfo .= "优惠项目：           {$print_order['couponprice']}<BR>";
                }
                if ($offers['dispatchprice'] == 1 && ($print_order['dispatchprice'] > 0)) {
                    $orderinfo .= "订单运费：           {$print_order['dispatchprice']}<BR>";
                }
            }
            $orderinfo .= "实际支付：           {$print_order['price']}<BR>";
            if (!empty($offers)) {
                if ($offers['url'] == 1) {
                    $orderinfo .= "================================<BR><QR>{$url}</QR>";
                }
            }
            $content = array(
                'sn'=>$printer_sn,  
                'printContent'=>$orderinfo,
                //'apitype'=>'php',//如果打印出来的订单中文乱码，请把注释打开
                'key'=>$key,
                'times'=>$times//打印次数
            );
            
            if(!$this->client->post(HOSTNAME.'/printOrderAction',$content)){
                echo 'error';
            }
            else{
                //echo "<pre>";print_r($this->client->getContent());exit;
                $this->client->getContent();
            }
        }

        public function executePrint($orderid) {
            global $_W;
            if (empty($orderid)) {
                return;
            }
            $set = $this->getSet();
            $offers = $set['offers'];
            $shopset = m('common')->getSysset('shop');
            $order = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND id=:id", array(
                    ':uniacid'  => $_W['uniacid'],
                    ':id'       => $orderid
                ));
            $order['shopname'] = $shopset['name'];
            $order['goods'] = pdo_fetchall("SELECT og.goodsid,og.price,og.total,g.title,g.marketprice FROM " . tablename('sz_yi_order_goods') . " og LEFT JOIN " . tablename('sz_yi_goods') . " g ON g.id=og.goodsid WHERE og.uniacid=:uniacid AND og.orderid=:orderid", array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )); 
            foreach ($order['goods'] as &$value) {
                $value['totalmoney'] = number_format($value['price']*$value['total'],2);
            }
            unset($value);
            $openprint = pdo_fetch("SELECT * FROM " . tablename('sz_yi_yunprint_list') . " WHERE uniacid=:uniacid AND status=:status LIMIT 1 ", array(
                    ':uniacid'  => $_W['uniacid'],
                    ':status'   => 1
                ));
            // mode = 1 飞蛾   mode = 2 飞印
            if ($openprint['mode'] == 1) {
                $this->feie_print($order, $openprint['print_no'], $openprint['key'], $openprint['print_nums'], $openprint['qrcode_link'],$offers);
            }
            if ($openprint['mode'] == 2) {
                $this->feiyin_print($order, $openprint['member_code'], $openprint['print_no'], $openprint['key'], $offers);
            }
        }

        public function getSet()
        {
            $set = parent::getSet();
            return $set;
        }
	}
}
