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
use app\common\models\Goods;

class MemberRelationController extends BaseController
{
    public function index()
    {

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation = $relation->toArray();
        }

        if (!empty($relation['become_goods_id'])) {
            $goods = Goods::getGoodsById($relation['become_goods_id']);
            $goods = $goods->toArray();
        } else {
            $goods = [];
        }
        return view('member.relation', [
            'set' => $relation,
            'goods' => $goods
        ])->render();
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

        return $this->message('保存成功', yzWebUrl('member.member-relation.index'));
    }

    public function query()
    {
        $kwd                = trim(\YunShop::request()->keyword);

        $goods_model= Goods::getGoodsByName($kwd);

        if (!empty($goods_model)) {
            $data = $goods_model->toArray();
        } else {
            $data = [];
        }

        return view('member.goods_query', [
            'goods' => $data
        ])->render();
    }
}