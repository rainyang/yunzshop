<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 下午2:27
 */

namespace app\frontend\modules\member\services\factory;

use app\frontend\modules\member\services\MemberMobileService;
use app\frontend\modules\member\services\MemberWechatService;
use app\frontend\modules\member\services\MemberAppWechatService;
use app\frontend\modules\member\services\MemberMiniAppService;
use app\frontend\modules\member\services\MemberOfficeAccountService;
use app\frontend\modules\member\services\MemberQQService;

class MemberFactory
{
    const LOGIN_OFFICE_ACCOUNT = 1;
    const LOGIN_MINI_APP = 2;
    const LOGIN_APP_WECHAT = 3;
    const LOGIN_WECHAT = 4;
    const LOGIN_MOBILE = 5;
    const LOGIN_QQ = 6;

    public static function create($type = null)
    {
        $className = null;

        switch($type)
        {
            case self::LOGIN_OFFICE_ACCOUNT:
                $className = new MemberOfficeAccountService();
                break;
            case self::LOGIN_MINI_APP:
                $className = new MemberMiniAppService();
                break;
            case self::LOGIN_APP_WECHAT:
                $className = new MemberAppWechatService();
                break;
            case self::LOGIN_WECHAT:
                $className = new MemberWechatService();
                break;
            case self::LOGIN_MOBILE:
                $className = new MemberMobileService();
                break;
            case self::LOGIN_QQ:
                $className = new MemberQQService();
                break;
            default:
                $className = null;
        }
        return $className;
    }
}