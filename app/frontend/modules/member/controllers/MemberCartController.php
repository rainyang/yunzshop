<?php
namespace app\frontend\modules\member\controllers;
use app\common\components\BaseController;
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
        $pageSize = '2';
        $cartList = MemberCart::getMemberCartList($memberId, $pageSize);
        dd($cartList);
        $msg = '';
        return $this->successResult($msg, $cartList);
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
                    return $this->successResult($msg);
                }else{
                    $msg = "写入出错，添加购物车失败！！！";
                    return $this->errorResult($msg);
                }
            }
        }
        $msg = "数据出错，添加购物车失败！";
        return $this->errorResult($msg);
    }
    /**
     * Delete member cart
     */
    public function destroy()
    {
        $level = MemberCart::getMemberCartById(\YunShop::request()->id);
        if(!$level) {
            $msg = "未找到该商品或已经删除";
            return $this->errorResult($msg);
        }

        $result = MemberCart::destroyMemberCart(\YunShop::request()->id);
        if($result) {
            $msg = "移除购物车成功。";
            return $this->successResult($msg);
        }
        $msg = "写入出错，移除购物车失败！";
        return $this->errorResult($msg);
    }

    protected function errorResult($msg, $data='')
    {
        $result = array(
            'result' => '0',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }
    protected function successResult($msg, $data='')
    {
        $result = array(
            'result' => '1',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }


}
