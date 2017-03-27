<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;


use app\common\models\Member;
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
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize);
        if($list['data']){

            return $this->successJson('获取评论数据成功!', $list);
        }
        return $this->errorJson('未检测到评论数据!',$list);
    }

    public function createComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if(!$member){
            return $this->errorJson('评论失败!未检测到会员数据!');
        }

        $comment = [
            'order_id' => \YunShop::request()->order_id,
            'goods_id' => \YunShop::request()->goods_id,
            'content' => \YunShop::request()->content,
            'level' => \YunShop::request()->level,
        ];
        if(!$comment['order_id']){
            return $this->errorJson('评论失败!未检测到订单ID!');
        }
        if(!$comment['goods_id']){
            return $this->errorJson('评论失败!未检测到商品ID!');
        }
        if(!$comment['content']){
            return $this->errorJson('评论失败!未检测到评论内容!');
        }
        if(!$comment['level']){
            return $this->errorJson('评论失败!未检测到评论等级!');
        }
        
        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        
        $validator = $commentModel->validator($commentModel->getAttributes());
        if ($validator->fails()) {
            //检测失败
            return $this->errorJson($validator->messages());
        } else {
            //数据保存
            if ($commentModel->save()) {
                //显示信息并跳转
                return $this->successJson('评论成功!');
            }else{
                return $this->errorJson('评论失败!');
            }
        }
    }
    

}