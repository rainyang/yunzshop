<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/7
 * Time: 下午2:01
 */

namespace app\common\models\order;


use app\common\models\BaseModel;
use Ixudra\Curl\Facades\Curl;

class Express extends BaseModel
{
    public $table = 'yz_order_express';

    public function getExpress($express = null, $express_sn = null)
    {
        if (!isset($express)) {
            $express = $this->express_code;
        }
        if (!isset($express_sn)) {
            $express_sn = $this->express_sn;
        }
        $url = sprintf('https://m.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=%s', $express, $express_sn, time());

        $result = Curl::to($url)
            ->asJsonResponse(true)->get();
        if (empty($result)) {
            return array();
        }
        $result['status_name'] = $this->expressStatusName($express['state']);

        return $result;
    }

    private function expressStatusName($key)
    {
        $state_name_map = [
            0 => '在途',
            1 => '揽件',
            2 => '疑难',
            3 => '签收',
            4 => '退签',
            5 => '派件',
            6 => '退回',
        ];
        return $state_name_map[$key];
    }
}