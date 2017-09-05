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

    private $income_set;

    private $key;

    private $key_set;

    private $item;

    private $amount;




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
                'title' => $config['title'],
                'type' => $config['type']
            ];
        }
        if ($searchType) {
            return $this->successJson('获取数据成功!', $searchType);
        }
        return $this->errorJson('未检测到数据!');
    }


    /**
     * 获取收入提现按钮开关
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncomeWithdrawMode()
    {
        $incomeWithdrawMode = IncomeService::getIncomeWithdrawMode();

        if ($incomeWithdrawMode) {
            return $this->successJson('获取数据成功!', $incomeWithdrawMode);
        }

        return $this->errorJson('未检测到数据!');
    }

    /**
     * 可提现数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithdraw()
    {
        $config = \Config::get('income');

        $incomeData = [];
        foreach ($config as $key => $item) {

            if ($item['type'] == 'balance') {
                continue;
            }

            $this->key = $key;
            $this->item = $item;

            $incomeData[] = $this->getItemData();
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
     * 收入提现接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveWithdraw()
    {
        $withdrawData = \YunShop::request()->data;
        if (!$withdrawData) {
            return $this->errorJson('未检测到数据!');
        }


        //如果提现到支付宝，验证会员支付宝配置
        if (!$this->getMemberAlipaySet() && $withdrawData['total']['pay_way'] == 'alipay') {
            return $this->errorJson('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }


        //记录提现数据
        Log::info("POST - Withdraw Total ", print_r($withdrawData['total'],true));
        Log::info("POST - Withdraw Data ", print_r($withdrawData,true));



        //提现数据验证
        $validator = $this->validatorWithdrawData($withdrawData['withdrawal']);
        if ($validator !== true) {
            return $this->errorJson($validator);
        }



        Log::info("提现成功:提现成功");
        $request = static::setWithdraw($withdrawData);
        if ($request) {
            return $this->successJson('提现成功!');
        }
        return $this->errorJson('提现失败!');
    }


    /**
     * 提现数据验证
     * @param array $withdrawData
     * @return bool|string
     */
    private function validatorWithdrawData($withdrawData =[])
    {
        foreach ($withdrawData as $item) {

            $set[$item['key_name']] = \Setting::get('withdraw.' . $item['key_name']);

            $incomes = $this->getIncomeModel()->whereIn('id', explode(',', $item['type_id']))->get();
            $set[$item['key_name']]['roll_out_limit'] = $set[$item['key_name']]['roll_out_limit'] ? $set[$item['key_name']]['roll_out_limit'] : 0;

            Log::info("roll_out_limit:");
            Log::info($set[$item['key_name']]['roll_out_limit']);

            if (bccomp($incomes->sum('amount'), $set[$item['key_name']]['roll_out_limit'], 2) == -1) {
                return '提现失败,' . $item['type_name'] . '未达到提现标准!';
            }
        }
        return true;
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
     * 增加数据库事务
     * @param array $withdrawData
     * @return mixed
     */
    private function setWithdraw($withdrawData =[])
    {
        return DB::transaction(function () use ($withdrawData) {
            return $this->_setWithdraw($withdrawData);
        });
    }

    /**
     * @param array $withdrawData
     * @return mixed
     */
    private function _setWithdraw($withdrawData =[])
    {
        foreach ($withdrawData['withdrawal'] as $item) {
            $data[] = [
                'withdraw_sn'       => Pay::setUniacidNo(\YunShop::app()->uniacid),
                'uniacid'           => \YunShop::app()->uniacid,
                'member_id'         => \YunShop::app()->getMemberId(),
                'type'              => $item['type'],
                'type_name'         => $item['type_name'],
                'type_id'           => $item['type_id'],
                'amounts'           => $item['income'],
                'poundage'          => $item['poundage'],
                'poundage_rate'     => $item['poundage_rate'],
                'actual_amounts'    => $item['income'] - $item['poundage'] - $item['servicetax'],
                'actual_poundage'   => $item['poundage'],
                'servicetax'        => $item['servicetax'],
                'servicetax_rate'   => $item['servicetax_rate'],
                'actual_servicetax' => $item['servicetax'],
                'pay_way'           => $withdrawData['total']['pay_way'],
                'status'            => 0,
                'created_at'        => time(),
                'updated_at'        => time(),
            ];
            static::setIncomeAndOrder($item['type'], $item['type_id']);
        }
        $withdrawData['total']['member_id'] = \YunShop::app()->getMemberId();
        $withdrawData['withdrawal'] = $data;
        event(new AfterIncomeWithdrawEvent($withdrawData));
        Log::info("Withdraw - data", $data);
        return Withdraw::insert($data);
    }




    /**
     * 可提现数据 item
     * @return array
     */
    private function getItemData()
    {
        $this->getKeySet();
        $this->getIncomeSet();
        $this->getIncomeModelSum();

        return [
            'type'              => $this->item['class'],
            'key_name'          => $this->item['type'],
            'type_name'         => $this->item['title'],
            'income'            => $this->amount,
            'poundage'          => $this->getItemPoundage(),
            'poundage_rate'     => $this->getItemPoundageRate(),
            'servicetax'        => $this->getItemServiceTax(),
            'servicetax_rate'   => $this->getItemServiceRate(),
            'roll_out_limit'    => $this->getItemAmountFetter(),
            'can'               => $this->itemIsCanWithdraw(),
            'selected'          => $this->itemIsCanWithdraw(),
            'type_id'           => $this->getItemTypeIds(),
        ];
    }

    /**
     * 检测会员支付宝配置，存在信息返回 true，不存在返回 false
     * @return bool
     */
    private function getMemberAlipaySet()
    {
        $array = MemberShopInfo::select('alipay','alipayname')->where('member_id',\YunShop::app()->getMemberId())->first();
        if ($array && $array['alipay'] && $array['alipayname']) {
            return true;
        }
        return false;
    }


    /**
     * 对应 key (提现挂件) 是否可以提现
     * @return bool
     */
    private function itemIsCanWithdraw()
    {
        if (bccomp($this->amount,$this->getItemAmountFetter(),2) == -1) {
            return false;
        }
        return true;
    }

    /**
     * 获取 item 对应 id 集
     * @return string
     */
    private function getItemTypeIds()
    {
        if ($this->itemIsCanWithdraw()) {
            $type_ids = '';
            foreach ($this->getIncomeModel()->where('incometable_type', $this->item['class'])->get() as $ids) {
                $type_ids .= $ids->id . ",";
            }
            return $type_ids;
        }
        return '';
    }


    /**
     * 获取对应 item 提现最小额度
     * @return string
     */
    private function getItemAmountFetter()
    {
        return $this->judgmentValue($this->key_set['roll_out_limit']);
    }


    /**
     * 获取 item 对应劳务税比例
     * @return string
     */
    private function getItemServiceRate()
    {
        return $this->judgmentValue($this->income_set['servicetax_rate']);
    }


    /**
     * 获取 item 对应劳务税
     * @return string
     */
    private function getItemServiceTax()
    {
        if ($this->getItemServiceRate() && $this->item['type'] != 'StoreCashier') {
            return $this->poundageMath(($this->amount - $this->getItemPoundage()), $this->getItemServiceRate());
        }
        return '0';
    }


    /**
     * 获取 item 对应手续费比例
     * @return string
     */
    private function getItemPoundageRate()
    {
       return $this->judgmentValue($this->key_set['poundage_rate']);
    }


    /**
     * 获取 item 对应手续费
     * @return string
     */
    private function getItemPoundage()
    {
        return $this->poundageMath($this->amount,$this->getItemPoundageRate());
    }


    /**
     * 获取 key (提现挂件) 可提现金额
     * @return mixed
     */
    private function getIncomeModelSum()
    {
        return $this->amount = $this->getIncomeModel()->where('incometable_type', $this->item['class'])->sum('amount');
    }


    /**
     * 获取劳务税比例
     * @return string
     */
    private function getServiceRate()
    {
        return $this->judgmentValue($this->income_set['servicetax_rate']);
    }


    /**
     * 获取收入提现全局设置
     * @return mixed
     */
    private function getIncomeSet()
    {
        return $this->income_set = \Setting::get('withdraw.income');
    }


    /**
     * 获取 key (提现挂件) 对应设置
     * @return mixed
     */
    private function getKeySet()
    {
        return $this->key_set = \Setting::get('withdraw.' . $this->key);
    }


    /**
     * 获取对应 class 实例
     * @param $class
     * @return mixed
     */
    private function getIncomeModel()
    {
        return Income::getIncomes()
            ->where('member_id', \YunShop::app()->getMemberId())
            ->where('status', '0');
            //->where('incometable_type', $this->item['class']);
    }


    /**
     * 手续费计算公式
     * @param $amount
     * @param $rate
     * @return string
     */
    private function poundageMath($amount, $rate)
    {
        return bcmul(bcdiv($amount,100,4),$rate,2);
    }


    /**
     * 数据验证（用于手续费、提现额度限制验证），为空返回 0
     * @param $value
     * @return string
     */
    private function judgmentValue($value)
    {
        return $value ? $value : '0';
    }

}
