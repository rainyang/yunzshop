<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/6
 * Time: 上午9:26
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberFavorite;

class MemberFavoriteController extends BaseController
{
    public function index()
    {
        //todo 需要增加商品信息显示
        $memberId = \YunShop::app()->getMemberId();
        $memberId = '57';
        $favoriteList = MemberFavorite::getFavoriteList($memberId);
        //dd($favoriteList);

        $goodsModel = new GoodsService();

        $i = 0;
        foreach ($favoriteList as $favorite) {
            $favorite['goods'] = $goodsModel->getGoodsByCart($favorite['goods_id']);
            if ($favorite['goods'] != false) {
                $favoriteList[$i] = $favorite;
            } else {
                unset($favoriteList[$i]);
            }
            $i += 1;
        }
        dd($favoriteList);
        return $this->successJson($favoriteList);

    }

    public function isFavorite()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId = '9';
        if (\YunShop::request()->goods_id){
            if (MemberFavorite::getFavoriteByGoodsId(\YunShop::request()->goods_id, $memberId)){
                $data = array(
                    'status' => 1,
                    'message' => '商品已收藏'
                );
            } else {
                $data = array(
                    'status' => 0,
                    'message' => '商品未收藏'
                );
            }
            return $this->successJson('接口访问成功', $data);
        }
        return $this->errorJson('未获取到商品ID');
    }

    public function store()
    {
        if (\YunShop::request()->goods_id) {
            $requestFaveorit = array(
                'member_id' => '9',
                //'member_id' => \YunShop::app()->getMemberId(),
                'goods_id' => \YunShop::request()->goods_id,
                'uniacid' => \YunShop::app()->uniacid
            );

            $favoriteModel = new MemberFavorite();

            $favoriteModel->setRawAttributes($requestFaveorit);
            $favoriteModel->uniacid = \YunShop::app()->uniacid;
            $validator = $favoriteModel->validator($favoriteModel->getAttributes());
            if ($validator->fails()) {
                return $this->errorJson($validator->messages());
            }
            if ($favoriteModel->save()) {
                return $this->successJson('添加收藏成功');
            }
            return $this->errorJson("数据写入出错，请重试！");
        }
        return $this->errorJson("未获取到商品ID");
    }


    public function destroy()
    {
        if (\YunShop::request()->goods_id) {
            $memberId = '9';
            $favoriteModel = MemberFavorite::getFavoriteByGoodsId(\YunShop::request()->goods_id, $memberId);
            if (!$favoriteModel) {
                return $this->errorJson("未找到记录或已删除");
            }
            if ($favoriteModel->delete()) {
                return $this->successJson("移除收藏成功");
            } else {
                return $this->errorJson("数据写入出错，移除收藏失败");
            }
        }
        return $this->errorJson("未获取到商品ID");
    }
}
