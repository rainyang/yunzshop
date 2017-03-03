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
        $uniacid = \YunShop::app()->uniacid;


        $pageSize = 1;
        $list = Member::select(['uid','nickname'])
            ->where(['uniacid'=>$uniacid])
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','group_id','level_id'])
                    ->with(['group'=>function($query1){
                        return $query1->select(['id','group_name']);
                    },'level'=>function($query2){
                        return $query2->select(['id','level_name']);
                    }]);
            }])
            ->paginate($pageSize)
            ->toArray();

        $this->render('member/member_list');
    }
}