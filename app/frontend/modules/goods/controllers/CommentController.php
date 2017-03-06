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

    private $goods_id;
    private $page_size;

    public function __construct()
    {
        $this->goods_id = \YunShop::request()->goods_id;
        $this->page_size = 2;
    }

    public function getComment()
    {
        $request = Comment::getCommentsByGoods($this->goods_id, $this->page_size);
        return $request->toJson();
    }
}