<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/20
 * Time: 下午2:21
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\memberCart\MemberCartCollection;

class CreateAllController extends CreateController
{

    /**
     * @return MemberCartCollection|\Illuminate\Database\Eloquent\Collection|mixed|static
     * @throws \app\common\exceptions\AppException
     */
    protected function _getMemberCarts()
    {
        return MemberService::getCurrentMemberModel()->memberCarts->slice(0,50);
    }
}