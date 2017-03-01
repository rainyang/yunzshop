<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\models\MemberModel;

class LoginController extends BaseController
{
    private $error = array();

    public function index()
    {
        // 1-公众号;2-小程序;3-微信app;4-pc扫码;5-手机号/app;6-QQ
        $type = \YunShop::request()->type;

        switch ($type) {
            case '1':
                $member = MemberFactory::create('OfficeAccount');
                break;
            case '2':
                $member = MemberFactory::create('MiniApp');
                break;
            case '3':
                $member = MemberFactory::create('AppWechat');
                break;
            case '4':
                $member = MemberFactory::create('Wechat');
                break;
            case '5':
                if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost && $this->_validate())) {
                    $member = MemberFactory::create('Mc');
                }
                if (SZ_YI_DEBUG) {
                    $member = MemberFactory::create('Mc');
                }
                break;
            case '6':
                $member = MemberFactory::create('QQ');
                break;
        }

        $member->login();
    }

    /**
     * pc端微信扫码登录
     */
    public function pcWechatLogin()
    {
        $member = MemberFactory::create('Wechat');
        $userinfo = $member->getUserInfo();


    }

    private function validate()
    {
        return true;
    }
}