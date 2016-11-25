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

}


