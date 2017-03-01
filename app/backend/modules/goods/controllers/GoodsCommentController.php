<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\GoodsComment;
use app\backend\modules\goods\services\GoodsCommentService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

use app\common\models\Goods;
use app\common\models\Order;
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
        $psize = 2;
        
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
        $result = GoodsComment::reply($reply, $data['id']);
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
        $comment['goods_id'] = '';
        $comment['head_img_url'] = '';
        $comment['nick_name'] = '';
        $comment['level'] = '';
        $comment['content'] = '';
        $comment['images'] = '';
        $comment['reply_content'] = '';
        $comment['reply_images'] = '';
        $comment['append_content'] = '';
        $comment['append_images'] = '';
        $comment['append_reply_content'] = '';
        $comment['append_reply_images'] = '';

        $this->render('add_info', [
            'comment' => $comment,
            'goods' => $goods
        ]);
    }

    /**
     * 修改评论
     */
    public function updated()
    {
        echo "<pre>"; print_r('修改评论');exit;
    }

    /**
     * 删除评论
     */
    public function deleted()
    {
        echo "<pre>"; print_r('删除评论');exit;
    }


    
}