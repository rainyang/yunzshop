<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/20
 * Time: 下午2:21
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\models\Member;

class CreateAllController extends CreateController
{

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static
     * @throws \app\common\exceptions\AppException
     */
    protected function _getMemberCarts()
    {
        return Member::current()->memberCarts;
    }
}