<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/27
 * Time: 下午10:15
 */

namespace app\frontend\modules\finance\controllers;

use app\common\models\MemberShopInfo;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\services\finance\IncomeService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class IncomeController extends ApiController
{
    protected $pageSize = 20;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncomeCount()
    {
        //todo 为了获取一个数据重复调用此方法，效率地下，需要重构 2018-01-05-YITIAN
        $status = \YunShop::request()->status;
        $incomeModel = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId())->get();
        if ($status !== null && $status >= '0') {
            $incomeModel = $incomeModel->where('status', $status);
        }
        $config = \Config::get('plugin');
        $incomeData['total'] = [
            'title' => '推广收入',
            'type' => 'total',
            'type_name' => '推广佣金',
            'income' => $incomeModel->sum('amount')
        ];
        foreach ($config as $key => $item) {

            $typeModel = $incomeModel->where('incometable_type', $item['class']);
            $incomeData[$key] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['title'],
                'income' => $typeModel->sum('amount')
            ];
            if ($item['agent_class']) {
                $agentModel = $item['agent_class']::$item['agent_name'](\YunShop::app()->getMemberId());

                if ($item['agent_status']) {
                    $agentModel = $agentModel->where('status', 1);
                }

                //推广中心显示
                if (!$agentModel) {
                    $incomeData[$key]['can'] = false;
                } else {
                    $agent = $agentModel->first();
                    if ($agent) {
                        $incomeData[$key]['can'] = true;
                    } else {
                        $incomeData[$key]['can'] = false;
                    }
                }
            } else {
                $incomeData[$key]['can'] = true;
            }

        }
        if ($incomeData) {
            return $this->successJson('获取数据成功!', $incomeData);
        }
        return $this->errorJson('未检测到数据!');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncomeList()
    {
        $configs = \Config::get('income');
        $type = \YunShop::request()->income_type;
        $search = [];
        foreach ($configs as $key => $config) {
            if ($config['type'] == $type) {
                $search['type'] = $config['class'];
                break;
            }
        }

//        $incomeModel = Income::getIncomeInMonth($search)->where('member_id', \YunShop::app()->getMemberId())->get();
        $incomeModel = Income::getIncomesList($search)->where('member_id', \YunShop::app()->getMemberId())->paginate($this->pageSize);
        if ($incomeModel) {
            return $this->successJson('获取数据成功!', $incomeModel);
        }
        return $this->errorJson('未检测到数据!');
    }

    /**
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getDetail()
    {
        $data = "";
        $id = \YunShop::request()->id;
        $detailModel = Income::getDetailById($id);
        if ($detailModel) {
            if ($detailModel->first()->detail != '') {
                $data = $detailModel->first()->detail;
                return '{"result":1,"msg":"成功","data":' . $data . '}';
            }
            return '{"result":1,"msg":"成功","data":""}';
        }
        return $this->errorJson('未检测到数据!');
    }

    public function getLangTitle($data)
    {
        $lang = Setting::get('shop.lang');
        $langData = $lang[$lang['lang']];
        $titleType = '';
        foreach ($langData as $key => $item) {
            $names = explode('_', $key);
            foreach ($names as $k => $name) {
                if ($k == 0) {
                    $titleType = $name;
                } else {
                    $titleType .= ucwords($name);
                }
            }

            if ($data == $titleType) {
                return $item[$key];
            }
        }

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSearchType()
    {


        $configs = \Config::get('income');
        foreach ($configs as $key => $config) {
            if ($config['type'] == 'balance') {
                continue;
            }
            $searchType[] = [
                'title' => $this->getLangTitle($key) ? $this->getLangTitle($key) : $config['title'],
                'type' => $config['type']
            ];
        }
        if ($searchType) {
            return $this->successJson('获取数据成功!', $searchType);
        }
        return $this->errorJson('未检测到数据!');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithdraw()
    {
        $incomeSet = \Setting::get('withdraw.income');
        $config = \Config::get('income');
        Log::info('获取提现数据');
        foreach ($config as $key => $item) {
            if ($item['type'] == 'balance') {
                continue;
            }
            $set[$key] = \Setting::get('withdraw.' . $key);

            $set[$key]['roll_out_limit'] = $set[$key]['roll_out_limit'] ? $set[$key]['roll_out_limit'] : 0;
            $set[$key]['poundage_rate'] = $set[$key]['poundage_rate'] ? $set[$key]['poundage_rate'] : 0;

            $incomeModel = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId());
            $incomeModel = $incomeModel->where('status', '0');

            $incomeModel = $incomeModel->where('incometable_type', $item['class']);
            $amount = $incomeModel->sum('amount');
            $poundage = $incomeModel->sum('amount') / 100 * $set[$key]['poundage_rate'];
            $poundage = sprintf("%.2f", $poundage);
            //劳务税
            $servicetax = 0;
            if ($incomeSet['servicetax_rate'] && ($item['type'] != 'StoreCashier')) {
                $servicetax = ($amount - $poundage) / 100 * $incomeSet['servicetax_rate'];
                $servicetax = sprintf("%.2f", $servicetax);
            }
Log::info($this->getLangTitle($key) ? $this->getLangTitle($key) : $item['title']);
            if (($amount > 0) && (bccomp($amount, $set[$key]['roll_out_limit'], 2) != -1)) {
                $type_id = '';
                foreach ($incomeModel->get() as $ids) {
                    $type_id .= $ids->id . ",";
                }
                $incomeData[] = [
                    'type' => $item['class'],
                    'key_name' => $item['type'],
                    'type_name' => $this->getLangTitle($key) ? $this->getLangTitle($key) : $item['title'],
                    'type_id' => rtrim($type_id, ','),
                    'income' => $incomeModel->sum('amount'),
                    'poundage' => $poundage,
                    'poundage_rate' => $set[$key]['poundage_rate'],
                    'servicetax' => $servicetax,
                    'servicetax_rate' => $incomeSet['servicetax_rate'] ? $incomeSet['servicetax_rate'] : 0,
                    'can' => true,
                    'roll_out_limit' => $set[$key]['roll_out_limit'],
                    'selected' => true,
                ];
            } else {
                $incomeData[] = [
                    'type' => $item['class'],
                    'key_name' => $item['type'],
                    'type_name' => $this->getLangTitle($key) ? $this->getLangTitle($key) : $item['title'],
                    'type_id' => '',
                    'income' => $incomeModel->sum('amount'),
                    'poundage' => $poundage,
                    'poundage_rate' => $set[$key]['poundage_rate'],
                    'servicetax' => $servicetax,
                    'servicetax_rate' => $incomeSet['servicetax_rate'] ? $incomeSet['servicetax_rate'] : 0,
                    'can' => false,
                    'roll_out_limit' => $set[$key]['roll_out_limit'],
                    'selected' => false,
                ];
            }
        }
        if ($incomeData) {
            return $this->successJson('获取数据成功!', $incomeData);
        }
        return $this->errorJson('未检测到数据!');
    }


    /**
     * @param $type
     * @param $typeId
     */
    public function setIncomeAndOrder($type, $typeId)
    {
        static::setIncome($type, $typeId);
//        static::setCommissionOrder($type, $typeId);

        $configs = Config::get('income');
        foreach ($configs as $config) {
            if (isset($config['name']) && ($type == $config['class'])) {
                $income = \Yunshop\Commission\models\Income::whereIn('id', explode(',', $typeId))->get();
                foreach ($income as $item) {
                    $config['class']::$config['name']([$config['value'] => 1], ['id' => $item->incometable_id]);
                }

            }
        }
    }

    /**
     * @param $type
     * @param $typeId
     */
    public function setIncome($type, $typeId)
    {
        Log::info('setIncome');
        $request = Income::updatedWithdraw($type, $typeId, '1');

    }



    public function getIncomeWithdrawMode()
    {
        //finance.income.get-income-withdraw-mode

        $incomeWithdrawMode = IncomeService::getIncomeWithdrawMode();

        if ($incomeWithdrawMode) {
            return $this->successJson('获取数据成功!', $incomeWithdrawMode);
        }

        return $this->errorJson('未检测到数据!');
    }

    private function getMemberAlipaySet()
    {
        $array = MemberShopInfo::select('alipay', 'alipayname')->where('member_id', \YunShop::app()->getMemberId())->first();
        if ($array && $array['alipay'] && $array['alipayname']) {
            return true;
        }
        return false;
    }

}
