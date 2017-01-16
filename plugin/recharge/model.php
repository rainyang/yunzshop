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
!defined('REACHARGE_API_URL') && define('REACHARGE_API_URL', "https://www.tieba8.com/api/web/v1/site/");
!defined('MOBILE_API_URL') && define('MOBILE_API_URL', "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm");//手机号码验证接口
!defined('API_SUBMIT') && define('API_SUBMIT', REACHARGE_API_URL . "submit");//订单提交接口
!defined('API_BLANCE') && define('API_BLANCE', REACHARGE_API_URL . "blance");//账户余额查询接口
!defined('API_QUERY') && define('API_QUERY', REACHARGE_API_URL . "query");//订单查询接口
!defined('API_BACK') && define('API_BACK', REACHARGE_API_URL . "back");//回调地址接口
if (!class_exists('RechargeModel')) {
	class RechargeModel extends PluginModel
	{
		/**
		 * 获取流量话费充值基础设置
		 * 
		 * @return array $set
		 **/
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		public function mobile_blance_api($data)
		{
			global $_W, $_GPC;
			load()->func('communication');
			$sign  = $this->getSign($data['apikey'], $data['username']);
		    $param = array(
				'apikey'  =>  $data['apikey'],
				'sign'    =>  $sign
			);
			$resp = ihttp_post(API_BLANCE,$param);
			$ret = @json_decode($resp['content'], true);
		    if (!empty($ret['blance']) && $ret['blance'] < $data['price']) {
		        show_json(0,'接口账户余额不足！无法充值！请联系工作人员！');
		    }
		}
		function mobile_submit_api($data)
		{
			global $_W, $_GPC;
			load()->func('communication');
			$sign  = $this->getSign($data['apikey'], $data['account']);
		    $param  =   array(
		        'apikey'       => $data['apikey'],
		        'sign'         => $sign,
		        'phone_no'     => $data['phone_no'],
		        'flow_val'     => $data['flow_val'],
		        'out_order_id' => $data['out_order_id'],
		        'timetamp'     => $data['timetamp'],
		        'scope'		   => $data['scope'],
		    );
			$resp = ihttp_post(API_SUBMIT, $param);
			$ret = @json_decode($resp['content'], true);
			$ret['createtime'] = date('Y-m-d H:i:s', time());
		    if (empty($ret)) {
				$ret['desc'] = '没收到充值接口返回信息...';
		    }
			$this->rechargeLog('api_submit_return', print_r($ret,true));
		    if (!empty($ret['out_order_id']) && $ret['result'] == "0") {
		        $message = array(
		            'keyword1' => array('value' => '手机流量提交成功', 'color' => '#73a68d'),
		            'keyword2' => array('value' => '[订单编号]' . $data['out_order_id'], 'color' => '#73a68d'),
		            'keyword3' => array('value' => '[手机号码]' . $data['phone_no'], 'color' => '#73a68d'),
		            'keyword4' => array('value' => '[充值流量]' . $data['flow_val'] . 'M', 'color' => '#73a68d'),
		            'remark' => array('value' => '您购买的流量已经提交成功.请留意订单的状态.如果24小时未发货.联系售后处理.
		            如果已经发货.请短信查询流量到账情况.部分流量需要在网厅查询.关注订阅号《优惠一线》优惠早知道.')
		            );
		        m('message')->sendCustomNotice($data['openid'], $message);
		        return true;
		    } else if (empty($ret['out_order_id']) && $ret['result'] != "0") {
				$message = array(
		            'keyword1' => array('value' => '手机流量提交失败', 'color' => '#73a68d'),
		            'keyword2' => array('value' => '[订单编号]' . $data['out_order_id'], 'color' => '#73a68d'),
		            'keyword3' => array('value' => '[手机号码]' . $data['phone_no'], 'color' => '#73a68d'),
		            'keyword4' => array('value' => '[充值流量]' . $data['flow_val'] . 'M', 'color' => '#73a68d'),
		            'keyword5' => array('value' => '[失败原因]' . $ret['err_desc'], 'color' => '#73a68d'),
		            'remark' => array('value' => '您购买的手机流量充值提交失败，如未自动退款到您的微信账户，请联系管理员！')
		            );
				$remark_data = array(
					'uniacid' => $_W['uniacid'],
					'orderid' => $data['order_id'],
					'remark' =>  "流量提交失败,失败原因: " . $ret['err_desc'],
					'createtime' => time()
				);
				pdo_insert('sz_yi_recharge_remark', $remark_data);
		        m('message')->sendCustomNotice($data['openid'], $message);
				$data['err_desc'] = $ret['err_desc'];
				$refunddata = array(
					'orderid' => $data['order_id'],
					'price' => $data['price'],
					'content' => $data['err_desc'],
					'openid' => $data['openid'],
					'pay_ordersn' => $data['pay_ordersn'],
					'ordersn' => $data['out_order_id']
				);
				$this->autoRefund($refunddata);

		    }
		}
		function getSign($apikey, $account)
		{
			global $_W, $_GPC;
			$sign = MD5($apikey . $account);
			return $sign;
		}
		function autoRefund($data)
		{
			global $_W, $_GPC;
			$refundno = m("common")->createNO("order_refund", "refundno", "SR");
			$order_refund = array(
				"uniacid" => $_W['uniacid'],
				"orderid" => $data['orderid'],
				"refundno" => $refundno,
				"price" => $data['price'],
				"reason" => "自动退款",
				"content" => $data['content'],
				"createtime" => time(),
				"refundtime" => time(),
				"status" => 1,
				"refundtype" => 1,
			);
			pdo_insert('sz_yi_order_refund', $order_refund);
			$returnid = pdo_insertid();
			if ($returnid) {
				pdo_update('sz_yi_order', array(
					'status' => -1,
					'refundtime' => time(),
				), array(
					'id' => $data['orderid']
				));
				$refundprice = $data['price'] * 100;
				$isrefund = m("finance")->refund($data['openid'], $data['pay_ordersn'], $refundno, $refundprice,
					$refundprice);
				if ($isrefund) {
					$log = "订单" . $data['ordersn'] . "____" . print_r($isrefund)  . "____提交失败退款成功...";
					$this->rechargeLog('fail_autorefund',print_r($log,true));
					$auto_refund_mess = array(
						'keyword1'  => array('value' => '流量充值失败自动退款成功', 'color' => '#73a68d'),
						'keyword2'  => array('value' => '[订单编号]' . $data['ordersn'], 'color' => '#73a68d'),
						'keyword3'  => array('value' => '[退单编号]' . $refundno, 'color' => '#73a68d'),
						'keyword4'  => array('value' => '[退款金额]' . $data['price'], 'color' => '#73a68d'),
						'keyword5'  => array('value' => '[退款方式]微信钱包', 'color' => '#73a68d'),
						'remark'    => array('value' => '您的流量充值失败，已经自动给您退款成功，退款到您的微信钱包，
						请根据订单编号查看确认退款金额是否正确！')
					);
					m('message')->sendCustomNotice($data['openid'], $auto_refund_mess);
					return true;

				}
			}
		}
		public function rechargeLog($filename = '',$log = '')
		{
			global $_W;
			$path = IA_ROOT . "/addons/sz_yi/data/rechargelog/" . $_W['uniacid'] . "/" . date('Ymd');
			if (!is_dir($path)) {
				load()->func('file');
				@mkdirs($path, '0777');
			}
			if (!empty($filename)) {
				$file = $path . "/" . $filename . '.log';
			} else {
				$file = $path . "/" . date('H') . '.log';
			}
			file_put_contents($file, $log, FILE_APPEND);
		}
		public function mobileApi($mobile)
		{
			global $_W, $_GPC;
			$url = MOBILE_API_URL . "?tel=" . $mobile . "&t=" . time();
			$num = 0;
			do {
				$content = file_get_contents($url);
				$html = iconv("gb2312", "utf-8//IGNORE", $content);
				$data = explode(',', $html);
				$province = explode(':', $data[1]);
				$catname = explode(':', $data[2]);
				$carrier = explode(':', $data[6]);
				$array = array(
					trim($province[0]) => str_replace("'", "", $province[1]),
					trim($catname[0]) => str_replace("'", "", $catname[1]),
					trim($carrier[0]) => trim(substr(str_replace("'", "", $carrier[1]), 0, -3))
				);
				$num++;
			} while (empty($array['catName']) && $num < 3);
			return  $array;
		}
		public function getOperatorGoods($operator)
		{
			global $_W, $_GPC;
			$goods = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods') .
				" WHERE operator = :operator AND uniacid = :uniacid AND status = :status ",
				array(
					':operator' => $operator,
					':uniacid' => $_W['uniacid'],
					':status' => 1
				)
			);
			return $goods;
		}
		public function getAllOptionsByMobile($goods, $catname, $province)
		{
			global $_W, $_GPC;
			foreach ($goods as $key => $good)
			{
				$spec_operator_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
                    left join " . tablename('sz_yi_goods_spec_item') . " gsi on gs.id = gsi.specid 
                    where gs.goodsid = :goodsid and gsi.title = :title and gs.uniacid = :uniacid 
                    order by gs.displayorder asc",
					array(
						':goodsid' => $good['id'],
						':title' => $catname,
						':uniacid' => $_W['uniacid']
					)
				);//获取用户填写手机号码的商品规格运营商id
				$spec_ids = pdo_fetchall("select gsi.id,gsi.title from " . tablename('sz_yi_goods_spec') . " gs 
                    left join " . tablename('sz_yi_goods_spec_item') . " gsi on gs.id = gsi.specid 
                    where gs.goodsid = :id and gs.uniacid = :uniacid
                    order by gs.displayorder asc",
					array(
						':id' => $good['id'],
						':uniacid' => $_W['uniacid']
					)
				);//获取商品所有规格
				$spec_data = array();
				foreach ($spec_ids as $k => $data_id)
				{
					$spec_data[] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_option') . " 
                        WHERE specs =  :specs 
                        and stock > 0 and uniacid = :uniacid",
						array(
							':specs' => $spec_operator_id . "_" . $data_id['id'],
							':uniacid' => $_W['uniacid']
						)
					);
				}
				foreach ($spec_data as $k => $value)
				{
					//省份省内
					if ($good['isprovince'] == 1 && strpos($good['province'], $province) !== false && !empty($value)) {
						$spec_datas['provincial'][$k]['itemid'] = $spec_ids[$k]['id'];
						$spec_datas['provincial'][$k]['itemtitle'] = $spec_ids[$k]['title'];
						$spec_datas['provincial'][$k]['optionid'] = $value['id'];
						$spec_datas['provincial'][$k]['price'] = $value['marketprice'];
						$spec_datas['provincial'][$k]['goodsid'] = $good['id'];
					}
					//省份国内
					if (empty($good['isprovince']) && strpos($good['province'], $province) !== false && !empty($value)) {
						$spec_datas['domestic'][$k]['itemid'] = $spec_ids[$k]['id'];
						$spec_datas['domestic'][$k]['itemtitle'] = $spec_ids[$k]['title'];
						$spec_datas['domestic'][$k]['optionid'] = $value['id'];
						$spec_datas['domestic'][$k]['price'] = $value['marketprice'];
						$spec_datas['domestic'][$k]['goodsid'] = $good['id'];
					}
					//全国国内
					if (empty($good['isprovince']) && empty($good['province']) && !empty($value)) {
						$spec_datas['alldomestic'][$k]['itemid'] = $spec_ids[$k]['id'];
						$spec_datas['alldomestic'][$k]['itemtitle'] = $spec_ids[$k]['title'];
						$spec_datas['alldomestic'][$k]['optionid'] = $value['id'];
						$spec_datas['alldomestic'][$k]['price'] = $value['marketprice'];
						$spec_datas['alldomestic'][$k]['goodsid'] = $good['id'];
					}
				}
			}
			return $spec_datas;
		}
		public function getOrderByOrdersn($ordersn)
		{
			global $_W, $_GPC;
			$order = pdo_fetch("SELECT id, openid, redprice, uniacid, status, price, pay_ordersn, ordersn FROM " .
				tablename("sz_yi_order") .
				"WHERE ordersn = :ordersn ",
				array(
					':ordersn' => $ordersn
				));
			return $order;
		}
		public function getAllAdv()
		{
			global $_W, $_GPC;
			$adv = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_recharge_adv') .
				" WHERE uniacid = :uniacid 
				ORDER BY displayorder DESC ",
				array(
					':uniacid' => $_W['uniacid']
				)
			);
			return $adv;
		}
		public function getShowAdv()
		{
			global $_W, $_GPC;
			$advs = pdo_fetchall('SELECT id,advname,link,thumb FROM ' . tablename('sz_yi_recharge_adv') .
				' WHERE uniacid=:uniacid AND isshow = :isshow AND LENGTH(thumb) > 0 
				ORDER BY displayorder DESC',
				array(
					':uniacid' => $_W['uniacid'],
					':isshow' => 1
				)
			);
			$advs = set_medias($advs, 'thumb');
			return $advs;
		}
		public function getAdvById($id)
		{
			global $_W, $_GPC;
			$adv = pdo_fetch("SELECT * FROM " . tablename('sz_yi_recharge_adv') .
				" WHERE id = :id AND uniacid = :uniacid limit 1",
				array(
					":id" => $id,
					":uniacid" => $_W['uniacid']
				)
			);
			return $adv;
		}
	}
}
