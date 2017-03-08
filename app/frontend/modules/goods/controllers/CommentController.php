<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;


use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\Store;

use app\frontend\modules\goods\models\Comment;
use app\frontend\modules\goods\services\CommentService;

class CommentController extends BaseController
{

    public function getComment()
    {
        $goodsId = \YunShop::request()->goods_id;
        $pageSize = 10;
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize)->toArray();
        if($list['data']){
            return $this->successJson($list);
        }
        return $this->errorJson('未检测到数据!',$list);
    }
}