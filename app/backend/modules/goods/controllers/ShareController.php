<?php
/**
 * 商品分享权限操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/24
 * Time: 下午3:11
 */

namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Share;
use app\common\components\BaseController;

class ShareController extends BaseController
{
    /**
     * 商品分享权限详情页
     * @return array $item
     */
    public function index()
    {
        //监听商品基本操作并获得商品id
        $goodsId = 1;// \YunShop::request()->id;//实际商品id为监听添加商品事件获得
        $shopset = m('common')->getSysset('shop');
        $item = Share::getGoodsShareInfo($goodsId);
        //print_r($item);exit;
        $this->render('share', [
            'item' => $item,
            'shopset' => $shopset
        ]);
    }
    /**
     * 商品分享权限信息保存
     * @return
     */
    public function save()
    {
        //监听商品添加或编辑操作并获得商品id及相关数据
        $shareInfo = [
            'goods_id' => 0,
            'need_follow' => '0',
            'no_follow_message' => '123123',
            'follow_message' => '123123',
            'share_title' => '123123',
            'share_thumb' => '123123',
            'share_desc' => '123123',
        ];
        $goodsId =  $shareInfo['goods_id'];
        $item = Share::getGoodsShareInfo($goodsId);
        if (!empty($item)) {
            //updated
             self::update($shareInfo);
        } else {
            //created
            self::create($shareInfo);
        }
    }

    /**
     * 商品分享权限信息添加方法
     * @return
     */
    public function create($shareInfo)
    {
        if (Share::validator($shareInfo) && Share::createdShare($shareInfo)) {
            echo 1;
        } else {
            echo 2;
        }
    }

    /**
     * 商品分享权限信息更新方法
     * @return
     */
    public function update($shareInfo)
    {
        if (Share::validator($shareInfo) && Share::updatedShare($shareInfo['goods_id'], $shareInfo)) {
            echo 1;
            exit;
        } else {
            echo 2;
            exit;
        }
    }

    /**
     * 商品分享权限信息删除方法
     * @return
     */
    public function delete()
    {
        //监听商品添加或编辑操作并获得商品id及相关数据
        $goodsId = \YunShop::request()->id;
        if (Share::deletedShare($goodsId)) {
            //成功
        } else {
            //失败
        }
    }
}