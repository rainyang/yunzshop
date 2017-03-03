<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\GoodsComment;
use app\backend\modules\goods\services\GoodsCommentService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

use app\common\helpers\Url;
use app\common\models\Goods;
use app\common\models\Member;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:09
 */
class GoodsCommentController extends BaseController
{
    /**
     * 评论列表
     */
    public function index()
    {
        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 5;
        
        $search = GoodsCommentService::Search(\YunShop::request()->search);
        
        $total = GoodsComment::getCommentTotal(\YunShop::app()->uniacid);
        $list = GoodsComment::getComments(\YunShop::app()->uniacid, $pindex, $psize);

        $pager = PaginationHelper::show($total, $pindex, $psize);
        $this->render('list', [
            'list' => $list,
            'total' => $total,
            'search' => $search,
            'pager' => $pager
        ]);
    }

    /**
     * 评论回复
     */
    public function reply()
    {
        ca('shop.comment.edit');
        $id    = intval(\YunShop::request()->id);
        $comment = GoodsComment::getComment($id);
        $goods = Goods::getGoodsById($comment['goods_id'])->toArray();

        //$order = Order::getOrder($comment['order_id'], \YunShop::app()->uniacid);

        $this->render('reply', [
            'comment' => $comment,
            'goods' => $goods
        ]);
    }

    /**
     * 保存评论回复
     */
    public function saveReply()
    {

        $data = \YunShop::request()->reply;
        $reply = GoodsCommentService::reply($data);
        $result = GoodsComment::updatedComment($reply, $data['id']);

        if ($result){
            Header("Location: " . $this->createWebUrl('goods.goods-comment.reply', ['id' => $data['id']]));
            exit;
        }

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
        $comment = new GoodsComment();
        $comment->goods_id = $goods_id;
        $comment->toArray();


        $this->render('add_info', [
            'comment' => $comment,
            'goods' => $goods
        ]);
    }

    /**
     * 保存添加评论
     */
    public function saveComment()
    {

        $id = \YunShop::request()->id;
        $comment = \YunShop::request()->comment;
        $comment['uniacid'] = \YunShop::app()->uniacid;


        if (empty($comment['nick_name'])) {
            $nick_names = Member::getRandNickName();
            $comment['nick_name'] = $nick_names['nick_name'];
        }
        if (empty($comment['head_img_url'])) {
            $head_img_urls = Member::getRandAvatar();
            $comment['head_img_url'] = $head_img_urls['avatar'];
        }
        if (empty($id)) {
            $comment['created_at'] = time();
            $result = GoodsComment::saveComment($comment);
        } else {
            $result = GoodsComment::updatedComment($comment, $id);
        }
        if ($result) {
            Header("Location: " . $this->createWebUrl('goods.goods-comment.index'));exit;
        }

    }
    /**
     * 修改评论
     */
    public function updated()
    {
        $id = \YunShop::request()->id;
        $comment = GoodsComment::getComment($id);

        if (!empty($comment['goods_id'])) {
            $goods = Goods::getGoodsById($comment['goods_id'])->toArray();
        }
        $this->render('add_info', [
            'id' => $id,
            'comment' => $comment,
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
            Header("Location: " . $this->createWebUrl('goods.goods-comment.index'));exit;
        }

        echo "<pre>"; print_r('删除评论');exit;
    }


    
}