<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class CreateController extends ApiController
{
    private function getMemberCarts(){
        $params = \YunShop::request()->get();
        $result = [];
        foreach ($params['goods'] as $goods_params){
            $result = new MemberCart($goods_params);
        }
        return $result;
    }
    public function index(){
        //dd(defined('IS_TEST'));exit;
        /*if (!defined('IS_TEST')) {
            return;
        }*/
        $params = \YunShop::request()->get();
        $this->validator($params['goods']);
        $member_model = MemberService::getCurrentMemberModel();

        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        $order_goods_models = OrderService::getOrderGoodsModels($this->getMemberCarts());
        list($result, $message) = GoodsService::GoodsListAvailable($order_goods_models);
        if ($result === false) {
            return $this->errorJson($message);
        }
        $order_model = OrderService::getPreGeneratedOrder($order_goods_models,$member_model,$shop_model);
        $order_model->generate();
        $this->successJson();
    }
    private function validator($params){
        if(!is_array($params)){
            throw new AppException('请选择下单商品(非数组)');
        }
        if(!count($params)){
            throw new AppException('请选择下单商品(空数组)');
        }
        foreach ($params as $param){
            if(!isset($param['goods_id'])){
                throw new AppException('请选择下单商品(缺少goods_id)');
            }
            if(!isset($param['total'])){
                throw new AppException('请选择下单商品(缺少total)');
            }
        }
    }
}