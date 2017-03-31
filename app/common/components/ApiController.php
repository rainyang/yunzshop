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

        if (isset(\YunShop::request()->sessoin_id)) {
            session_id(\YunShop::request()->sessoin_id);
        } else {
            session_id($_COOKIE[session_name()]);
        }

        session_start();
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