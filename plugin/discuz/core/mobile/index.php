<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/30
 * Time: 上午7:11
 */

global $_GPC, $_W;

$operation   = !empty($_GPC['op']) ? $_GPC['op'] : '' ;

if ($operation == 'info') {
    if (!$this->model->isOpenUC() || !$this->model->chkSynMemberSwitch()) {
        return;
    }

    $exist = pdo_fetch("SELECT * FROM " .tablename('mc_mapping_ucenter') . " WHERE `uniacid` = " . $_W['uniacid'] . " AND `centeruid` =" . $_GPC['centeruid']);

    if (!empty($exist)) {
        if (empty($_GPC['mobile'])) {
            $data = array(
                'realname'=>$_GPC['realname'],
                'gender'=>$_GPC['gender'],
                'birthyear'=>$_GPC['birthyear'],
                'birthmonth'=>$_GPC['birthmonth'],
                'birthday'=>$_GPC['birthday']
            );
        } else {
            $data = array(
                'membermobile'=>$_GPC['mobile']
            );
        }

        pdo_update('sz_yi_member', $data, array('uniacid'=>$exist['uniacid'], 'uid'=>$exist['uid']));
    }
} elseif ($operation == 'credits') {
    if (!$this->model->isOpenUC() || !$this->model->chkSynCreditSwitch()) {
        return;
    }

    if (!empty($_GPC['centeruid']) && !empty($_GPC['credit1'])) {
        $this->model->setShopCredit($_GPC['centeruid'], $_GPC['credit1']);
    }

} elseif ($operation == 'usergroups') {
    if (!$this->model->isOpenUC() || !$this->model->chkSynGroupSwitch()) {
        return;
    }

    if (!empty($_GPC['groupid'])) {
        foreach ($_GPC['groupid'] as $k => $v) {
            $group = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member_group') . " WHERE groupid = {$v}");

            if (empty($group)) {
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'groupid' => trim($v),
                    'status' => 1,
                    'groupname' => trim($_GPC['groupname'][$k])
                );

                pdo_insert('sz_yi_member_group', $data);
            } elseif ($group['status'] == 1) {
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'groupname' => trim($_GPC['groupname'])
                );

                pdo_update('sz_yi_member_group', $data, array('id'=>$group['id']));
            }
        }
    }

} elseif ($operation == 'userregister') {

    $openid = 'u'.md5(mt_rand());

    $fan = mc_fansinfo($openid);

    if (empty($fan)) {
        $userinfo = array(
            "openid" => $openid,
            "nickname" => $_GPC['username'],
            "email" => $_GPC['email'],
            "passowrd" => md5($_GPC['pwd']),
            "sex" => '',
            "city" => '',
            "province" => '',
            "country" => '',
            "headimgurl" => ''
        );


        if(!is_error($userinfo) && !empty($userinfo)) {
            $userinfo['nickname'] = stripcslashes($userinfo['nickname']);
            if (!empty($userinfo['headimgurl'])) {
                $userinfo['headimgurl'] = rtrim($userinfo['headimgurl'], '0') . 132;
            }
            $userinfo['avatar'] = $userinfo['headimgurl'];
            //$_SESSION['userinfo'] = base64_encode(iserializer($userinfo));
        }

        $record = array(
            'openid' => $userinfo['openid'],
            'uid' => 0,
            'acid' => $_W['acid'],
            'uniacid' => $_W['uniacid'],
            'salt' => random(8),
            'updatetime' => TIMESTAMP,
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => 0,
            'followtime' => '',
            'unfollowtime' => 0,
            'tag' => base64_encode(iserializer($userinfo))
        );

        if (!isset($unisetting['passport']) || empty($unisetting['passport']['focusreg'])) {
            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
            $data = array(
                'uniacid' => $_W['uniacid'],
                'email' => $userinfo['email'],
                'groupid' => $default_groupid,
                'createtime' => TIMESTAMP,
                'nickname' => stripslashes($userinfo['nickname']),
                'avatar' => $userinfo['headimgurl'],
                'gender' => $userinfo['sex'],
                'nationality' => $userinfo['country'],
                'resideprovince' => $userinfo['province'] . '省',
                'residecity' => $userinfo['city'] . '市',
            );

            $data['salt']  = random(8);

            $data['password'] = md5($data['email'] . $data['salt'] . $_W['config']['setting']['authkey']);

            //mc_members
            pdo_insert('mc_members', $data);
            $uid = pdo_insertid();
            $record['uid'] = $uid;
            $_SESSION['uid'] = $uid;
        }

        //mc_mapping_fans
        pdo_insert('mc_mapping_fans', $record);
        $_SESSION['openid'] = $record['openid'];
        $_W['fans'] = $record;
        $_W['fans']['from_user'] = $record['openid'];

        //sz_yi_member
        $member = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $uid,
            'openid' => $userinfo['openid'],
            'realname' =>  '',
            'mobile' => '',
            'pwd' => $userinfo['passowrd'],
            'nickname' => stripslashes($userinfo['nickname']),
            'avatar' => $userinfo['avatar'],
            'gender' => $userinfo['sex'],
            'province' => $userinfo['province'],
            'city' => $userinfo['city'],
            'area' => '',
            'createtime' => time(),
            'status' => 1
        );

        pdo_insert('sz_yi_member', $member);
    }
} elseif ($operation == 'userdelete') {
    if (!$this->model->isOpenUC() || !$this->model->chkSynGroupSwitch()) {
        return;
    }

    pdo_delete('mc_mapping_ucenter', array('centeruid' => $_GPC['centeruid']));
}


