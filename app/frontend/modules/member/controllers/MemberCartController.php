<?php
namespace app\frontend\modules\member\controllers;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberCart;

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:17
 */
class MemberCartController extends BaseController
{
    public function index()
    {
        $memberId = '1';
        $list = MemberCart::getMemberCartList($memberId);
        echo '<pre>'; print_r($list); exit;
    }

}