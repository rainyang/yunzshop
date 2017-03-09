<?php
namespace app\frontend\modules\member\controllers;
use app\common\components\BaseController;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:17
 */
class MemberCartController extends BaseController
{
    public function index()
    {
        $memberId = '1';

        $cartList = MemberCart::getMemberCartList($memberId);

        $goods = new GoodsService();

        $i = 0;
        foreach ($cartList as $cart) {
            $cart['goods'] = $goods->getGoodsByCart($cart['goods_id'], $cart['option_id']);
            if ($cart['goods'] != false) {
                $cartList[$i] = $cart;
            } else {
                unset($cartList[$i]);
            }
            $i += 1;
        }
        dd($cartList);

        return $this->successJson($cartList);
    }
    /**
     * Add member cart
     */
    public function store()
    {
        $requestcart = array(
            'member_id' => '77',
            'uniacid'   => '8',
            'goods_id'  => '19',
            'total'     => '1',
            'price'     => '100',
            'option_id' => '123'
        );

        $cartModel = new membercart();

        $requestcart = \YunShop::request()->cart;
        if($requestcart) {
            //将数据赋值到model
            $cartModel->setRawAttributes($requestcart);
            //其他字段赋值
            $cartModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $cartModel::validator($cartModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($cartModel->save()) {
                    //输出
                    $msg = "添加购物车成功";
                    return $this->errorJson($msg);
                }else{
                    $msg = "写入出错，添加购物车失败！！！";
                    return $this->successJson($msg);
                }
            }
        }
        $msg = "接受数据出错，添加购物车失败！";
        return $this->errorJson($msg);
    }
    /*
     *  Update memebr cart
     **/
    public function update()
    {
        //需要判断商品状态、限制数量、商品类型（实体、虚拟）
    }
    /*
     * Delete member cart
     **/
    public function destroy()
    {
        $cart = MemberCart::getMemberCartById(\YunShop::request()->id);
        if(!$cart) {
            $msg = "未找到该商品或已经删除";
            return $this->errorJson($msg);
        }

        $result = MemberCart::destroyMemberCart(\YunShop::request()->id);
        if($result) {
            $msg = "移除购物车成功。";
            return $this->successJson($msg);
        }
        $msg = "写入出错，移除购物车失败！";
        return $this->errorJson($msg);
    }



}
