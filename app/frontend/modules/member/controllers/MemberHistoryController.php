<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:26
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberFavorite;
use app\frontend\modules\member\models\MemberHistory;


class MemberHistoryController extends ApiController
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

    public function destroy()
    {
        $historyModel = MemberHistory::getHistoryById(\YunShop::request()->id);
        if (!$historyModel) {
            return $this->errorJson('未找到数据或已删除！');
        }
        if ($historyModel->delete()) {
            return $this->successJson('移除成功');
        }
        return $this->errorJson('未获取到历史记录ID');
    }

}
