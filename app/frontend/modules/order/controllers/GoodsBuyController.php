<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:20
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Yunshop\ServiceFee\models\ServiceFeeModel;
class GoodsBuyController extends ApiController
{
    /**
     * @return MemberCartCollection
     * @throws \app\common\exceptions\AppException
     */
    protected function getMemberCarts()
    {
        $goods_params = [
            'goods_id' => request()->input('goods_id'),
            'total' => request()->input('total'),
            'option_id' => request()->input('option_id'),
        ];
        $result = new MemberCartCollection();
        $result->push(MemberCartService::newMemberCart($goods_params));
        $trade['service'] = $this->service(\YunShop::request()->goods_id);
        return $result;
    }

    /**
     * @throws \app\common\exceptions\ShopException
     */

    protected function validateParam()
    {

        $this->validate([
            'goods_id' => 'required|integer',
            'options_id' => 'integer',
            'total' => 'integer|min:1',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse

     * @throws \app\common\exceptions\ShopException
     */
    public function index()
    {
        $this->validateParam();
        $trade = $this->getMemberCarts()->getTrade();
        return $this->successJson('成功', $trade);
    }


    public function service($goodsId){

        $service = \Setting::get('plugins.service-fee');
       if(app('plugins')->isEnabled('service-fee'))
       {
            $serviceFee = (new ServiceFeeModel())->where(['goods_id' => $goodsId])->first();
            if (!$serviceFee){
                $service['service']['fee'] = 0;
                $service['service']['is_open'] = 0;
                $service['service']['open'] = 0;
            }else{
                $service['service']['fee'] = $serviceFee->fee;
                $service['service']['is_open'] = $serviceFee->is_open;
            }
       }else{
           $service['service']['open'] = 0;
           $service['service']['fee'] = 0;
        }
        return $service;
    }
}