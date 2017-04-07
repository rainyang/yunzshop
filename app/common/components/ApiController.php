<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\common\helpers\Client;
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

        $this->setCookie();
        if (!MemberService::isLogged() && !in_array($this->action,$this->publicAction)) {
            $yz_redirect  = \YunShop::request()->yz_redirect;
            $type  = \YunShop::request()->type;

            return $this->errorJson('',['login_status'=>0,'login_url'=>Url::absoluteApi('member.login.index', ['type'=>$type,'yz_redirect'=>$yz_redirect])]);
        }
    }

    private function setCookie()
    {
//        $session_id = '';
//        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
//            $pieces = explode('-', \YunShop::request()->state);
//            $session_id = $pieces[1];
//            unset($pieces);
//        }
//
//        if (!empty($session_id)) {
//            session_id($session_id);
//        }



//        if (empty($session_id) && \YunShop::request()->session_id &&
//            \YunShop::request()->session_id != 'undefined') {
//            $session_id = \YunShop::request()->session_id;
//        }

//        if (empty($session_id)) {
//            $session_id = $_COOKIE[session_name()];
//            \Log::debug('apiController:cookie session_name : '.$session_id);
//        }
//
//        if (empty($session_id)) {
//            $session_id = \YunShop::app()->uniacid . '-' . Client::random(20) ;
//            $session_id = md5($session_id);
//            setcookie(session_name(), $session_id);
//            \Log::debug('apiController: create session_id : '.$session_id);
//        }

        session_save_path('/tmp');
        session_start();

        \Log::debug('apiController: path : '. $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        \Log::debug('apiController: setCookie session_start : '. session_id());
        \Log::debug('apiController: setCookie print cookie : '. print_r($_COOKIE, 1));
        \Log::debug('apicontroller: printCookie result : ' . print_r($_SESSION, 1));
    }
}