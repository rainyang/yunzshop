<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\models\Member;
use app\common\models\OrderGoods;
use app\frontend\modules\goods\models\Comment;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CommentController extends ApiController
{

    public function getComment()
    {
        $goodsId = \YunShop::request()->goods_id;
        $pageSize = 20;
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize);//

        if ($list) {
            foreach ($list as &$item) {
                $item->reply_count = $item->hasManyReply->count('id');
            }
            return $this->successJson('获取评论数据成功!', $list);
        }
        return $this->errorJson('未检测到评论数据!', $list);
    }

    public function createComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('评论失败!未检测到会员数据!');
        }
        $commentStatus = '1';

        $comment = [
            'order_id' => \YunShop::request()->order_id,
            'goods_id' => \YunShop::request()->goods_id,
            'content' => \YunShop::request()->content,
            'level' => \YunShop::request()->level,
        ];
        if (!$comment['order_id']) {
            return $this->errorJson('评论失败!未检测到订单ID!');
        }
        if (!$comment['goods_id']) {
            return $this->errorJson('评论失败!未检测到商品ID!');
        }
        if (!$comment['content']) {
            return $this->errorJson('评论失败!未检测到评论内容!');
        }
        if (!$comment['level']) {
            return $this->errorJson('评论失败!未检测到评论等级!');
        }


        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->type = '1';
        $this->insertComment($commentModel, $commentStatus);

    }

    public function appendComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('追加评论失败!未检测到会员数据!');
        }
        $commentStatus = '2';
        $id = \YunShop::request()->id;
        $append = $commentModel::find($id);
        if (!$append) {
            return $this->errorJson('追加评论失败!未检测到评论数据!');
        }

        $comment = [
            'order_id' => $append->order_id,
            'goods_id' => $append->goods_id,
            'content' => \YunShop::request()->content,
            'comment_id' => $append->id,
        ];

        if (!$comment['content']) {
            return $this->errorJson('追加评论失败!未检测到评论内容!');
        }

        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->reply_id = $append->uid;
        $commentModel->reply_name = $append->nick_name;
        $commentModel->type = '3';

        $this->insertComment($commentModel, $commentStatus);

    }

    public function replyComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('回复评论失败!未检测到会员数据!');
        }

        $id = \YunShop::request()->id;
        $reply = $commentModel::find($id);
        if (!$reply) {
            return $this->errorJson('回复评论失败!未检测到评论数据!');
        }
        
        $comment = [
            'order_id' => $reply->order_id,
            'goods_id' => $reply->goods_id,
            'content' => \YunShop::request()->content,
            'comment_id' => $reply->comment_id ? $reply->comment_id : $reply->id,
        ];
        if (!$comment['content']) {
            return $this->errorJson('回复评论失败!未检测到评论内容!');
        }

        if (isset($comment['images']) && is_array($comment['images'])) {
            $comment['images'] = serialize($comment['images']);
        } else {
            $comment['images'] = serialize([]);
        }

        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->reply_id = $reply->uid;
        $commentModel->reply_name = $reply->nick_name;
        $commentModel->type = '2';

        $this->insertComment($commentModel);

    }

    public function insertComment($commentModel, $commentStatus = '')
    {
        $validator = $commentModel->validator($commentModel->getAttributes());
        if ($validator->fails()) {
            //检测失败
            return $this->errorJson($validator->messages());
        } else {
            //数据保存
            if ($commentModel->save()) {
                Goods::updatedComment($commentModel->goods_id);

                if ($commentStatus) {
                    OrderGoods::where('order_id', $commentModel->order_id)
                        ->where('goods_id', $commentModel->goods_id)
                        ->update(['comment_status' => $commentStatus, 'comment_id' => $commentModel->id]);
                }

                return $this->successJson('评论成功!',$commentModel);
            } else {
                return $this->errorJson('评论失败!');
            }
        }
    }


    public function getOrderGoodsComment()
    {
        $orderId = \YunShop::request()->order_id;
        $goodsId = \YunShop::request()->goods_id;
        if (!$orderId) {
            return $this->errorJson('获取评论失败!未检测到订单ID!');
        }
        if (!$goodsId) {
            return $this->errorJson('获取评论失败!未检测到商品ID!');
        }
        $comment = Comment::getOrderGoodsComment()
            ->with('hasOneOrderGoods')
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->where('uid', \YunShop::app()->getMemberId())
            ->first();
        if ($comment) {
            return $this->successJson('获取评论数据成功!', $comment->toArray());
        }
        return $this->errorJson('未检测到评论数据!');


    }


}