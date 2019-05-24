<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 18:23
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;
use Setting;
use app\common\models\goods\GoodsService;

class ServiceFee extends Widget
{

    public function run()
    {
        $service = GoodsService::select()->ofGoodsId($this->goods_id)->first();
        $open = Setting::get('goods.service');
        if (!isset($open['service']['open']) || empty($open['service']['open'])){
            $open['service']['open'] = 0;
        }
        return view('goods.widgets.service_fee', [
            'service' =>  $service,
            'open' =>  $open['service']['open']
        ])->render();
    }
}