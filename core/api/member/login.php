<?php
$_YZ->validate('username','password');
$setting = $_W['setting'];

$_GPC['username'] = trim($_GPC['username']);
$record = user_single($_GPC);
if(!empty($record)) {
    if($record['status'] == 1) {
        $_YZ->returnError('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！');
    }
    $_W['isfounder'] = $_YZ->isFonder();
    if (!empty($_W['siteclose']) && empty($_W['isfounder'])) {
        $_YZ->returnSuccess('站点已关闭，关闭原因：' . $_W['setting']['copyright']['reason']);
    }
    $record['isfounder'] = $_W['isfounder'];

    $status = array();
    $status['uid'] = $record['uid'];
    $status['lastvisit'] = TIMESTAMP;
    $status['lastip'] = CLIENT_IP;
    user_update($status);
    if($record['type'] == ACCOUNT_OPERATE_CLERK) {
        header('Location:' . url('account/switch', array('uniacid' => $record['uniacid'])));
        die;
    }
}
$_YZ->returnSuccess($record);