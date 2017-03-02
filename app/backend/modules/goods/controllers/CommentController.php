<?php
namespace app\backend\modules\goods\controllers;


use app\common\helpers\Url;
use app\common\models\Goods;
use app\common\models\Member;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use app\backend\modules\goods\models\Comment;
use app\backend\modules\goods\services\CommentService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;



/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:09
 */
class CommentController extends BaseController
{
    /**
     * 评论列表
     */
    public function index()
    {
        $pageSize = 5;
        
        $search = CommentService::Search(\YunShop::request()->search);

        $list = Comment::getComments($pageSize);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $this->render('list', [
            'list' => $list['data'],
            'total' => $list['total'],
            'pager' => $pager,
            'search' => $search,
        ]);
    }

    /**
     * 添加评论
     */
    public function addComment()
    {
        $goods_id = \YunShop::request()->goods_id;
        $goods = [];
        if (!empty($goods_id)) {
            $goods = Goods::getGoodsById($goods_id)->toArray();
        }

        $item = new Comment();
        $item->goods_id = $goods_id;

        $comment = \YunShop::request()->comment;
        if ($comment) {
            $comment['uniacid'] = \YunShop::app()->uniacid;

            if (empty($comment['nick_name'])) {
                $nick_names = Member::getRandNickName();
                $comment['nick_name'] = $nick_names['nick_name'];
            }
            if (empty($comment['head_img_url'])) {
                $head_img_urls = Member::getRandAvatar();
                $comment['head_img_url'] = $head_img_urls['avatar'];
            }

            $comment = CommentService::comment($comment);

            $validator = Comment::validator($comment);
            $item = new Comment($comment);
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $result = Comment::saveComment($comment);
                if ($result) {
                    Header("Location: " . $this->createWebUrl('goods.comment.index'));
                    exit;
                }
            }
        }

        $this->render('add_info', [
            'comment' => $item,
            'goods' => $goods
        ]);
    }

    /**
     * 修改评论
     */
    public function updated()
    {
        $id = \YunShop::request()->id;
        $item = Comment::getComment($id);
        if (!empty($item['goods_id'])) {
            $goods = Goods::getGoodsById($item['goods_id']);
        }

        $comment = \YunShop::request()->comment;
        if ($comment) {
            $comment['uniacid'] = \YunShop::app()->uniacid;

            if (empty($comment['nick_name'])) {
                $nick_names = Member::getRandNickName();
                $comment['nick_name'] = $nick_names['nick_name'];
            }
            if (empty($comment['head_img_url'])) {
                $head_img_urls = Member::getRandAvatar();
                $comment['head_img_url'] = $head_img_urls['avatar'];
            }

            $comment = CommentService::comment($comment);

            $validator = Comment::validator($comment);
            $item = new Comment($comment);
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $result = Comment::updatedComment($comment, $id);
                if ($result) {
                    Header("Location: " . $this->createWebUrl('goods.comment.index'));
                    exit;
                }
            }
        }

        $this->render('add_info', [
            'id' => $id,
            'comment' => $item,
            'goods' => $goods
        ]);

    }

    /**
     * 评论回复
     */
    public function reply()
    {
        ca('shop.comment.edit');
        $id    = intval(\YunShop::request()->id);
        $item = Comment::getComment($id);
        $goods = Goods::getGoodsById($item['goods_id']);

        $replys = Comment::getReplysByCommentId($id)->toArray();

        $reply = \YunShop::request()->reply;
        if ($reply) {
            $member = Member::getMemberById($reply['reply_id']);
            
            $reply = CommentService::reply($reply, $item, $member);

            $validator = Comment::validator($reply);

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $result = Comment::saveComment($reply);
                if ($result) {
                    Header("Location: " . $this->createWebUrl('goods.comment.reply', ['id'=>$id]));
                    exit;
                }
            }
        }

        $this->render('reply', [
            'comment' => $item,
            'replys' => $replys,
            'goods' => $goods
        ]);
    }




    /**
     * 删除评论
     */
    public function deleted()
    {
        $comment = Comment::getComment(\YunShop::request()->id);
        if(!$comment) {
            return $this->message('无此评论或已经删除','','error');
        }

        $result = Comment::daletedComment(\YunShop::request()->id);
        if($result) {
            return $this->message('删除评论成功',Url::absoluteWeb('goods.comment.index'));
        }else{
            return $this->message('删除评论失败','','error');
        }

    }


    
}