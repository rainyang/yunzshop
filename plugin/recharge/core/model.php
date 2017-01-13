<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
/**
 * * recharge插件方法类
 * *
 * *
 * * @package   流量话费充值插件公共方法
 * * @author    LuckyStar_D<duanfuxing@yunzshop.com>
 * * @version   v1.0
 * */


if (!class_exists('RechargeModel')) {
	class RechargeModel extends PluginModel
	{
		/**
 * 		 * 获取流量话费充值基础设置
 * 		 		 *
 * 		 		 		 * @return array $set
 * 		 		 		 		 */
		public function getSet()
		{

			$set = parent::getSet();
			return $set;
		}
		public function mobile_blance_api($data){
			$sign   =   MD5($data['apikey'].$data['username']);
		    $param  =   array(
		                    'apikey'  =>  $data['apikey'],
		                    'sign'    =>  $sign
		                    );
		    //$blanceurl = 'http://www.tieba8.com/api/web/v1/site/blance'; //账户余额查询接口
		    $ch = curl_init();  
		    curl_setopt($ch, CURLOPT_POST, 1);  
		    curl_setopt($ch, CURLOPT_URL, $blanceurl);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);   
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $return = curl_exec($ch);  
		    $status = json_decode($return,true);
		    if (!empty($status['blance']) && $status['blance'] < $data['price']) {
		        show_json(0,'接口账户余额不足！无法充值！请联系工作人员！');
		    }
		}
		function mobile_submit_api($data){
			//print_r($data);exit;
		    global $_W, $_GPC;
		    file_put_contents(IA_ROOT."/submit_data.txt", print_r($data,true),FILE_APPEND);
		/*  $queryurl = 'http://www.tieba8.com/api/web/v1/site/query'; //订单查询接口
		    $submiturl = 'http://www.tieba8.com/api/web/v1/site/submit'; //订单提交接口
		    $blanceurl = 'http://www.tieba8.com/api/web/v1/site/blance'; //账户余额查询接口
		    $backurl = 'http://www.tieba8.com/api/web/v1/site/back';  //回调地址接口
		    $sign = MD5(apikey+username); //校验密码为 接口账户的apikey加平台账户(username) ;
		*/    
		    $openid    = m('user')->getOpenid();

		    $submiturl = 'http://www.tieba8.com/api/web/v1/site/submit'; //订单提交接口
		    $sign   =   MD5($data['apikey'].$data['account']);
		    $param  =   array(
		        'apikey'        =>  $data['apikey'],
		        'sign'          =>  $sign,
		        'phone_no'      =>  $data['phone_no'],
		        'flow_val'      =>  $data['flow_val'],
		        'out_order_id'  =>  $data['out_order_id'],
		        'timetamp'      =>  $data['timetamp'],
		        'scope'			=>	$data['scope'],
		    );
		    file_put_contents(IA_ROOT."/submit_param.txt", print_r($param,true),FILE_APPEND);
		    $ch = curl_init();  
		    curl_setopt($ch, CURLOPT_POST, 1);  
		    curl_setopt($ch, CURLOPT_URL, $submiturl);  
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);  
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		    $return  = curl_exec($ch);  
		    $status = json_decode($return,true);
		    if(!empty($status)){
		        $status['createtime'] = date('Y-m-d H:i:s',time());
		        file_put_contents(IA_ROOT."/submit_return.txt", print_r($status,true),FILE_APPEND);
		    }else{
		        $status['createtime'] = date('Y-m-d H:i:s',time());
		        $status['desc'] = '没收到充值接口返回信息...';
		        file_put_contents(IA_ROOT."/submit_return.txt", print_r($status,true),FILE_APPEND);
		    }

		    if (!empty($status['out_order_id']) && $status['result'] == "0") {
		        $_var_156 = array(
		            'keyword1' => array('value' => '手机流量提交成功', 'color' => '#73a68d'),
		            'keyword2' => array('value' => '[订单编号]'.$data['out_order_id'], 'color' => '#73a68d'),
		            'keyword3' => array('value' => '[手机号码]'.$data['phone_no'],'color' => '#73a68d'),
		            'keyword4' => array('value' => '[充值流量]'.$data['flow_val'].'M','color' => '#73a68d'),
		            'remark' => array('value' => '您购买的流量已经提交成功.请留意订单的状态.如果24小时未发货.联系售后处理.如果已经发货.请短信查询流量到账情况.部分流量需要在网厅查询.关注订阅号《优惠一线》优惠早知道.')
		            );
		        m('message')->sendCustomNotice($openid, $_var_156);
		    }else if (empty($status['out_order_id']) && $status['result'] != "0") {
		        $_var_156 = array(
		            'keyword1' => array('value' => '手机流量提交失败', 'color' => '#73a68d'),
		            'keyword2' => array('value' => '[订单编号]'.$data['out_order_id'], 'color' => '#73a68d'),
		            'keyword3' => array('value' => '[手机号码]'.$data['phone_no'],'color' => '#73a68d'),
		            'keyword4' => array('value' => '[充值流量]'.$data['flow_val'].'M','color' => '#73a68d'),
		            'keyword5' => array('value' => '[失败原因]'.$status['err_desc'],'color' => '#73a68d'),
		            'remark' => array('value' => '您购买的手机流量充值提交失败，如未自动退款到您的微信账户，请联系管理员！')
		            );
		        pdo_update('sz_yi_order', array(
		            'remark' => "流量提交失败,失败原因:".$status['err_desc']
		            ), array(
		                'ordersn' => $data['out_order_id']
		        ));
		        m('message')->sendCustomNotice($openid, $_var_156);
	            $refundno= m("common")->createNO("order_refund", "refundno", "SR");
	            $order_refund = array(
	                "uniacid" => $_W['uniacid'],
	                "orderid" => $data['order_id'],
	                "refundno" => $refundno,
	                "price" => $data['price'],
	                "reason" => "自动退款",
	                "content" => $status['err_desc'], 
	                "createtime" => time(),
	                "refundtime" => time(),
	                "status" => 1,
	                "refundtype" => 1,
	                );
	            pdo_insert('sz_yi_order_refund',$order_refund);
	            
	            $returnid = pdo_insertid();
	            if($returnid){
	                file_put_contents(IA_ROOT."/data_submit_refund_log.txt", print_r($order_refund,true),FILE_APPEND);
	                pdo_update('sz_yi_order', array(
	                    'status' => -1,
	                    'refundtime' => time(),
	                    ), array(
	                        'id' => $data['order_id']
	                ));
	                $refundprice = $data['price'] * 100;
	                $isrefund= m("finance")->refund($openid, $data['pay_ordersn'], $refundno, $refundprice, $refundprice);
	                if($isrefund){
	                    file_put_contents(IA_ROOT."/data_submit_refund_price_log.txt", "订单".$data['out_order_id']."___".print_r($isrefund)."提交失败退款成功...",FILE_APPEND);
	                    $auto_refund_mess = array(
	                    'keyword1'  => array('value' => '流量充值失败自动退款成功', 'color' => '#73a68d'),
	                    'keyword2'  => array('value' => '[订单编号]'.$data['out_order_id'], 'color' => '#73a68d'),
	                    'keyword3'  => array('value' => '[退单编号]'.$refundno,'color' => '#73a68d'),
	                    'keyword4'  => array('value' => '[退款金额]'.$data['price'],'color' => '#73a68d'),
	                    'keyword5'  => array('value' => '[退款方式]微信钱包','color' => '#73a68d'),
	                    'remark'    => array('value' => '您的流量充值失败，已经自动给您退款成功，退款到您的微信钱包，请根据订单编号查看确认退款金额是否正确！')
	                    );
	                    m('message')->sendCustomNotice($openid, $auto_refund_mess);
	                }
	            }                 
		    }
		}
	}
}
