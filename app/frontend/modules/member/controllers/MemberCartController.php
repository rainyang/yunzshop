<?php

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use \app\frontend\models\MemberCart;
use app\frontend\modules\member\services\MemberCartService;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 上午10:17
 */
class MemberCartController extends ApiController
{
    //购物车列表
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();
        if ($memberId) {
            $cartList = app('OrderManager')->make('MemberCart')->carts()->where('member_id', $memberId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            //dd($cartList);
            foreach ($cartList as $key => $cart) {
                $cartList[$key]['option_str'] = '';
                $cartList[$key]['goods']['thumb'] = yz_tomedia($cart['goods']['thumb']);
                if (!empty($cart['goods_option'])) {
                    //规格数据替换商品数据
                    if ($cart['goods_option']['title']) {
                        $cartList[$key]['option_str'] = $cart['goods_option']['title'];
                    }
                    if ($cart['goods_option']['thumb']) {
                        $cartList[$key]['goods']['thumb'] = yz_tomedia($cart['goods_option']['thumb']);
                    }
                    if ($cart['goods_option']['market_price']) {
                        $cartList[$key]['goods']['price'] = $cart['goods_option']['product_price'];
                    }
                    if ($cart['goods_option']['market_price']) {
                        $cartList[$key]['goods']['market_price'] = $cart['goods_option']['market_price'];
                    }
                }
                //unset ($cartList[$key]['goods_option']);
            }
            return $this->successJson('获取列表成功', $cartList);
        }
        return $this->errorJson('未获取到会员ID');
    }

    /**
     * Add member cart
     */
    public function store()
    {
        $this->validate([
            'goods_id' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
            'option_id' => 'integer|min:0',
        ]);
        $data = array(
            'member_id' => \YunShop::app()->getMemberId(),
            'uniacid' => \YunShop::app()->uniacid,
            'goods_id' => request()->input('goods_id'),
            'total' => request()->input('total'),
            'option_id' => (int)request()->input('option_id', 0),
        );
        /**
         * @var MemberCart $cartModel
         */
        $cartModel = app('OrderManager')->make('MemberCart',$data);
//        dd($cartModel);
        //验证商品是否存在购物车,存在则修改数量
        $hasGoodsModel = app('OrderManager')->make('MemberCart')->hasGoodsToMemberCart($data);
        $cart_id = $hasGoodsModel['id'];
//dd($cart_id);
        if ($hasGoodsModel) {
            $hasGoodsModel->total = $hasGoodsModel->total + 1;

            $hasGoodsModel->validate();

            if ($hasGoodsModel->update()) {
                return $this->successJson('添加购物车成功',['cart_id' => $cart_id]);
            }
            return $this->errorJson('数据更新失败，请重试！');
        }

        $validator = $cartModel->validator($cartModel->getAttributes());
        if ($validator->fails()) {
            return $this->errorJson("数据验证失败，添加购物车失败！！！");
        } else {
            if ($cartModel->save()) {
                return $this->successJson("添加购物车成功");
            } else {
                return $this->errorJson("写入出错，添加购物车失败！！！");
            }
        }
        return $this->errorJson("接收数据出错，添加购物车失败!");
    }

    /*
     * 修改购物车商品数量
     * */
    public function updateNum()
    {
        $cartId = request()->input('id');
        $num = request()->input('num');

        if (is_null($cartId)) {
            $cartId = $this->getMemberCarId();
        }

        if ($cartId && $num) {
            $cartModel = app('OrderManager')->make('MemberCart')->find($cartId);
            if ($cartModel) {
                $cartModel->total = $cartModel->total + $num;

                if ($cartModel->total < 1) {
                     $result = MemberCartService::clearCartByIds([$cartModel->id]);
                    if ($result) {
                        return $this->successJson('移除购物车成功。');
                    }
                }
                $cartModel->validate();
                if ($cartModel->update()) {
                    return $this->successJson('修改数量成功');
                }
            }
        }

        return $this->errorJson('未获取到数据，请重试！');
    }

    /*
     * 修改购物车商品数量
     * */
    public function updateNumV2()
    {
        $cartId = request()->input('id');
        $num = intval(request()->input('num'));

        if (is_null($cartId)) {
            $cartId = $this->getMemberCarId();
        }

        if ($cartId && $num) {
            $cartModel = app('OrderManager')->make('MemberCart')->find($cartId);
            if ($cartModel) {
                $cartModel->total = $num;

                if ($cartModel->total < 1) {
                    $result = MemberCartService::clearCartByIds([$cartModel->id]);
                    if ($result) {
                        return $this->successJson('移除购物车成功。');
                    }
                }
                $cartModel->validate();
                if ($cartModel->update()) {
                    return $this->successJson('修改数量成功');
                }
            }
        }

        return $this->errorJson('未获取到数据，请重试！');
    }

    /*
     * Delete member cart
     **/
    public function destroy()
    {

        $ids = explode(',', request()->input('ids'));

        if (is_null(request()->input('ids'))) {
            $ids = $this->getMemberCarId();
        }

        $result = MemberCartService::clearCartByIds($ids);
        if ($result) {
            return $this->successJson('移除购物车成功。');
        }
        throw new AppException('写入出错，移除购物车失败！');


    }

    private function getMemberCarId()
    {
        $cartId = null;
        $memberId = \YunShop::app()->getMemberId();
        $goods_id = request()->input('goods_id');

        if (!is_null($memberId) && !is_null($goods_id)) {
            $cartList = app('OrderManager')->make('MemberCart')->carts()->where('member_id', $memberId)
                ->orderBy('created_at', 'desc')
                ->get();

            if (!$cartList->isEmpty()) {
                collect($cartList)->map(function ($item, $key) use ($goods_id, &$cartId) {

                    if ($item->goods_id == $goods_id) {
                        $cartId = $item->id;
                    }
                });
            }
        }

        return $cartId;
    }
}
