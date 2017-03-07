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
        $pageSize = 10;
        
        $search = CommentService::Search(\YunShop::request()->search);

        $list = Comment::getComments()->paginate($pageSize)->toArray();
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

        $commentModel = new Comment();
        $commentModel->goods_id = $goods_id;

        $requestComment = \YunShop::request()->comment;
        if ($requestComment) {

            //将数据赋值到model
            $commentModel->setRawAttributes($requestComment);
            //其他字段赋值
            $commentModel->uniacid = \YunShop::app()->uniacid;
            if (empty($commentModel->nick_name)) {
                $commentModel->nick_name = Member::getRandNickName()->nick_name;
            }
            if (empty($commentModel->head_img_url)) {
                $commentModel->head_img_url = Member::getRandAvatar()->avatar;
            }
            $commentModel = CommentService::comment($commentModel);
            //字段检测
            $validator = Comment::validator($commentModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($commentModel->save()) {
                    //显示信息并跳转
                    return $this->message('评论创建成功', Url::absoluteWeb('goods.comment.index'));
                }else{
                    $this->error('评论创建失败');
                }
            }
        }

        $this->render('add_info', [
            'comment' => $commentModel,
            'goods' => $goods
        ]);
    }

    /**
     * 修改评论
     */
    public function updated()
    {
        $id = \YunShop::request()->id;
        $commentModel = Comment::getComment($id);
        if(!$commentModel){
            return $this->message('无此记录或已被删除','','error');
        }

        if (!empty($commentModel->goods_id)) {
            $goods = Goods::getGoodsById($commentModel->goods_id);
        }
        $requesComment = \YunShop::request()->comment;

        if ($requesComment) {
            //将数据赋值到model
            $commentModel->setRawAttributes($requesComment);

            if (empty($commentModel->nick_name)) {
                $commentModel->nick_name = Member::getRandNickName()->nick_name;
            }
            if (empty($commentModel->head_img_url)) {
                $commentModel->head_img_url = Member::getRandAvatar()->avatar;
            }
            $commentModel = CommentService::comment($commentModel);

            //字段检测
            $validator = Comment::validator($commentModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($commentModel->save()) {
                    //显示信息并跳转
                    return $this->message('评论保存成功', Url::absoluteWeb('goods.comment.index'));
                }else{
                    $this->error('评论保存失败');
                }
            }
        }

        $this->render('add_info', [
            'id' => $id,
            'comment' => $commentModel,
            'goods' => $goods
        ]);

    }

    /**
     * 评论回复
     */
    public function reply()
    {
        $id    = intval(\YunShop::request()->id);
        $commentModel = Comment::getComment($id);
        if(!$commentModel){
            return $this->message('无此记录或已被删除','','error');
        }

        $goods = Goods::getGoodsById($commentModel->goods_id);
        $replys = Comment::getReplysByCommentId($id)->toArray();
        $requestReply = \YunShop::request()->reply;
        if ($requestReply) {
            $member = Member::getMemberById($requestReply['reply_id']);
            $requestReply = CommentService::reply($requestReply, $commentModel, $member);
            //将数据赋值到model
            $commentModel->setRawAttributes($requestReply);
            $validator = Comment::validator($commentModel->getAttributes());
            //字段检测
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //数据保存
                if (Comment::saveComment($commentModel->getAttributes())) {
                    //显示信息并跳转
                    return $this->message('评论回复保存成功', Url::absoluteWeb('goods.comment.reply', ['id' => $id]));
                }else{
                    $this->error('评论回复保存失败');
                }
            }
        }

        $this->render('reply', [
            'comment' => $commentModel,
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