<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\common\helpers\Url;
use app\frontend\modules\member\services\MemberService;

class ApiController extends BaseController
{
    protected $publicAction = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function preAction()
    {
        parent::preAction();

        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state)) {
            $session_id = \YunShop::request()->state;
        } elseif (isset(\YunShop::request()->sessoin_id)) {
            echo 'sid:' . \YunShop::request()->sessoin_id;exit;
            $session_id = \YunShop::request()->sessoin_id;
        } else {
            echo 'cc:' . $_COOKIE[session_name()];
            $session_id = $_COOKIE[session_name()];
        }
        echo 'sid1:' . $session_id;
        session_id($session_id);
echo '<pre>';print_r($_COOKIE);
echo '<pre>';print_r($_COOKIE['PHPSESSID']);
echo session_name();
        echo '<pre>';print_r($_COOKIE[session_name()]);
        echo '<BR>';
       // session_id($_COOKIE[session_name()]);
        session_start();
       // echo '<pre>session: ';print_r($_SESSION);exit;
        if (!MemberService::isLogged() && !in_array($this->action,$this->publicAction)) {
//            echo 'sessid:<BR>';
//            echo session_name(). ':' . session_id();
//            echo '<BR>';
//            echo '<pre>';print_r($_SESSION);exit;
            $yz_redirect  = \YunShop::request()->yz_redirect;
            $type  = \YunShop::request()->type;

            redirect(Url::absoluteApi('member.login.index', ['type'=>$type,'yz_redirect'=>$yz_redirect]))->send();
        }
    }
}