<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\services\JsonRpc;
use app\frontend\modules\member\models\SubMemberModel;

class TestController extends BaseController
{
    public function index()
    {

        $result = (new JsonRpc())->client('plus',['user'=>'1','pass'=>2]);
        dd($result);
    }

    public function op_database()
    {$sub_data = array(
        'member_id' => 999,
        'uniacid' => 5,
        'group_id' => 0,
        'level_id' => 0,
    );

    SubMemberModel::insertData($sub_data);

    if (SubMemberModel::insertData($sub_data)) {
        echo 'ok';
    } else {
        echo 'ko';
    }

    }
}