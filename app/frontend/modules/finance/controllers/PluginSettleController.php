<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 13:47
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\modules\finance\services\PluginSettleService;

class PluginSettleController extends ApiController
{
    public function pluginList()
    {
        $list = [];
        $config = \Config::get('income');
        foreach ($config as $key => $value) {
            $list[] = $this->available($key, $value);
        }
        if ($list) {
            return $this->successJson('获取数据成功', $list);
        }

        return $this->errorJson('未开启手动结算');
    }

    protected function available($key, $value)
    {
        switch ($key) {
            case 'merchant':
                if (\Setting::get('plugin.merchant.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-merchant',
                    ];
                }
                break;
            case 'commission':
                if (\Setting::get('plugin.commission.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-commission',
                    ];
                }
                break;
            case 'areaDividend':
                if (\Setting::get('plugin.area_dividend.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-area-dividend',
                    ];
                }
                break;
            case 'teamDividend':
                if (\Setting::get('plugin.team_dividend.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-team-dividend',
                    ];
                }
                break;
            default:
                $arr = [];
        }

        return $arr;
    }

    //获取插件可结算佣金列表
    public function getNotSettleList()
    {
        $type = \YunShop::request()->plugin_type;
        $member_id = \YunShop::app()->getMemberId();

        if (empty($type) || empty($member_id)) {
            throw new AppException('参数错误');
        }

        $plugin_income = \Config::get('income.'.$type);
        $class = array_get($plugin_income,'class');
        $function = 'getNotSettleInfo';
        if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])){
           $result = $class::$function(['member_id' => $member_id])->paginate(15);
        }

        if ($result->isEmpty()) {
            throw new AppException('暂无数据');
        }
        $data_processing = PluginSettleService::create($type);

        if (is_null($data_processing)) {
            throw new AppException('数据处理出错');
        }
        $data = [
            'total'   => $result->total(),
            'perPage' => $result->perPage(),
            'data'    => $data_processing->sameFormat($result),
        ];

        return $this->successJson('获取数据成功', $data);
    }

    //招商分红
    public function pluginMerchant()
    {

    }

    //分销佣金
    public function pluginCommission()
    {

    }

    //经销商提成
    public function pluginTeamDividend()
    {
//        $member_id = \YunShop::app()->getMemberId();
//
//        $config = \Config::get('income.merchant');

    }

    //区域分红
    public function pluginAreaDividend()
    {

    }
}