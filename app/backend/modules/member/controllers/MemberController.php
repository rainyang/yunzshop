<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午2:03
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\services\MemberServices;
use app\common\components\BaseController;

class MemberController extends BaseController
{
    public function index()
    {
        $pageSize = 50;
        $list = Member::getMembers($pageSize);
echo '<pre>';print_r($list);exit;
        $opencommission  = false;
        $this->render('member/member_list',['list'=>$list, 'opencommission'=>$opencommission]);
    }
}