<?php
namespace  app\backend\modules\member\controllers;
use app\backend\modules\member\models\MemberCart;

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午8:56
 */
class MemberCartController extends \app\common\components\BaseController
{
    public function index()
    {
        $list = MemberCart::select('select * from users where active = ?', [1]);
        echo '<pre>'; print_r($list);exit;
    }
}