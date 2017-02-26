<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午2:58
 */

namespace app\modules\goods\model\frontend;


class Store
{
    public function getStoreIds(){
        return Goods::getLastDBGoods()["storeids"];
    }
    public function getStores($store_ids){
        return [];
    }
    public function getStoresSend($store_ids){
        return [];
    }
}