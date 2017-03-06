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
use app\frontend\models\Member;
use League\Flysystem\Exception;

class LoginController extends BaseController
{
    public function index()
    {
        if (Member::isLogged()) {
            show_json(1, array('member_id'=> session('member_id')));
        }

        // 1-公众号;2-小程序;3-微信app;4-pc扫码;5-手机号/app;6-QQ
        $type = \YunShop::request()->type;

        if (!empty($type)) {
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
                    $mobile   = \YunShop::request()->mobile;
                    $password = \YunShop::request()->password;

                    if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost && Member::validate($mobile, $password))) {
                        $member = MemberFactory::create('Mc');
                    }
                    if (SZ_YI_DEBUG) {
                        $member = MemberFactory::create('Mc');
                    }
                    break;
                case '6':
                    $member = MemberFactory::create('QQ');
                    break;
                default:
                    $member = null;
            }

            try{
                $member->login();
            } catch (Exception $e) {
                if ($e->getHttpStatus() != NULL) {
                    header('Status: ' . $e->getHttpStatus());
                    echo $e->getHttpBody();
                }
            }
        } else {
            return show_json(0, array('msg' => '登录失败'));
        }
    }
}