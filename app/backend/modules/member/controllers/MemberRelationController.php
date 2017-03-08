<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 上午10:11
 */

namespace app\backend\modules\member\controllers;

use app\common\components\BaseController;
use app\backend\modules\member\models\MemberRelation as Relation;

class MemberRelationController extends BaseController
{
    public function index()
    {

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation = $relation->toArray();
        }

        $this->render('member/member_set',[
            'set' => $relation
        ]);

    }

    public function save()
    {
        $setData = \YunShop::request()->setdata;
        $setData['uniacid'] = \YunShop::app()->uniacid;

        if (empty($setData['become_ordercount'])) {
            $setData['become_ordercount'] = 0;
        }

        if (empty($setData['become_moneycount'])) {
            $setData['become_moneycount'] = 0;
        }

        if (empty($setData['become_goods_id'])) {
            $setData['become_goods_id'] = 0;
        }

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation->setRawAttributes($setData);

            $relation->save();
        } else {
            $relation = Relation::create($setData);
        }

        $relation = $relation->toArray();

        $this->render('member/member_set',[
            'set' => $relation
        ]);
    }
}