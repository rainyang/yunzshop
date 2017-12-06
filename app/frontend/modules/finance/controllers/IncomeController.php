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
use app\common\components\BaseController;
use app\common\events\finance\AfterIncomeWithdrawEvent;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\services\finance\IncomeService;
use app\common\services\Pay;
use app\common\services\PayFactory;
use app\frontend\modules\finance\models\Withdraw;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\CommissionOrder;

class IncomeController extends ApiController
{
    protected $pageSize = 20;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncomeCount()
    {
        $status = \YunShop::request()->status;
        $incomeModel = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId())->get();
        if ($status >= '0') {
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
     * todo 删除该方法、及该方法关联方法，已经移交 IncomeWithdrawController 处理
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveWithdraw()
    {
        $config = \Config::get('income');
        $withdrawData = \YunShop::request()->data;
        if (!$withdrawData) {
            return $this->errorJson('未检测到数据!');
        }
        if (!$this->getMemberAlipaySet() && $withdrawData['total']['pay_way'] == 'alipay') {
            return $this->errorJson('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }
        $withdrawTotal = $withdrawData['total'];
        Log::info("POST - Withdraw Total ", $withdrawTotal);
        Log::info("POST - Withdraw Data ", $withdrawData);
        /**
         * 验证数据
         */
        foreach ($withdrawData['withdrawal'] as $item) {
            $set[$item['key_name']] = \Setting::get('withdraw.' . $item['key_name']);

            $incomes = Income::getIncomes()
                ->where('member_id', \YunShop::app()->getMemberId())
                ->where('status', '0')
                ->whereIn('id', explode(',', $item['type_id']))
                ->get();
            $set[$item['key_name']]['roll_out_limit'] = $set[$item['key_name']]['roll_out_limit'] ? $set[$item['key_name']]['roll_out_limit'] : 0;

            Log::info("roll_out_limit:");
            Log::info($set[$item['key_name']]['roll_out_limit']);

            if (bccomp($incomes->sum('amount'), $set[$item['key_name']]['roll_out_limit'], 2) == -1) {
                return $this->errorJson('提现失败,' . $item['type_name'] . '未达到提现标准!');
            }

        }
        Log::info("提现成功:提现成功");
        $request = static::setWithdraw($withdrawData);
        if ($request) {
            return $this->successJson('提现成功!');
        }
        return $this->errorJson('提现失败!');
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

    /**
     * @param $type
     * @param $typeId
     */
//    public function setCommissionOrder($type, $typeId)
//    {
//        Log::info('setCommissionOrder');
//        $request = CommissionOrder::updatedCommissionOrderWithdraw($type, $typeId, '1');
//    }

    /**
     * @param $withdrawData
     * @param $withdrawTotal
     * @return mixed
     */
    public function setWithdraw($withdrawData)
    {
        return DB::transaction(function () use ($withdrawData) {
            return $this->_setWithdraw($withdrawData);
        });

    }

    public function _setWithdraw($withdrawData)
    {
        foreach ($withdrawData['withdrawal'] as $item) {
            $data[] = [
                'withdraw_sn' => Pay::setUniacidNo(\YunShop::app()->uniacid),
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => \YunShop::app()->getMemberId(),
                'type' => $item['type'],
                'type_name' => $item['type_name'],
                'type_id' => $item['type_id'],
                'amounts' => $item['income'],
                'poundage' => $item['poundage'],
                'poundage_rate' => $item['poundage_rate'],
                'actual_amounts' => $item['income'] - $item['poundage'] - $item['servicetax'],
                'actual_poundage' => $item['poundage'],
                'servicetax' => $item['servicetax'],
                'servicetax_rate' => $item['servicetax_rate'],
                'actual_servicetax' => $item['servicetax'],
                'pay_way' => $withdrawData['total']['pay_way'],
                'status' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ];
            static::setIncomeAndOrder($item['type'], $item['type_id']);
        }
        $withdrawData['total']['member_id'] = \YunShop::app()->getMemberId();
        $withdrawData['withdrawal'] = $data;
        event(new AfterIncomeWithdrawEvent($withdrawData));
        Log::info("Withdraw - data", $data);
        return Withdraw::insert($data);
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
