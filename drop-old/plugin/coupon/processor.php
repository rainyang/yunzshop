<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require IA_ROOT . '/addons/sz_yi/defines.php';
require SZ_YI_INC . 'plugin/plugin_processor.php';

class CouponProcessor extends PluginProcessor
{
	public function __construct()
	{
		parent::__construct('coupon');
	}

	public function respond($obj = null)
	{
		global $_W;
		$message = $obj->message;
		$content = $obj->message['content'];
		$msgtype = strtolower($message['msgtype']);
		$event = strtolower($message['event']);
		if ($msgtype == 'text' || $event == 'click') {
			return $this->respondText($obj);
		}
		return $this->responseEmpty();
	}

	private function responseEmpty()
	{
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}

	function replaceCoupon($coupon, $member, $times, $lasttimes)
	{
		$texts = array('pwdask' => '请输入优惠券口令: ', 'pwdfail' => '很抱歉，您猜错啦，继续猜~', 'pwdsuc' => '恭喜你，猜中啦！优惠券已发到您账户了! ', 'pwdfull' => '很抱歉，您已经没有机会啦~ ', 'pwdown' => '您已经参加过啦,等待下次活动吧~', 'pwdexit' => '0', 'pwdexitstr' => '好的，等待您下次来玩!');
		foreach ($texts as $key => $value) {
			if (empty($coupon[$key])) {
				$coupon[$key] = $value;
			} else {
				$coupon[$key] = str_replace('[nickname]', $member['nickname'], $coupon[$key]);
				$coupon[$key] = str_replace('[couponname]', $coupon['couponname'], $coupon[$key]);
				$coupon[$key] = str_replace('[times]', $times, $coupon[$key]);
				$coupon[$key] = str_replace('[lasttimes]', $lasttimes, $coupon[$key]);
			}
		}
		return $coupon;
	}

	function getGuess($coupon, $openid)
	{
		global $_W;
		$lasttimes = 1;
		$times = 0;
		$guess = pdo_fetch('select id,times from ' . tablename('sz_yi_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ', array(':couponid' => $coupon['id'], ':openid' => $openid, ':pwdkey' => $coupon['pwdkey'], ':uniacid' => $_W['uniacid']));
		if ($coupon['pwdtimes'] > 0) {
			$times = $guess['times'];
			$lasttimes = $coupon['pwdtimes'] - intval($times);
			if ($lasttimes <= 0) {
				$lasttimes = 0;
			}
		}
		return array('times' => $times, 'lasttimes' => $lasttimes);
	}

	function respondText($obj)
	{
		global $_W;
		@session_start();
		$content = $obj->message['content'];
		$openid = $obj->message['from'];
		$member = m('member')->getMember($openid);
		$coupon_key = $content;
		if (isset($_SESSION['sz_yi_coupon_key'])) {
			$coupon_key = $_SESSION['sz_yi_coupon_key'];
		} else {
			$_SESSION['sz_yi_coupon_key'] = $content;
		}
		$coupon = pdo_fetch('select id,couponname,pwdkey,pwdask,pwdsuc,pwdfail,pwdfull,pwdtimes,pwdurl,pwdwords,pwdown,pwdexit,pwdexitstr from ' . tablename('sz_yi_coupon') . ' where pwdkey=:pwdkey and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':pwdkey' => $coupon_key));
		$pwdwords = explode(',', $coupon['pwdwords']);
		if (empty($coupon)) {
			$obj->endContext();
			unset($_SESSION['sz_yi_coupon_key']);
			return $this->responseEmpty();
		}
		if (!$obj->inContext) {
			$coupon_guess = pdo_fetch('select id,times from ' . tablename('sz_yi_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and ok=1 and uniacid=:uniacid limit 1 ', array(':couponid' => $coupon['id'], ':openid' => $openid, ':pwdkey' => $coupon['pwdkey'], ':uniacid' => $_W['uniacid']));
			if (!empty($coupon_guess)) {
				$guess = $this->getGuess($coupon, $openid);
				$coupon = $this->replaceCoupon($coupon, $member, $guess['times'], $guess['lasttimes']);
				$obj->endContext();
				unset($_SESSION['sz_yi_coupon_key']);
				return $obj->respText($coupon['pwdown']);
			}
			$guess = $this->getGuess($coupon, $openid);
			$coupon = $this->replaceCoupon($coupon, $member, $guess['times'], $guess['lasttimes']);
			if ($guess['lasttimes'] <= 0) {
				$obj->endContext();
				unset($_SESSION['sz_yi_coupon_key']);
				return $obj->respText($coupon['pwdfull']);
			}
			$obj->beginContext();
			return $obj->respText($coupon['pwdask']);
		} else {
			if ($content == $coupon['pwdexit']) {
				unset($_SESSION['sz_yi_coupon_key']);
				$obj->endContext();
				$guess = $this->getGuess($coupon, $openid);
				$coupon = $this->replaceCoupon($coupon, $member, $guess['times'], $guess['lasttimes']);
				return $obj->respText($coupon['pwdexitstr']);
			}
			$guess = pdo_fetch('select id,times from ' . tablename('sz_yi_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ', array(':couponid' => $coupon['id'], ':openid' => $openid, ':pwdkey' => $coupon['pwdkey'], ':uniacid' => $_W['uniacid']));
			$coupon_ok = in_array($content, $pwdwords);
			if (empty($guess)) {
				$guess = array('uniacid' => $_W['uniacid'], 'couponid' => $coupon['id'], 'openid' => $openid, 'times' => 1, 'pwdkey' => $coupon['pwdkey'], 'ok' => $coupon_ok ? 1 : 0);
				pdo_insert('sz_yi_coupon_guess', $guess);
			} else {
				pdo_update('sz_yi_coupon_guess', array('times' => $guess['times'] + 1, 'ok' => $coupon_ok ? 1 : 0), array('id' => $guess['id']));
			}
			$time = time();
			if ($coupon_ok) {
				$log = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'logno' => m('common')->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $coupon['id'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 5);
				pdo_insert('sz_yi_coupon_log', $log);
				$data = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'couponid' => $coupon['id'], 'gettype' => 5, 'gettime' => $time);
				pdo_insert('sz_yi_coupon_data', $data);
				unset($_SESSION['sz_yi_coupon_key']);
				$obj->endContext();
				$set = $this->model->getSet();
				$send_data = $this->model->getCoupon($coupon['id']);
				$this->model->sendMessage($send_data, 1, $member, $set['templateid']);
				$guess = $this->getGuess($coupon, $openid);
				$coupon = $this->replaceCoupon($coupon, $member, $guess['times'], $guess['lasttimes']);
				return $obj->respText($coupon['pwdsuc']);
			} else {
				$guess = $this->getGuess($coupon, $openid);
				$coupon = $this->replaceCoupon($coupon, $member, $guess['times'], $guess['lasttimes']);
				if ($guess['lasttimes'] <= 0) {
					$obj->endContext();
					unset($_SESSION['sz_yi_coupon_key']);
					return $obj->respText($coupon['pwdfull']);
				}
				return $obj->respText($coupon['pwdfail']);
			}
		}
	}
}
