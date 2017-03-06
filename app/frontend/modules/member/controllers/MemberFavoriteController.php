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

    }

    public function store()
    {}

    public function update()
    {}

    public function destory()
    {
        $favoriteId = \YunShop::request()->id;
        $requestModel = MemberFavorite::getFavoriteById($favoriteId);
        if (!$requestModel) {
            $msg = "未找到记录或已删除";
            return $this->errorResult($msg);
        }
        $result = MemberFavorite::destroyFavorite($favoriteId);
        if ($result) {
            $msg = "移除收藏成功";
            return $this->successResult($msg);
        } else {
            $msg = "数据写入出错，移除收藏失败";
            $this->errorResult($msg);
        }
    }

    protected function errorResult($msg, $data='')
    {
        $result = array(
            'result' => '0',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }

    protected function successResult($msg, $data='')
    {
        $result = array(
            'result' => '1',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }
}
