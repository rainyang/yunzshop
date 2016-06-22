<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/28
 * Time: 上午4:30
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member   = m('member')->getMember($openid);
$userinfo = m('user')->getInfo();
$followed = m('user')->followed($openid);
$uid      = 0;
$mc       = array();

$redireurl = urldecode($_GPC['redireurl']);


load()->model('mc');
if ($followed) {
    $uid = mc_openid2uid($openid);
    $mc = mc_fetch($uid, array('realname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
}

if ($_W['isajax']) {
    if ($_W['ispost']) {

        if (is_weixin()) {



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

                $exists = pdo_fetchcolumn("SELECT `id` FROM " . tablename('sz_yi_member') . " WHERE mobile=:mobile AND uniacid=:uniacid", array(
                    'mobile'=> $_GPC['mobile'],
                    'uniacid'=>$_W['uniacid']
                ));

                if ($exists) {
                    pdo_update('sz_yi_member',array(
                        'uid'=> $member['uid'],
                        'openid'=> $member['openid'],
                        'realname'=> $member['realname'],
                        'nickname'=> $member['nickname'],
                        'avatar'=> $member['avatar'],
                        'gender'=> $member['gender'],
                        'province'=> $member['province'],
                        'city'=> $member['city'],
                        'area'=> $member['area'],
                        'createtime'=> $member['createtime'],
                        'bindapp'=>1
                    ),
                        array('mobile'=>$_GPC['mobile'], 'uniacid'=>$_W['uniacid']));

                    show_json(1, array(
                        'redireurl' => $redireurl
                    ));
                } else {
                    show_json(-1);
                }

            } else {
                pdo_update('sz_yi_member',array('mobile'=>$_GPC['mobile'], 'bindapp'=>1),array('openid'=>$openid));

                show_json(1, array(
                    'redireurl' => $redireurl
                ));
            }
        }

    }
}


include $this->template('member/bindapp');