<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 下午2:27
 */

namespace app\frontend\modules\member\services\factory;

use app\frontend\modules\member\services\McMemberService;
use app\frontend\modules\member\services\MemberWechatService;
use app\frontend\modules\member\services\MemberAppWechatService;
use app\frontend\modules\member\services\MemberMiniAppService;
use app\frontend\modules\member\services\MemberOfficeAccountService;
use app\frontend\modules\member\services\MemberQQService;

class MemberFactory
{
    public static function create($className = null)
    {
        $className = 'app\frontend\modules\member\services\Member' . $className . 'Service';
        if ($className && class_exists($className)) {
            return new $className();
        }
        return null;
    }
}