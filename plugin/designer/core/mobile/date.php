<?php
//芸众商城 QQ:913768135

global $_W, $_GPC;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
if ($operation == 'date') {
        if ($_SESSION['data'] && !empty($_SESSION['data']['bdate']) && !empty($_SESSION['data']['day'])) {
            $bdate = $_SESSION['data']['bdate'];
            $day = $_SESSION['data']['day'];
        } else {
            $bdate = date('Y-m-d');
            $day = 1;
        }
       include $this->template('date');
}

if ($operation == 'ajaxData') {
        global $_GPC, $_W;
        $hid = $_GPC['hid'];
        $data=  $this->model->getSearchArray();
        switch ($_GPC['ac'])
        {
            //选择日期
            case 'time':
                $bdate = $_GPC['bdate'];
                $day = $_GPC['day'];
                if (!empty($bdate) && !empty($day)) {
                    $btime = strtotime($bdate);
                    $etime = $btime + $day * 86400;
                    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                    $data['btime'] = $btime;
                    $data['etime'] = $etime;
                    $data['bdate'] = $bdate;
                    $data['edate'] = date('Y-m-d', $etime);
                    $data['bweek'] = '星期' . $weekarray[date("w", $btime)];
                    $data['eweek'] = '星期' . $weekarray[date("w", $etime)];
                    $data['day'] = $day;        
                    //setcookie('data',serialize($data),time()+2*7*24*3600);
                    $_SESSION['data']=$data;
                    $url = $this->createMobileUrl('shop');
                    die(json_encode(array("result" => 1, "url" => $url)));
                }
                break;         

        }
}
$this->setHeader();

