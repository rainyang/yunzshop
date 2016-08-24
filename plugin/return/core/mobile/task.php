<?php
global $_W, $_GPC;
set_time_limit(0);

//创建文件锁
$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
$file   = $tmpdir."/filelock.txt";
if (!is_dir($tmpdir)) {
    mkdirs($tmpdir);
}
$return_log = $tmpdir."/return_jog.txt";
$log_content = array();

if (!file_exists($file)) {
    touch($file);
    $log_content[] = date("Y-m-d H:i:s")."返现开始========\r\n";
    $sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
    foreach ($sets as $k => $val) {
        $log_content[] = '公众号ID：'.$val['uniacid']."开始-----------\r\n";
        $log_content[] = '当前时间：'.date("Y-m-d H:i:s")."\r\n";
        $_W['uniacid'] = $val['uniacid'];
        if (empty($_W['uniacid'])) {
            continue;
        }
        $set = m('plugin')->getpluginSet('return', $_W['uniacid']);
        if (!empty($set)) {
            $isexecute = false;
            if ($set['returnlaw'] == 1) {
                $log_content[] = '返现规律：按天返现，每天：'.$set['returntime']."返现\r\n";
                if (date('H') == $set['returntime']) {
                    if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                        //$data  = array_merge($set, array('current_d'=>date('d')));
                        $set['current_d'] = date('d');
                        $this->updateSet($set);
                        $isexecute = true;
                    }
                }
            } elseif ($set['returnlaw'] == 2) {
                $log_content[] = "返现规律：按月返现！\r\n";
                if (!isset($set['current_m']) || $set['current_m'] != date('m')) {
                    //$data  = array_merge($set, array('current_m'=>date('m')));
                    $set['current_m'] = date('m');
                    $this->updateSet($set);
                    $isexecute = true;
                }
            } elseif ($set['returnlaw'] == 3) {
                $log_content[] = "返现规律：按周返现！\r\n";
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
                $log_content[] = "当前可以返现\r\n";
                if ($set["returnrule"] == 1) {
                    //单笔订单
                    $log_content[] = "返现类型：单笔订单返现\r\n";
                    p('return')->setOrderReturn($set, $_W['uniacid']);
                } else {
                    //订单累计金额
                    $log_content[] = "返现类型：订单累计金额返现\r\n";
                    p('return')->setOrderMoneyReturn($set, $_W['uniacid']);

                }
                echo "<pre>";
                print_r('返现成功');
                echo "</br>";

            } else {
                $log_content[] = "当前不可返现\r\n";
                echo "<pre>";
                print_r('返现失败');
                echo "</br>";
            }

        }
        $log_content[] = "公众号ID：".$val['uniacid']."结束-----------\r\n\r\n";
    }
    @unlink ($file);

    $log_content[] = date("Y-m-d H:i:s")."返现任务执行完成===================\r\n \r\n \r\n";
    file_put_contents($return_log,$log_content,FILE_APPEND);
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
