<?php
global $_W, $_GPC;
set_time_limit(0);
//创建文件锁
$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/yunbi";
$file   = $tmpdir."/filelock.txt";
if (!is_dir($tmpdir)) {
    mkdirs($tmpdir);
}
if (!file_exists($file)) {
    touch($file);

    $sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
    foreach ($sets as $val) {
        $_W['uniacid'] = $val['uniacid'];
        if (empty($_W['uniacid'])) {
            continue;
        }
        $set = m('plugin')->getpluginSet('yunbi', $_W['uniacid']);
        //虚拟币返现到余额
        if (!empty($set) && $set['isreturn_or_remove'] == 0) {
            $isexecute = false;
            if (date('H') == $set['yunbi_returntime']) {
                if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                    //$data  = array_merge($set, array('current_d'=>date('d')));
                    $set['current_d'] = date('d');
                    $this->updateSet($set);
                    $isexecute = true;
                }
            }
            if ( $isexecute ) {
                //虚拟币返现到余额
                p('yunbi')->PerformYunbiReturn($set, $_W['uniacid']);
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币返现到余额成功！</br>";
            } else {
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币返现到余额失败！</br>";
            }
        } elseif (!empty($set) && $set['isreturn_or_remove'] == 1) {
            //清除虚拟币
            $remove_times = explode("||",$set['yunbi_remove_times']);
            $isexecute = false;
            foreach ($remove_times as $k => $v) {
                $removes = explode("号",$v);
                if (date('d') == $removes['0'] && date('H') == $removes['1'] ) {
                    if (!isset($set['remove_d']) || $set['remove_d'] != date('d')) {
                        //$data  = array_merge($set, array('current_d'=>date('d')));
                        $set['remove_d'] = date('d');
                        $this->updateSet($set);
                        $isexecute = true;
                        break;
                    }
                }
            }
            if ( $isexecute ) {
                //清除虚拟币
                p('yunbi')->RemoveYunbi($set, $_W['uniacid']);
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币清除成功！</br>";
            } else {
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币清除失败！</br>";
            }

        }
        //分销下线获得虚拟币
        if (!empty($set) && $set['isdistribution']) {
            $isdistribution = false;
            if (!isset($set['distribution_d']) || $set['distribution_d'] != date('d')) {
                    $set['distribution_d'] = date('d');
                    $this->updateSet($set);
                    $isdistribution = true;
            }
            if ( $isdistribution) {
                //分销商获得虚拟币
                p('yunbi')->GetVirtual_Currency($set, $_W['uniacid']);
                echo "uniacid:".$_W['uniacid'];
                echo "分销下线获得虚拟币成功！</br>";
            } else {
                echo "uniacid:".$_W['uniacid'];
                echo "分销下线获得虚拟币失败！</br>";
            }
        }
        

    }
    @unlink ($file);
    echo "返现任务执行完成!";
}
function mkdirs($path) {
    if (!is_dir($path)) {
        mkdirs(dirname($path));
        mkdir($path);
    }
    return is_dir($path);
}


// 	unset($set['current_d']);
// unset($set['current_m']);
// 	$this->updateSet($set);


//定时任务 执行地址
//http://test.yunzhong.com/app/index.php?c=entry&method=task&p=yunbi&m=sz_yi&do=plugin
