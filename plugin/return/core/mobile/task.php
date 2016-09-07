<?php
global $_W, $_GPC;
set_time_limit(0);
load()->func('file');

//创建文件锁
$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
$file   = $tmpdir."/filelock.txt";
if (!is_dir($tmpdir)) {
    mkdirs($tmpdir);
}
$return_log = $tmpdir."/return_log.txt";
$log_content = array();
if (!file_exists($file)) {
    //$fp = fopen($file, "r+");
    touch($file);
    //if (flock($fp, LOCK_EX)) {  // 进行排它型锁定
        $log_content[] = date("Y-m-d H:i:s")."返现开始========================\r\n";
        $log_content[] = "当前域名：".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\r\n";
        //$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
        $sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid WHERE a.default_acid <> 0 ORDER BY a.`rank` DESC, a.`uniacid` DESC ";
        $sets = pdo_fetchall($sql);
        foreach ($sets as $k => $val) {
            $tmpdirs = IA_ROOT . "/addons/sz_yi/tmp/reutrn/".date("Ymd");
            if (!is_dir($tmpdirs)) {
                mkdirs($tmpdirs);
            }
            $validation      = $tmpdirs."/".date("Ymd").$val['uniacid'].".txt";
            if (!file_exists($validation)) {
                $log_content[] = '公众号ID：'.$val['uniacid']."开始-----------\r\n";
                $log_content[] = '当前时间：'.date("Y-m-d H:i:s")."\r\n";
                $_W['uniacid'] = $val['uniacid'];
                if (empty($_W['uniacid'])) {
                    continue;
                }
                $set = m('plugin')->getpluginSet('return', $_W['uniacid']);

                if (!empty($set)) {

                    if (!isset($set['test'])) {
                        $set['test'] = 1;
                    }else{
                        $set['test'] += 1;
                    }
                    $this->updateSet($set);
                    $log_content[] = "test:".$set['test']."\r\n";

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
                        touch($validation);
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
            } else {
                $log_content[] = "公众号ID：".$val['uniacid'].date("Y-m-d")."已返现\r\n\r\n";
            }
        }

        $log_content[] = date("Y-m-d H:i:s")."返现任务执行完成===================\r\n \r\n \r\n";
        file_put_contents($return_log,$log_content,FILE_APPEND);
        echo "返现任务执行完成!";
        //flock($fp, LOCK_UN);    // 释放锁定
    //}
    //fclose($fp);
    @unlink ($file);
}



// 	unset($set['current_d']);
// unset($set['current_m']);
// 	$this->updateSet($set);


//定时任务 执行地址
//http://yl.yunzshop.com/app/index.php?i=1&c=entry&method=task&p=return&m=sz_yi&do=plugin&twgdh=xjdmg
