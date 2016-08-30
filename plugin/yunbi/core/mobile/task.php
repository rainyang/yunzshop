<?php
global $_W, $_GPC;
load()->func('file');
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
        $tmpdirs = IA_ROOT . "/addons/sz_yi/tmp/yunbi/".date("Ymd");
        if (!is_dir($tmpdirs)) {
            mkdirs($tmpdirs);
        }
    foreach ($sets as $val) {
        $_W['uniacid'] = $val['uniacid'];
        if (empty($_W['uniacid'])) {
            continue;
        }
        $set = m('plugin')->getpluginSet('yunbi', $_W['uniacid']);

        //虚拟币返现到余额
        if (!empty($set) && $set['isreturn_or_remove'] == 0) {
            $return_validation   = $tmpdirs."/return_".date("Ymd").$_W['uniacid'].".txt";
            if (!file_exists($return_validation)) {
                $isexecute = false;
                if (date('H') == $set['yunbi_returntime']) {
                    if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                        //$data  = array_merge($set, array('current_d'=>date('d')));
                        $set['current_d'] = date('d');
                        $this->updateSet($set);
                        $isexecute = true;
                    }
                }
            }else{
                echo "uniacid:".$_W['uniacid'];
                echo date("Y-m-d")."虚拟币已返现！</br>"; 
            }
            if ($_GPC['testtype'] == 'return') {
                $isexecute = true;
            }
            if ( $isexecute ) {
                //虚拟币返现到余额
                p('yunbi')->PerformYunbiReturn($set, $_W['uniacid']);
                touch($return_validation);
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币返现到余额成功！</br>";
            } else {
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币返现到余额失败！</br>";
            }

        }

        if (!empty($set) && $set['isreturn_or_remove'] == 1) {
            //清除虚拟币
            $remove_validation   = $tmpdirs."/remove_".date("Ymd").$_W['uniacid'].".txt";
            if (!file_exists($remove_validation)) {
                $remove_times = explode("||",$set['yunbi_remove_times']);
                $isexecute = false;
                foreach ($remove_times as $k => $v) {
                    if (str_replace(array("日","点"),"",$v) == date('dH')) {
                        if (!isset($set['remove_d']) || $set['remove_d'] != date('d')) {
                            //$data  = array_merge($set, array('current_d'=>date('d')));
                            $set['remove_d'] = date('d');
                            $this->updateSet($set);
                            $isexecute = true;
                            break;
                        }
                    }
                }
            }else{
                echo "uniacid:".$_W['uniacid'];
                echo date("Y-m-d")."虚拟币已清除！</br>"; 
            }

            if ($_GPC['testtype'] == 'remove') {
                $isexecute = true;
            }
            if ( $isexecute ) {
                //清除虚拟币
                p('yunbi')->RemoveYunbi($set, $_W['uniacid']);
                touch($remove_validation);
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币清除成功！</br>";
            } else {
                echo "uniacid:".$_W['uniacid'];
                echo "虚拟币清除失败！</br>";
            }


        }

        //分销下线获得虚拟币
        if (!empty($set) && $set['isdistribution']) {
            $d_validation   = $tmpdirs."/d_".date("Ymd").$_W['uniacid'].".txt";
            if (!file_exists($d_validation)) {
                        $this->updateSet($set);
                if (!isset($set['distribution_d']) || $set['distribution_d'] != date('d')) {
                        $set['distribution_d'] = date('d');
                        $this->updateSet($set);
                        $isdistribution = true;
                }
            }else{
                echo "uniacid:".$_W['uniacid'];
                echo date("Y-m-d")."分销下线已获得虚拟币！</br>"; 
            }

            if ($_GPC['testtype'] == 'distribution') {
                $isdistribution = true;
            }
            if ( $isdistribution) {
                //分销商获得虚拟币
                p('yunbi')->GetVirtual_Currency($set, $_W['uniacid']);
                touch($d_validation);
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




// 	unset($set['current_d']);
// unset($set['current_m']);
// 	$this->updateSet($set);


//定时任务 执行地址
//http://test.yunzhong.com/app/index.php?c=entry&method=task&p=yunbi&m=sz_yi&do=plugin
