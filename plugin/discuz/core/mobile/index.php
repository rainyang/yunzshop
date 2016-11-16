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
        $exist = pdo_fetch("SELECT * FROM " .tablename('mc_mapping_ucenter') . " WHERE `uniacid` = " . $_W['uniacid'] . " AND `centeruid` =" . $_GPC['centeruid']);

        if (!empty($exist)) {
            $value     = pdo_fetchcolumn("SELECT credit1 FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                ':uid' => $exist['uid']
            ));

            $newcredit = $_GPC['credit1'] + $value;
            if ($newcredit <= 0) {
                $newcredit = 0;
            }

            pdo_update('mc_members', array(
                'credit1' => $newcredit
            ), array(
                'uid' => $exist['uid']
            ));
            if (empty($log) || !is_array($log)) {
                $log = array(
                    $exist['uid'],
                    '未记录'
                );
            }
            $data = array(
                'uid' => $exist['uid'],
                'credittype' => 'credit1',
                'uniacid' => $_W['uniacid'],
                'num' => $_GPC['credit1'],
                'createtime' => TIMESTAMP,
                'operator' => intval($log[0]),
                'remark' => $log[1]
            );
            pdo_insert('mc_credits_record', $data);
        }
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

}


