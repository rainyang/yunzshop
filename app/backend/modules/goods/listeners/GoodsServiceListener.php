<?php

namespace app\backend\modules\goods\listeners;

use app\common\models\Goods;
use app\common\models\UniAccount;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\goods\GoodsService;
use app\common\models\Goods;

/**
* 
*/
class GoodsServiceListener
{
	
	public function subscribe(Dispatcher $events)
	{
		$events->listen('cron.collectJobs', function () {
			$UniAccount = UniAccount::get();

			foreach ($UniAccount as $u) {
				\Yunshop::app()->uniacid = $u->uniacid;
				\Setting::$uniqueAccountId = $uniacid = $u->uniacid;

                \Log::info("--商品自动上下架--");

                \Cron::add("upperLowerShelves{$u->uniacid}", '*/10 * * * * *', function() use ($uniacid) {
                	$this->handle($uniacid);
            	});

			}
		})
	}

	public function handle($uniacid)
	{
		\YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;

        $goods = Goods::select(['id', 'status'])->whereHas('hasOneGoodsService', function ($query) {
        	return $query->where('is_automatic', 1);
        })->with(['hasOneGoodsService' => function ($query2) {
            return $query2->select(['goods_id', 'on_shelf_time', 'lower_shelf_time']);
        }])->get();


        if ($goods) {
        	$current_time = time();
        	foreach ($goods as $key => $item) {
        		//上架
        		if ($item->hasOneGoodsService->on_shelf_time < $current_time) {
        			if ($item->status == 0) {
        				$item->status = 1;
        				$item->save();
        			}
        		}

        		//下架
        		if ($item->hasOneGoodsService->lower_shelf_time < $current_time) {
        			if ($item->status == 1) {
        				$item->status = 0;
        				$item->save();
        			}
        		}
        	}
        }

	}
}