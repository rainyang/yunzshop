<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\models\MemberModel;
use Illuminate\Session\Store;

class LoginController extends BaseController
{
    public function index()
    {
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> session('member_id')));
        }

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

    private function _validate()
    {
        $data = array(
            'mobile' => \YunShop::request()->mobile,
            'password' => \YunShop::request()->password,
        );
        $validator = \Validator::make($data, array(
            'mobile' => array('required',
                'digits:11',
                'regex:/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1}))+\d{8})$/'
            ),
            'password' => 'required'
        ));

        if ($validator->fails()) {
            return false;
        } else {
            return true;
        }
    }

    public function isLogged()
    {
        return !empty(session('member_id'));
    }
}