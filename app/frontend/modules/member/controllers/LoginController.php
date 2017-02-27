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
        //islogined;

        // 1-公众号;2-小程序;3-微信app;4-pc扫码;5-手机号/app;6-QQ
        $type = \YunShop::request()->type;

        switch ($type) {
            case '1':
                $member = MemberFactory::create('OfficeAccount');
                $member->login();
                break;
            case '2':
                $member = MemberFactory::create('MiniApp');
                $member->login();
                break;
            case '3':
                $member = MemberFactory::create('AppWechat');
                $member->login();
                break;
            case '4':
                $member = MemberFactory::create('Wechat');
                $member->login();
                break;
            case '5':
                if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost && $this->_validate())) {
                    $member = MemberFactory::create('Mc');
                    $member->login();
                }

                include $this->template('member/login');
                break;
            case '6':
                $member = MemberFactory::create('QQ');
                $member->login();
                break;
        }
    }

    private function validate()
    {}
}