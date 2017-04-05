<?php
namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberCartService;

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:17
 */
class MemberCartController extends ApiController
{
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();
        $memberId = '9';

        $cartList = MemberCart::getMemberCartList($memberId);
        //dd($cartList);
        foreach ($cartList as $key => $cart) {
            $cartList[$key]['option_str'] = '';
            if (empty($cart['goods'])) {
                //销毁未找到商品的数据
                //unset($cartList[$key]);
            } elseif (!empty($cart['goods_option'])) {
                //规格数据替换商品数据
                if ($cart['goods_option']['title']) {
                    $cartList[$key]['option_str'] = $cart['goods_option']['title'];
                }
                if ($cart['goods_option']['thumb']) {
                    $cart['goods']['thumb'] = $cart['goods_option']['thumb'];
                }
                if ($cart['goods_option']['market_price']) {
                    $cart['goods']['price'] = $cart['goods_option']['market_price'];
                }
                if ($cart['goods_option']['market_price']) {
                    $cart['goods']['price'] = $cart['goods_option']['market_price'];
                }
            }
            //unset ($cartList[$key]['goods_option']);
        }
        //dd($cartList);

        return $this->successJson('获取列表成功', $cartList);
    }

    /**
     * Add member cart
     */
    public function store()
    {
        $cartModel = new membercart();

        $requestcart = \YunShop::request();
        if($requestcart) {
            $data = array(
                'member_id' => '9',
                'uniacid'   => \YunShop::app()->uniacid,
                'goods_id'  => $requestcart->goods_id,
                'total'     => $requestcart->total,
                'option_id' => $requestcart->option_id ? $requestcart->option_id : '0'
            );


            //验证商品是否存在购物车,存在则修改数量
            $hasGoodsModel = MemberCart::hasGoodsToMemberCart($data);
            if ($hasGoodsModel) {
                $hasGoodsModel->total = $hasGoodsModel->total + 1;
                if ($hasGoodsModel->update()){
                    return $this->successJson('添加购物车成功');
                }
                return $this->errorJson('数据更新失败，请重试！');
            }


            //将数据赋值到model
            $cartModel->setRawAttributes($data);
            //字段检测
            $validator = $cartModel->validator($cartModel->getAttributes());
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
        $msg = "接收数据出错，添加购物车失败！";
        return $this->errorJson($msg);
    }

    /*
     * 修改购物车商品数量
     * */
    public function updateNum()
    {
        //@todo 需要添加商品最多购买判断。会员限购数量判断
        $cartId = \YunShop::request()->id;
        $num = \YunShop::request()->num;
        if ($cartId && $num) {
            $cartModel = MemberCart::getMemberCartById($cartId);
            if ($cartModel) {
                $cartModel->total = $cartModel->total + $num;
                if ($cartModel->update()) {
                    return $this->successJson('修改数量成功');
                }
            }
        }

        return $this->errorJson('未找到数据或已删除，请重试！');
    }

    /*
     * Delete member cart
     **/
    public function destroy()
    {
        $ids = explode(',', \YunShop::request()->ids);

        $result = MemberCartService::clearCartByIds($ids);


        if($result) {
            $this->successJson('移除购物车成功。');
        }
        throw new AppException('写入出错，移除购物车失败！');
    }

}
