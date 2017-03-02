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
        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 10;
        
        $search = CommentService::Search(\YunShop::request()->search);
        
        $total = Comment::getCommentTotal(\YunShop::app()->uniacid);
        $list = Comment::getComments(\YunShop::app()->uniacid, $pindex, $psize)->toArray();

        $pager = PaginationHelper::show($total, $pindex, $psize);
        $this->render('list', [
            'list' => $list,
            'total' => $total,
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

        if(1) {
            throw new NotFoundHttpException('no found');
        }else{
            Header("Location: " . $this->createWebUrl('goods.comment.index'));exit;
        }

        echo "<pre>"; print_r('删除评论');exit;
    }


    
}