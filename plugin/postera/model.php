<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('PosteraModel')) {
	class PosteraModel extends PluginModel
	{
		public function getSceneTicket($seconds, $qrcid)
		{
			global $_W, $_GPC;
			$account = m('common')->getAccount();
			$json = '{"expire_seconds":' . $seconds . ',"action_info":{"scene":{"scene_id":' . $qrcid . '}},"action_name":"QR_SCENE"}';
			$access_token = $account->fetch_token();
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
			$info = curl_exec($curl);
			$result = @json_decode($info, true);
			if (!is_array($result)) {
				return false;
			}
			if (!empty($result['errcode'])) {
				return error(-1, $result['errmsg']);
			}
			$ticket = $result['ticket'];
			return array('barcode' => json_decode($json, true), 'ticket' => $ticket);
		}

		function getSceneID()
		{
			global $_W;
			$acid = $_W['acid'];
			$min = 1;
			$max = 2147483647;
			$qrcid = rand($min, $max);
			if (empty($qrcid)) {
				$qrcid = rand($min, $max);
			}
			while (1) {
				$count = pdo_fetchcolumn('select count(*) from ' . tablename('qrcode') . ' where qrcid=:qrcid and acid=:acid and model=0 limit 1', array(':qrcid' => $qrcid, ':acid' => $acid));
				if ($count <= 0) {
					break;
				}
				$qrcid = rand($min, $max);
				if (empty($qrcid)) {
					$qrcid = rand($min, $max);
				}
			}
			return $qrcid;
		}

		public function getQR($poster, $member)
		{
			global $_W, $_GPC;
			$acid = $_W['acid'];
			$endtime = time();
			$timeend = $poster['timeend'];
			$seconds = $timeend - $endtime;
			if ($seconds > 86400 * 30 - 15) {
				$seconds = 86400 * 30 - 15;
			}
			$endtime = $endtime + $seconds;
			$qr = pdo_fetch('select * from ' . tablename('sz_yi_postera_qr') . ' where openid=:openid and acid=:acid and posterid=:posterid limit 1', array(':openid' => $member['openid'], ':acid' => $acid, ':posterid' => $poster['id']));
			if (empty($qr)) {
				$qr['current_qrimg'] = '';
				$qrcid = $this->getSceneID();
				$result = $this->getSceneTicket($seconds, $qrcid);
				if (is_error($result)) {
					return $result;
				}
				if (empty($result)) {
					return error(-1, '生成二维码失败');
				}
				$barcode = $result['barcode'];
				$ticket = $result['ticket'];
				$qrimg = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
				$qr = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qrcid' => $qrcid, 'model' => 0, 'name' => 'SZ_YI_POSTERA_QRCODE', 'keyword' => 'SZ_YI_POSTERA', 'expire' => $seconds, 'createtime' => time(), 'status' => 1, 'url' => $result['url'], 'ticket' => $result['ticket']);
				pdo_insert('qrcode', $qr);
				$qr = array('acid' => $acid, 'openid' => $member['openid'], 'sceneid' => $qrcid, 'type' => $poster['type'], 'ticket' => $result['ticket'], 'qrimg' => $qrimg, 'posterid' => $poster['id'], 'expire' => $seconds, 'url' => $result['url'], 'goodsid' => $poster['goodsid'], 'endtime' => $endtime);
				pdo_insert('sz_yi_postera_qr', $qr);
				$qr['id'] = pdo_insertid();
			} else {
				$qr['current_qrimg'] = $qr['qrimg'];
				if (time() > $qr['endtime']) {
					$qrcid = $qr['sceneid'];
					$result = $this->getSceneTicket($seconds, $qrcid);
					if (is_error($result)) {
						return $result;
					}
					if (empty($result)) {
						return error(-1, '生成二维码失败');
					}
					$barcode = $result['barcode'];
					$ticket = $result['ticket'];
					$qrimg = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
					pdo_update('qrcode', array('ticket' => $result['ticket'], 'url' => $result['url']), array('acid' => $_W['acid'], 'qrcid' => $qrcid));
					pdo_update('sz_yi_postera_qr', array('ticket' => $ticket, 'qrimg' => $qrimg, 'url' => $result['url'], 'endtime' => $endtime), array('id' => $qr['id']));
					$qr['ticket'] = $ticket;
					$qr['qrimg'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $qr['ticket'];
				}
			}
			return $qr;
		}

		public function getRealData($data)
		{
			$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
			$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
			$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
			$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
			$data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
			$data['src'] = tomedia($data['src']);
			return $data;
		}

		public function createImage($imgurl)
		{
			load()->func('communication');
			$resp = ihttp_request($imgurl);
			return imagecreatefromstring($resp['content']);
		}

		public function mergeImage($target, $data, $imgurl)
		{
			$img = $this->createImage($imgurl);
			$w = imagesx($img);
			$h = imagesy($img);
			imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
			imagedestroy($img);
			return $target;
		}

		public function mergeText($target, $data, $text)
		{
			$font = IA_ROOT . '/addons/sz_yi/static/fonts/msyh.ttf';
			$colors = $this->hex2rgb($data['color']);
			$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
			imagettftext($target, $data['size'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
			return $target;
		}

		function hex2rgb($colour)
		{
			if ($colour[0] == '#') {
				$colour = substr($colour, 1);
			}
			if (strlen($colour) == 6) {
				list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
			} elseif (strlen($colour) == 3) {
				list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
			} else {
				return false;
			}
			$r = hexdec($r);
			$g = hexdec($g);
			$b = hexdec($b);
			return array('red' => $r, 'green' => $g, 'blue' => $b);
		}

		public function createPoster($poster, $member, $qr, $upload = true)
		{
			global $_W;
			$path = IA_ROOT . '/addons/sz_yi/data/postera/' . $_W['uniacid'] . '/';
			if (!is_dir($path)) {
				load()->func('file');
				mkdirs($path);
			}
			if (!empty($qr['goodsid'])) {
				$goods = pdo_fetch('select id,title,thumb,commission_thumb,marketprice,productprice from ' . tablename('sz_yi_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $qr['goodsid'], ':uniacid' => $_W['uniacid']));
				if (empty($goods)) {
					m('message')->sendCustomNotice($member['openid'], '未找到商品，无法生成海报');
					exit;
				}
			}
			$md5 = md5(json_encode(array('openid' => $member['openid'], 'goodsid' => $qr['goodsid'], 'bg' => $poster['bg'], 'data' => $poster['data'], 'version' => 1)));
			$file = $md5 . '.png';
			if (!is_file($path . $file) || $qr['qrimg'] != $qr['current_qrimg']) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$target = imagecreatetruecolor(640, 1008);
				$bg = $this->createImage(tomedia($poster['bg']));
				imagecopy($target, $bg, 0, 0, 0, 0, 640, 1008);
				imagedestroy($bg);
				$data = json_decode(str_replace('&quot;', '\'', $poster['data']), true);
				foreach ($data as $d) {
					$d = $this->getRealData($d);
					if ($d['type'] == 'head') {
						$avatar = preg_replace('/\\/0$/i', '/96', $member['avatar']);
						$target = $this->mergeImage($target, $d, $avatar);
					} else if ($d['type'] == 'time') {
						$endtime = date('Y-m-d H:i', $qr['endtime']);
						$target = $this->mergeText($target, $d, $endtime);
					} else if ($d['type'] == 'img') {
						$target = $this->mergeImage($target, $d, $d['src']);
					} else if ($d['type'] == 'qr') {
						$target = $this->mergeImage($target, $d, tomedia($qr['qrimg']));
					} else if ($d['type'] == 'nickname') {
						$target = $this->mergeText($target, $d, $member['nickname']);
					} else {
						if (!empty($goods)) {
							if ($d['type'] == 'title') {
								$target = $this->mergeText($target, $d, $goods['title']);
							} else if ($d['type'] == 'thumb') {
								$thumb = !empty($goods['commission_thumb']) ? tomedia($goods['commission_thumb']) : tomedia($goods['thumb']);
								$target = $this->mergeImage($target, $d, $thumb);
							} else if ($d['type'] == 'marketprice') {
								$target = $this->mergeText($target, $d, $goods['marketprice']);
							} else if ($d['type'] == 'productprice') {
								$target = $this->mergeText($target, $d, $goods['productprice']);
							}
						}
					}
				}
				imagepng($target, $path . $file);
				imagedestroy($target);
			}
			$img = $_W['siteroot'] . 'addons/sz_yi/data/poster/' . $_W['uniacid'] . '/' . $file;
			if (!$upload) {
				return $img;
			}
			if ($qr['qrimg'] != $qr['current_qrimg'] || empty($qr['mediaid']) || empty($qr['createtime']) || $qr['createtime'] + 3600 * 24 * 3 - 7200 < time()) {
				$mediaid = $this->uploadImage($path . $file);
				$qr['mediaid'] = $mediaid;
				pdo_update('sz_yi_postera_qr', array('mediaid' => $mediaid, 'createtime' => time()), array('id' => $qr['id']));
			}
			return array('img' => $img, 'mediaid' => $qr['mediaid']);
		}

		public function uploadImage($img)
		{
			load()->func('communication');
			$account = m('common')->getAccount();
			$access_token = $account->fetch_token();
			$resp = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type=image";
			$curl = curl_init();
			$post = array('media' => '@' . $img);
			if (version_compare(PHP_VERSION, '5.5.0', '>')) {
				$post = array('media' => curl_file_create($img));
			}
			curl_setopt($curl, CURLOPT_URL, $resp);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			$content = @json_decode(curl_exec($curl), true);
			if (!is_array($content)) {
				$content = array('media_id' => '');
			}
			curl_close($curl);
			return $content['media_id'];
		}

		public function getQRByTicket($ticket = '')
		{
			global $_W;
			if (empty($ticket)) {
				return false;
			}
			$qrs = pdo_fetchall('select * from ' . tablename('sz_yi_postera_qr') . ' where ticket=:ticket and acid=:acid limit 1', array(':ticket' => $ticket, ':acid' => $_W['acid']));
			$count = count($qrs);
			if ($count <= 0) {
				return false;
			}
			if ($count == 1) {
				return $qrs[0];
			}
			return false;
		}

		public function checkMember($openid = '')
		{
			global $_W;
			$acc = WeiXinAccount::create($_W['acid']);
			$userinfo = $acc->fansQueryInfo($openid);
			$userinfo['avatar'] = $userinfo['headimgurl'];
			load()->model('mc');
			$uid = mc_openid2uid($openid);
			if (!empty($uid)) {
				pdo_update('mc_members', array('nickname' => $userinfo['nickname'], 'gender' => $userinfo['sex'], 'nationality' => $userinfo['country'], 'resideprovince' => $userinfo['province'], 'residecity' => $userinfo['city'], 'avatar' => $userinfo['headimgurl']), array('uid' => $uid));
			}
			pdo_update('mc_mapping_fans', array('nickname' => $userinfo['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
			$model = m('member');
			$member = $model->getMember($openid);
			if (empty($member)) {
				$mc = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
				$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => $mc['realname'], 'mobile' => $mc['mobile'], 'nickname' => !empty($mc['nickname']) ? $mc['nickname'] : $userinfo['nickname'], 'avatar' => !empty($mc['avatar']) ? $mc['avatar'] : $userinfo['avatar'], 'gender' => !empty($mc['gender']) ? $mc['gender'] : $userinfo['sex'], 'province' => !empty($mc['resideprovince']) ? $mc['resideprovince'] : $userinfo['province'], 'city' => !empty($mc['residecity']) ? $mc['residecity'] : $userinfo['city'], 'area' => $mc['residedist'], 'createtime' => time(), 'status' => 0);
				pdo_insert('sz_yi_member', $member);
				$member['id'] = pdo_insertid();
				$member['isnew'] = true;
			} else {
				$member['nickname'] = $userinfo['nickname'];
				$member['avatar'] = $userinfo['headimgurl'];
				$member['province'] = $userinfo['province'];
				$member['city'] = $userinfo['city'];
				pdo_update('sz_yi_member', $member, array('id' => $member['id']));
				$member['isnew'] = false;
			}
			return $member;
		}

		function perms()
		{
			return array('postera' => array('text' => $this->getName(), 'isplugin' => true, 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'log' => '扫描记录', 'clear' => '清除缓存-log', 'setdefault' => '设置默认海报-log'));
		}
	}
}
