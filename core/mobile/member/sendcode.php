<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$mc = $_GPC['memberdata'];  //'18646588292';
$op      = empty($_GPC['op']) ? 'sendcode' : trim($_GPC['op']);

session_start();
if($op == 'sendcode'){
    $code = rand(1000, 9999);

    $_SESSION['codetime'] = time();
    $_SESSION['code'] = $code;

    $content = "您的安全码是：". $code ."。请不要把安全码泄露给其他人。如非本人操作，可不用理会！";

    $this->sendSms($mc['mobile'], $content);
}
else if ($op == 'checkcode'){
    $code = $_GPC['code']; 

    if(($_SESSION['codetime']+60*5) < time()){
        show_json(0, array(
                'msg' => '验证码已过期,请重新获取'
            ));
        exit;
    }
    if($_SESSION['code'] != $code){
        show_json(0, array(
                'msg' => '验证码错误,请重新获取'
            ));
        exit;
    }
    show_json(1, array('msg' => ''));  
}
else if ($op == 'ismobile'){

        $mc = $_GPC['memberdata'];

        $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where  mobile ="'.$mc['mobile'].'"');

        if($info)
        {
            show_json(1, array('msg' => ''));
            exit;
        }else
        {
            show_json(0, array('msg' => ''));
            exit;
        }
      
}
