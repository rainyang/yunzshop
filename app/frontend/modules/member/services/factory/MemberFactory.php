<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 下午2:27
 */

namespace app\frontend\modules\member\services\factory;

use app\frontend\modules\member\services\MemberMcService;
use app\frontend\modules\member\services\MemberWechatService;
use app\frontend\modules\member\services\MemberAppWechatService;
use app\frontend\modules\member\services\MemberMiniAppService;
use app\frontend\modules\member\services\MemberOfficeAccountService;
use app\frontend\modules\member\services\MemberQQService;

class MemberFactory
{
    public static function create($type = null)
    {
        $className = null;

        switch($type)
        {
            case "1":
                $className = new MemberOfficeAccountService();
                break;
            case "2":
                $className = new MemberMiniAppService();
                break;
            case "3":
                $className = new MemberAppWechatService();
                break;
            case "4":
                $className = new MemberWechatService();
                break;
            case "5":
                $className = new MemberMcService();
                break;
            case "6":
                $className = new MemberQQService();
                break;
            default:
                $className = null;
        }
        return $className;
    }
}