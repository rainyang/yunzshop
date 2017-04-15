<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\common\exceptions\AppException;
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
            $type  = \YunShop::request()->type;


            if (5 == $type) {
                redirect(request()->getSchemeAndHttpHost() . '/addons/yun_shop/#/login')->send();
                exit;
            }

            return $this->errorJson('',['login_status'=>0,'login_url'=>Url::absoluteApi('member.login.index', ['type'=>$type,'session_id'=>session_id()])]);
        }
    }

    private function setCookie()
    {
        $session_id = '';
        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
            $pieces = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (empty($session_id) && \YunShop::request()->session_id &&
            \YunShop::request()->session_id != 'undefined') {
            $session_id = \YunShop::request()->session_id;
        }

        if (!empty($session_id)) {
            session_id($session_id);
        }

        session_start();
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
        //$validator->errors();
        if ($validator->fails()) {
            throw new AppException(current($this->formatValidationErrors($validator)));
        }
    }
}