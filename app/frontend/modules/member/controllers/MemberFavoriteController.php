<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/6
 * Time: 上午9:26
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberFavorite;

class MemberFavoriteController extends BaseController
{
    public function index()
    {
        dd(1);

    }

    public function store()
    {
        $requestFaveorit = \YunShop::request()->favortie;

        $requestFaveorit = array(
            'member_id' => '57',
            'goods_id' => '1'
        );

        $favoriteModel = new MemberFavorite();

        $favoriteModel->setRawAttributes($requestFaveorit);
        $favoriteModel->uniacid = \YunShop::app()->uniacid;
        $validator = MemberFavorite::validator($favoriteModel->getAttributes());
        if ($validator->fails()) {
            return $this->errorJson($validator->messages());
        }
        if ($favoriteModel->save()) {
            return $this->successJson();
        }
        $msg = "写入数据出错，添加收藏失败！";
        return $this->errorJson($msg);
    }


    public function destory()
    {
        $favoriteId = \YunShop::request()->id;
        $favoriteId = 1;
        $requestModel = MemberFavorite::getFavoriteById($favoriteId);
        if (!$requestModel) {
            $msg = "未找到记录或已删除";
            return $this->errorJson($msg);
        }
        $result = MemberFavorite::destroyFavorite($favoriteId);
        if ($result) {
            $msg = "移除收藏成功";
            return $this->successJson($msg);
        } else {
            $msg = "数据写入出错，移除收藏失败";
            $this->errorJson($msg);
        }
    }
}
