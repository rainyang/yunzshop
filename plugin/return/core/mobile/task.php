<?php
global $_W, $_GPC;
set_time_limit(0);

//创建文件锁
$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
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
        $set = m('plugin')->getpluginSet('return', $_W['uniacid']);
        if (!empty($set)) {

            $isexecute = false;
            if ($set['returnlaw'] == 1) {
                if (date('H') == $set['returntime']) {
                    if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                        //$data  = array_merge($set, array('current_d'=>date('d')));
                        $set['current_d'] = date('d');
                        $this->updateSet($set);
                        $isexecute = true;
                    }
                }
            } elseif ($set['returnlaw'] == 2) {
                if (!isset($set['current_m']) || $set['current_m'] != date('m')) {
                    //$data  = array_merge($set, array('current_m'=>date('m')));
                    $set['current_m'] = date('m');
                    $this->updateSet($set);
                    $isexecute = true;
                }
            } elseif ($set['returnlaw'] == 3) {
                if (date("w") == $set['returntimezhou']) {
                    if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                        $set['current_d'] = date('d');
                        $this->updateSet($set);
                        $isexecute = true;
                    }

                }


            }

            if (($set["isreturn"] || $set["isqueue"]) && $isexecute) {
                //p('return')->getmoney($set['orderprice'],$_W['uniacid']);
                if ($set["returnrule"] == 1) {
                    //单笔订单
                    p('return')->setOrderReturn($set, $_W['uniacid']);
                } else {
                    //订单累计金额
                    p('return')->setOrderMoneyReturn($set, $_W['uniacid']);

                }
                echo "<pre>";
                print_r('返现成功');
                echo "</br>";

            } else {
                echo "<pre>";
                print_r('返现失败');
                echo "</br>";
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
//http://yl.yunzshop.com/app/index.php?i=1&c=entry&method=task&p=return&m=sz_yi&do=plugin&twgdh=xjdmg
