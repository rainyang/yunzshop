<?php
/*=============================================================================
#     FileName: member.php
#         Desc:
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:32:02
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Member
{
    public function getInfo($openid = '')
    {
        global $_W;
        $uid = intval($openid);
        if ($uid == 0) {
            $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));
        } else {
            $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where id=:id  and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $uid
            ));
        }
        if (!empty($info['uid'])) {
            load()->model('mc');
            $uid                = mc_openid2uid($info['openid']);
            $fans               = mc_fetch($uid, array(
                'credit1',
                'credit2',
                'birthyear',
                'birthmonth',
                'birthday',
                'gender',
                'avatar',
                'resideprovince',
                'residecity',
                'nickname'
            ));
            $info['credit1']    = $fans['credit1'];
            $info['credit2']    = $fans['credit2'];
            $info['birthyear']  = empty($info['birthyear']) ? $fans['birthyear'] : $info['birthyear'];
            $info['birthmonth'] = empty($info['birthmonth']) ? $fans['birthmonth'] : $info['birthmonth'];
            $info['birthday']   = empty($info['birthday']) ? $fans['birthday'] : $info['birthday'];
            $info['nickname']   = empty($info['nickname']) ? $fans['nickname'] : $info['nickname'];
            $info['gender']     = empty($info['gender']) ? $fans['gender'] : $info['gender'];
            $info['sex']        = $info['gender'];
            $info['avatar']     = empty($info['avatar']) ? $fans['avatar'] : $info['avatar'];
            $info['headimgurl'] = $info['avatar'];
            $info['province']   = empty($info['province']) ? $fans['resideprovince'] : $info['province'];
            $info['city']       = empty($info['city']) ? $fans['residecity'] : $info['city'];
        }
        if (!empty($info['birthyear']) && !empty($info['birthmonth']) && !empty($info['birthday'])) {
            $info['birthday'] = $info['birthyear'] . '-' . (strlen($info['birthmonth']) <= 1 ? '0' . $info['birthmonth'] : $info['birthmonth']) . '-' . (strlen($info['birthday']) <= 1 ? '0' . $info['birthday'] : $info['birthday']);
        }
        if (empty($info['birthday'])) {
            $info['birthday'] = '';
        }
        return $info;
    }
    public function getMember($openid = '')
    {
        global $_W;

        $uid = intval($openid);

        if (empty($uid)) {
            $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));

        } else {
            $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where id=:id and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $uid
            ));
        }

        if (!empty($info['isactivity'])) {
            $info['avatar'] = $info['isactivity'];
        }
		if (!empty($info)) {

			$openid = $info['openid'];
			if (empty($info['uid'])) {
				$followed = m('user')->followed($openid);
				if ($followed) {
					load()->model('mc');
					$uid = mc_openid2uid($openid);
					if (!empty($uid)) {
						$info['uid'] = $uid;
						$upgrade = array('uid' => $uid);
						if ($info['credit1'] > 0) {
							mc_credit_update($uid, 'credit1', $info['credit1']);
								$upgrade['credit1'] = 0;
						}
						if ($info['credit2'] > 0) {
							mc_credit_update($uid, 'credit2', $info['credit2']);
							$upgrade['credit2'] = 0;
						}
                        if ($info['credit20'] > 0) {
                            mc_credit_update($uid, 'credit20', $info['credit20']);
                            $upgrade['credit20'] = 0;
                        }
						if (!empty($upgrade)) {
							pdo_update('sz_yi_member', $upgrade, array('id' => $info['id']));
						}
					}
				}
			}
			$credits = $this->getCredits($openid);
			$info['credit1'] = $credits['credit1'];
			$info['credit2'] = $credits['credit2'];
            $info['credit20'] = $credits['credit20'];
		}
        return $info;
    }
    public function getMid()
    {
        global $_W;
        $openid = m('user')->getOpenid();
        $member = $this->getMember($openid);
        return $member['id'];
    }
    public function responseReferral($set,$referral,$member)
    {
        global $_W;


        $subpaycontent = $set['subpaycontent'];
        if (empty($subpaycontent)) {
            $subpaycontent = '您通过 [nickname]的推荐码注册账号的奖励';
        }
        $subpaycontent = str_replace("[nickname]", $referral['mobile'], $subpaycontent);

        $recpaycontent = $set['recpaycontent'];
        if (empty($recpaycontent)) {
            $recpaycontent = '推荐 [nickname] 使用推荐码注册账号的奖励';
        }
        $recpaycontent = str_replace("[nickname]", $member['mobile'], $recpaycontent);

        if ($set['subcredit'] > 0) {
            m('member')->setCredit($member['openid'], 'credit1', $set['subcredit'], array(
                0,
                '推荐码注册积分+' . $set['subcredit']
            ));
        }
        if ($set['submoney'] > 0) {
            $pay = $set['submoney'];
            if ($set['paytype'] == 1) {
                $pay *= 100;
            }
            m('finance')->pay($member['openid'], $set['paytype'], $pay, '', $subpaycontent);
        }
        if ($set['reccredit'] > 0) {
            m('member')->setCredit($referral['openid'], 'credit1', $set['reccredit'], array(
                0,
                '分享推荐码注册积分+' . $set['reccredit']
            ));
        }
        if ($set['recmoney'] > 0) {
            $pay = $set['recmoney'];
            if ($set['paytype'] == 1) {
                $pay *= 100;
            }
            m('finance')->pay($referral['openid'], $set['paytype'], $pay, '', $recpaycontent);
        }
            if (!empty($set['subtext'])) {
                $subtext = $set['subtext'];
                $subtext = str_replace("[nickname]", $member['mobile'], $subtext);
                $subtext = str_replace("[credit]", $set['reccredit'], $subtext);
                $subtext = str_replace("[money]", $set['recmoney'], $subtext);

                if (!empty($set['templateid'])) {
                    m('message')->sendTplNotice($referral['openid'], $set['templateid'], array(
                        'first' => array(
                            'value' => "推荐注册奖励到账通知",
                            "color" => "#4a5077"
                        ),
                        'keyword1' => array(
                            'value' => '推荐奖励',
                            "color" => "#4a5077"
                        ),
                        'keyword2' => array(
                            'value' => $subtext,
                            "color" => "#4a5077"
                        ),
                        'remark' => array(
                            'value' => "\r\n谢谢您对我们的支持！",
                            "color" => "#4a5077"
                        )
                    ), '');
                } else {
                    m('message')->sendCustomNotice($referral['openid'], $subtext);
                }
            }
            if (!empty($set['entrytext'])) {
                $entrytext = $set['entrytext'];
                $entrytext = str_replace("[nickname]", $member['mobile'], $entrytext);
                $entrytext = str_replace("[credit]", $set['subcredit'], $entrytext);
                $entrytext = str_replace("[money]", $set['submoney'], $entrytext);

                if (!empty($set['templateid'])) {
                    m('message')->sendTplNotice($member['openid'], $set['templateid'], array(
                        'first' => array(
                            'value' => "使用推荐码奖励到账通知",
                            "color" => "#4a5077"
                        ),
                        'keyword1' => array(
                            'value' => '使用推荐码奖励',
                            "color" => "#4a5077"
                        ),
                        'keyword2' => array(
                            'value' => $entrytext,
                            "color" => "#4a5077"
                        ),
                        'remark' => array(
                            'value' => "\r\n谢谢您对我们的支持！",
                            "color" => "#4a5077"
                        )
                    ), '');
                } else {
                    m('message')->sendCustomNotice($openid, $entrytext);
                }
            }
   // [isreferrer] => 1
   //  [reccredit] => 1
   //  [recmoney] => 3
   //  [subcredit] => 2
   //  [submoney] => 4
   //  [paytype] => 1
   //  [isreferral] => 1
   //  [templateid] =>  OPENTM200605630
   //  [subtext] => [nickname] 通过您的推荐码注册账号! 获得了 [credit] 个积分,[money]元奖励!
   //  [entrytext] => 您使用了 [nickname] 的推荐码注册账号! 获得了 [credit] 个积分,[money]元奖励!
   //  [subpaycontent] => 您通过 [nickname]的推荐码注册账号的奖励
   //  [recpaycontent] => 推荐 [nickname] 使用推荐码注册账号的奖励
   //  [referrallogo] => images/1/2016/05/jr0KuPXRPQ04XgxpXXP40p4CUp1QzU.png


    }
    public function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $log = array())
    {
        global $_W;
        load()->model('mc');
        $uid = mc_openid2uid($openid);

        if (!empty($uid)) {

            $value     = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                ':uid' => $uid
            ));

            $newcredit = $credits + $value;
            if ($newcredit <= 0) {
                $newcredit = 0;
            }

            pdo_update('mc_members', array(
                $credittype => $newcredit
            ), array(
                'uid' => $uid
            ));
            if (empty($log) || !is_array($log)) {
                $log = array(
                    $uid,
                    '未记录'
                );
            }
            $data = array(
                'uid' => $uid,
                'credittype' => $credittype,
                'uniacid' => $_W['uniacid'],
                'num' => $credits,
                'createtime' => TIMESTAMP,
                'operator' => intval($log[0]),
                'remark' => $log[1]
            );
            pdo_insert('mc_credits_record', $data);

        } else {

            $value     = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('sz_yi_member') . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));

            $newcredit = $credits + $value;
            if ($newcredit <= 0) {
                $newcredit = 0;
            }
            pdo_update('sz_yi_member', array(
                $credittype => $newcredit
            ), array(
                'uniacid' => $_W['uniacid'],
                'openid' => $openid
            ));
        }
    }
    public function getCredit($openid = '', $credittype = 'credit1')
    {
        global $_W;
        load()->model('mc');
        $uid = mc_openid2uid($openid);
        if (!empty($uid)) {
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                ':uid' => $uid
            ));
        } else {
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('sz_yi_member') . " WHERE  openid=:openid and uniacid=:uniacid limit 1", array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));
		}
	}
	public function getCredits($openid = '', $type = array('credit1', 'credit2', 'credit20'))
	{
		global $_W;
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		$credittype = implode(',', $type);
		if (!empty($uid)) {
			return pdo_fetch("SELECT {$credittype} FROM " . tablename('mc_members') . ' WHERE `uid` = :uid limit 1', array(':uid' => $uid));
		} else {
			return pdo_fetch("SELECT {$credittype} FROM " . tablename('sz_yi_member') . ' WHERE  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}

    }

    public function checkMember($openid = '')
    {
        global $_W, $_GPC;
        if (strexists($_SERVER['REQUEST_URI'], '/web/')) {
            return;
        }
        if (empty($openid)) {
            $openid = m('user')->getOpenid();
        }
        if (empty($openid)) {
            return;
        }
        $member   = m('member')->getMember($openid);
        $userinfo = m('user')->getInfo();
        $followed = m('user')->followed($openid);
        $uid      = 0;
        $mc       = array();
		load()->model('mc');
		if ($followed) {
			$uid = mc_openid2uid($openid);
			$mc = mc_fetch($uid, array('realname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
		}

        $bindMobile = false;

        $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid']
        ));
        $set     = unserialize($setdata['sets']);

        if (empty($member)) {
            if ($followed) {
                $uid = mc_openid2uid($openid);
                $mc  = mc_fetch($uid, array(
                    'realname',
                    'mobile',
                    'avatar',
                    'resideprovince',
                    'residecity',
                    'residedist'
                ));
            }
            $member = array(
                'uniacid' => $_W['uniacid'],
                'uid' => $uid,
                'openid' => $openid,
                'realname' => !empty($mc['realname']) ? $mc['realname'] : '',
                'mobile' => !empty($mc['mobile']) ? $mc['mobile'] : '',
                'nickname' => !empty($mc['nickname']) ? $mc['nickname'] : $userinfo['nickname'],
                'avatar' => !empty($mc['avatar']) ? $mc['avatar'] : $userinfo['avatar'],
                'gender' => !empty($mc['gender']) ? $mc['gender'] : $userinfo['sex'],
                'province' => !empty($mc['residecity']) ? $mc['resideprovince'] : $userinfo['province'],
                'city' => !empty($mc['residecity']) ? $mc['residecity'] : $userinfo['city'],
                'area' => !empty($mc['residedist']) ? $mc['residedist'] : '',
                'createtime' => time(),
                'status' => 0
            );
            $bindMobile = true;

            pdo_insert('sz_yi_member', $member);

            /**
             * 分销 绑定app注册用户
             */
            if ($set['app']['base']['share']['switch'] == 1 && isset($_GPC['access']) && $_GPC['access'] == 'app') {
                /**
                 * 分销商品链接地址
                 */
                header("Location:/app/index.php?i=" . $_W['uniacid'] . "&c=entry&p=bindapp&do=member&m=sz_yi&mid=".$_GPC['mid']);
            }


        } else {
            if ($set['app']['base']['share']['switch'] == 1 && isset($_GPC['access']) && $_GPC['access'] == 'app' && $member['bindapp'] == 0) {
                /**
                 * 分销商品链接地址
                 */
                header("Location:/app/index.php?i=" . $_W['uniacid'] . "&c=entry&p=bindapp&do=member&m=sz_yi&mid=".$_GPC['mid']);
            }

            $upgrade = array();
            if (!empty($userinfo['nickname']) && $userinfo['nickname'] != $member['nickname']) {
                $upgrade['nickname'] = $userinfo['nickname'];
            }
            if (!empty($userinfo['avatar']) && $userinfo['avatar'] != $member['avatar']) {
                $upgrade['avatar'] = $userinfo['avatar'];
            }
            if (!empty($upgrade)) {
                pdo_update('sz_yi_member', $upgrade, array(
                    'id' => $member['id']
                ));
            }
        }
        if (p('commission')) {
            p('commission')->checkAgent();
        }

        if (p('poster')) {
            p('poster')->checkScan();
        }

        if($bindMobile && is_weixin()){
            /*
            $url = "/app/index.php?i={$_W['uniacid']}&c=entry&p=bindmobile&do=member&m=sz_yi";
            redirect($url);
            exit;
             */
        }
    }
    function getLevels()
    {
        global $_W;
        return pdo_fetchall('select * from ' . tablename('sz_yi_member_level') . ' where uniacid=:uniacid order by level asc', array(
            ':uniacid' => $_W['uniacid']
        ));
    }
    function getLevel($openid)
    {
        global $_W;
        if (empty($openid)) {
            return false;
        }
        $member = m('member')->getMember($openid);
        if (empty($member['level'])) {
            return array(
                'discount' => 10
            );
        }
        $level = pdo_fetch('select * from ' . tablename('sz_yi_member_level') . ' where id=:id and uniacid=:uniacid order by level asc', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $member['level']
        ));
        if (empty($level)) {
            return array(
                'discount' => 10
            );
        }
        return $level;
    }
    function upgradeLevel($openid,$orderid='')
    {
        global $_W;
        if (empty($openid)) {
            return;
        }
        $shopset = m('common')->getSysset('shop');
		$leveltype = intval($shopset['leveltype']);
		$member = m('member')->getMember($openid);
        if (empty($member)) {
			return;
		}
		$level = false;
		if (empty($leveltype)) {
			$ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id=og.orderid ' . ' where o.openid=:openid and o.status=3 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('sz_yi_member_level') . " where uniacid=:uniacid  and {$ordermoney} >= ordermoney and ordermoney>0  order by level desc limit 1", array(':uniacid' => $_W['uniacid']));
		} else if ($leveltype == 1) {
			$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where openid=:openid and status=3 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('sz_yi_member_level') . " where uniacid=:uniacid  and {$ordercount} >= ordercount and ordercount>0  order by level desc limit 1", array(':uniacid' => $_W['uniacid']));
        } else if ($leveltype == 2) {
            $goods = pdo_fetchall('select goodsid from ' . tablename('sz_yi_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
            foreach ($goods as $key => $value) {
                $goodsids[$key] = $value['goodsid'];
            }
            $goodsid = " AND goodsid in ('".implode($goodsids,"','")."') ";
            $level = pdo_fetch('select * from ' . tablename('sz_yi_member_level') . " where uniacid=:uniacid ".$goodsid." order by level desc limit 1", array(':uniacid' => $_W['uniacid']));
		}
		if (empty($level)) {
			return;
		}
		if ($level['id'] == $member['level']) {
			return;
		}
		$oldlevel = $this->getLevel($openid);
		$isup = false;
		if (empty($oldlevel['id'])) {
			$isup = true;
		} else {
			if ($level['level'] > $oldlevel['level']) {
				$isup = true;
			}
		}
		if ($isup) {
			pdo_update('sz_yi_member', array('level' => $level['id'],'upgradeleveltime' => time()), array('id' => $member['id']));
			m('notice')->sendMemberUpgradeMessage($openid, $oldlevel, $level);
		}
    }
    function getGroups()
    {
        global $_W;
        return pdo_fetchall('select * from ' . tablename('sz_yi_member_group') . ' where uniacid=:uniacid order by id asc', array(
            ':uniacid' => $_W['uniacid']
        ));
    }
    function getGroup($openid)
    {
        if (empty($openid)) {
            return false;
        }
        $member = m('member')->getMember($openid);
        return $member['groupid'];
    }
    function setRechargeCredit($openid = '', $money = 0)
    {

        if (empty($openid)) {
            return;
        }
        global $_W;
        $credit = 0;
        $set    = m('common')->getSysset(array(
            'trade',
            'shop'
        ));

        if ($set['trade']) {
            $tmoney  = floatval($set['trade']['money']);
            $tcredit = intval($set['trade']['credit']);
            if ($tmoney > 0) {
                if ($money % $tmoney == 0) {
                    $credit = intval($money / $tmoney) * $tcredit;
                } else {
                    $credit = (intval($money / $tmoney) + 1) * $tcredit;
                }
            }
        }
        if ($credit > 0) {
            $this->setCredit($openid, 'credit1', $credit, array(
                0,
                $set['shop']['name'] . '会员充值积分:credit2:' . $credit
            ));
        }
    }
	//日志记录
	function writelog($str,$title='Error')
	{
		$open=fopen($title.".txt","a" );
		fwrite($open,$str);
		fclose($open);
	}

    //自动执行方法
    function autoexec($uniacid = 0){
        global $_W, $_GPC;
        if(empty($uniacid)){
            return;
        }
        $_W['uniacid'] = $uniacid;
        $shopset = m('common')->getSysset('shop', $_W['uniacid']);

        if ($shopset['term']) {
            //echo "<pre>";print_r($shopset);
            $termtime = '';
            $current_time = time();
            if ( $shopset['term_unit'] == '1' ) {
                $termtime = $shopset['term_time'] * 86400;
            } elseif ( $shopset['term_unit'] == '2' ) {
                $termtime = $shopset['term_time'] * 86400 * 7;
            } elseif ( $shopset['term_unit'] == '3' ) {
                $termtime = $shopset['term_time'] * 86400 * 30;
            } elseif ( $shopset['term_unit'] == '4' ) {
                $termtime = $shopset['term_time'] * 86400 * 365;
            }

            $level = pdo_fetch('UPDATE ' . tablename('sz_yi_member') . " SET level = '0', upgradeleveltime = ".$current_time." where uniacid=:uniacid and level > 0 and (".$current_time." - upgradeleveltime ) >=  ".$termtime, array(':uniacid' => $_W['uniacid']));
        }
    }

    /**
     * 系统自动关闭 返还积分
     *
     * @param $id 订单id
     *
     * @return void
     */
    public function returnCredit($id = '')
    {
        global $_W;

        if (empty($id)) {
            $condition = 'uniacid=:uniacid';
            $param = array(':uniacid' => $_W['uniacid']);
        } else {
            $condition = 'uniacid = :uniacid AND id = :id';
            $param = array(':uniacid' => $_W['uniacid'], ':id'=> $id);
        }

        $orders = pdo_fetchall("SELECT `openid`, `ordersn`, `deductcredit`, `deductprice` FROM " . tablename(sz_yi_order) . " WHERE {$condition} AND `status` = -1", $param);

        foreach ($orders as $k => $v) {
            //抵扣积分
            if ($v["deductcredit"] > 0) {
                $shopset = m("common")->getSysset("shop");
                m("member")->setCredit($v["openid"], "credit1", $v["deductcredit"], array(
                    '0',
                    $shopset["name"] . "购物返还抵扣积分 积分: {$v["deductcredit"]} 抵扣金额: {$v["deductprice"]} 订单号: {$v["ordersn"]}"
                ));
            }
        }
    }
}
