<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:08
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\common\components\BaseController;

class MemberGroupController extends BaseController
{
    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;
        $groups_list = MemberGroup::getMemberGroupList($uniacid);
        //所在会员组会员人数【确认


        //echo '<pre>'; print_r($groups_list); exit;

        $this->render('member/group', [
            'groups_list' => $groups_list,
            'aaaa' => 'aaa'
        ]);
    }
}