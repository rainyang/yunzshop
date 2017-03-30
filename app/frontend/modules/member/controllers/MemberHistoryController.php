<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:26
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberFavorite;
use app\frontend\modules\member\models\MemberHistory;


class MemberHistoryController extends BaseController
{
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId = 9;

        $historyList = MemberHistory::getMemberHistoryList($memberId);
        return $this->successJson('获取列表成功', $historyList);
    }

    public function store()
    {
        $memberId = 9;
        //$goodsId = 100;
        $goodsId = \YunShop::request()->goods_id;
        if (!$goodsId) {
            return $this->errorJson('未获取到商品ID，添加失败！');
        }

        $historyModel = MemberHistory::getHistoryByGoodsId($memberId, $goodsId) ?: new MemberHistory();

        $historyModel->goods_id = $goodsId;
        $historyModel->member_id = $memberId;
        $historyModel->uniacid = \YunShop::app()->uniacid;
        if ($historyModel->save()) {
            return $this->successJson('更新足迹成功');
        }
    }

}
