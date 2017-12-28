<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/7 下午4:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;

use app\common\events\finance\AfterIncomeWithdrawEvent;
use app\common\events\finance\AfterIncomeWithdrawPayEvent;
use app\common\exceptions\AppException;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\IncomeService;
use app\common\services\PayFactory;
use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\services\WithdrawManualService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeWithdrawController extends ApiController
{


    //提现设置
    private $withdraw_set;


    //收入设置
    private $income_set;


    //提现方式
    private $pay_way;


    //手续费比例
    private $poundage_rate;


    //劳务税比例
    private $service_tax_rate;


    //
    private $special_poundage_rate;


    //
    private $special_service_tax_rate;


    //提现状态
    private $withdraw_status = Withdraw::STATUS_INITIAL;


    //提现金额
    private $withdraw_amounts;



    public function __construct()
    {
        parent::__construct();
        $this->setWithdrawSet();
    }



    //////

    /**
     * 可提现数据接口【完成】
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithdraw()
    {
        $income_config = \Config::get('income');

        $income_data = [];
        foreach ($income_config as $key => $income) {

            //余额不计算
            if ($income['type'] == 'balance') {
                continue;
            }

            //获取收入独立设置
            $this->setIncomeSet($income['type']);

            //附值手续费、劳务税(收银台不计算手续费、劳务税)
            if ($income['type'] == 'StoreCashier') {
                $this->poundage_rate = 0;
                $this->service_tax_rate = 0;
                $this->special_poundage_rate = 0;
                $this->special_service_tax_rate = 0;
            } else {
                $this->setPoundageRate($income['type']);
                $this->setServiceTaxRate();
                $this->setSpecialPoundageRate();
                $this->setSpecialServiceTaxRate();
            }


            $income_data[] = $this->getItemData($key, $income);
        }

        if ($income_data) {
            $data = [
                'data' => $income_data,
                'setting' => ['balance_special' => $this->getBalanceSpecialSet()]
            ];
            return $this->successJson('获取数据成功!', $data);
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


    /////


    /**
     * 收入提现数据提交接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveWithdraw()
    {
        $withdraw_data = $this->getPostValue();
        Log::info('收入提现开始：', print_r($withdraw_data, true));

        $this->pay_way = $withdraw_data['total']['pay_way'];

        if ($this->isFreeAudit() === true) {
            $this->withdraw_status = Withdraw::STATUS_PAY;
        }

        switch ($this->pay_way)
        {
            case 'balance':
                $result = $this->withdrawToBalance($withdraw_data);
                break;
            case 'wechat':
                $result = $this->withdrawToWeChat($withdraw_data);
                break;
            case 'alipay':
                $result = $this->withdrawToAlipay($withdraw_data);
                break;
            case 'manual':
                $result = $this->withdrawToManual($withdraw_data);
                break;
            default:
                $result = '非法提现方式';
                break;
        }

        if ($result === true) {
            return $this->successJson('提现成功!');
        }
        Log::info('收入提现-提现失败：', print_r($withdraw_data, true));
        return $this->errorJson($result ?: '提现失败');
    }


    /**
     * 提现到余额
     * @param $withdraw_data
     * @return bool
     */
    private function withdrawToBalance($withdraw_data)
    {
        DB::beginTransaction();

        $withdraw_items = $this->getWithdrawItems($withdraw_data['withdrawal'], $withdraw_data['total']['amounts']);

        //todo 兼容原杨雷设计事件 需要重构事件模型
        $this->afterIncomeWithdrawEvent($withdraw_data, $withdraw_items);

        foreach ($withdraw_items as $key => $item) {
            $result = Withdraw::insert($item);
            if (!$result) {
                DB::rollBack();
                return false;
            }


            if ($this->isFreeAudit()) {
                $remark = '提现打款-' . $item['type_name'] . '-金额:' . $item['actual_amounts'] . '元,';
                Log::info('收入提现余额免审核打款开始：'. $remark, print_r($item, true));
                $result = (new BalanceChange())->income($this->getBalancePayData($item, $remark));
                if ($result !== true) {
                    //Log::info('收入提现余额免审核打款失败：'. $remark, print_r($this->getBalancePayData($item, $remark), true));
                    //throw new AppException('提现失败：微信打款失败');
                    DB::rollBack();
                    return false;
                }
            }
        }
        DB::commit();
        return true;
    }


    /**
     * 提到微信
     * @param $withdraw_data
     * @return bool
     */
    private function withdrawToWeChat($withdraw_data)
    {
        DB::beginTransaction();

        $withdraw_items = $this->getWithdrawItems($withdraw_data['withdrawal'], $withdraw_data['total']['amounts']);

        //todo 兼容原杨雷设计事件 需要重构事件模型
        $this->afterIncomeWithdrawEvent($withdraw_data, $withdraw_items);

        foreach ($withdraw_items as $key => $item) {

            $result = Withdraw::insert($item);
            if (!$result) {
                DB::rollBack();
                return false;
            }


            if ($this->isFreeAudit()) {
                $remark = '提现打款-' . $item['type_name'] . '-金额:' . $item['actual_amounts'] . '元,';
                Log::info('收入提现微信免审核打款开始：'. $remark, print_r($item, true));
                $result = PayFactory::create(PayFactory::PAY_WEACHAT)->doWithdraw($this->getMemberId(), $item['withdraw_sn'], $item['amounts'], $remark);
                if (!empty($resultPay) && $result['errno'] == 1) {
                    //throw new AppException('提现失败：微信打款失败');
                    DB::rollBack();
                    return false;
                }
            }
        }
        DB::commit();
        return true;
    }


    /**
     * 提现到支付宝
     * @param $withdraw_data
     * @return bool
     * @throws AppException
     */
    private function withdrawToAlipay($withdraw_data)
    {
        if (!WithdrawManualService::getAlipayStatus()) {
            throw new AppException('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }

        DB::beginTransaction();

        $withdraw_items = $this->getWithdrawItems($withdraw_data['withdrawal'], $withdraw_data['total']['amounts']);

        //todo 兼容原杨雷设计事件 需要重构事件模型
        $this->afterIncomeWithdrawEvent($withdraw_data, $withdraw_items);

        $result = Withdraw::insert($withdraw_items);
        if (!$result > 0) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }


    /**
     * 手动提现
     * @param $withdraw_data
     * @return bool
     * @throws AppException
     */
    private function withdrawToManual($withdraw_data)
    {
        switch ($this->withdraw_set['manual_type']) {
            case Withdraw::MANUAL_TO_WECHAT:
                $result = $this->getWeChatStatus();
                break;
            case Withdraw::MANUAL_TO_ALIPAY:
                $result = $this->getAlipayStatus();
                break;
            default:
                $result = $this->getBankStatus();
        }
        if ($result !== true) {
            throw new AppException($result);
        }

        DB::beginTransaction();

        $withdraw_items = $this->getWithdrawItems($withdraw_data['withdrawal'], $withdraw_data['total']['amounts']);

        //todo 兼容原杨雷设计事件 需要重构事件模型
        $this->afterIncomeWithdrawEvent($withdraw_data, $withdraw_items);

        $result = Withdraw::insert($withdraw_items);
        if (!$result > 0) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * todo 兼容原杨雷设计事件 需要重构事件模型
     * @param $withdraw_data
     */
    private function afterIncomeWithdrawEvent($withdraw_data, $withdrawal)
    {
        $withdraw_data['withdrawal'] = $withdrawal;
        event(new AfterIncomeWithdrawEvent($withdraw_data));
    }


    /**
     * 获取提现记录数组
     * @param array $withdraw_items
     * @return array
     */
    private function getWithdrawItems($withdraw_items = [], $amounts)
    {
        $array = [];
        $this->withdraw_amounts = 0;

        foreach ($withdraw_items as $key => $income) {

            //获取收入独立设置
            $this->setIncomeSet($income['key_name']);


            //附值手续费、劳务税(收银台不计算手续费、劳务税)
            //收银台 不计算手续费、劳务税，提现到余额独立手续费、劳务税也不计算
            if ($income['key_name'] == 'StoreCashier') {
                $this->poundage_rate = 0;
                $this->service_tax_rate = 0;
            } else {
                $this->setPoundageRate($income['key_name']);
                $this->setServiceTaxRate();
            }


            //验证收入提交数据
            $this->validatorIncomeData($income);


            //更新收入提现状态
            $this->updateIncomeStatus($income['type'], $income['type_id']);


            $this->withdraw_amounts += $income['income'];
            $array[] = $this->getWithdrawRecordData($income);
        }

        if ($this->withdraw_amounts != $amounts) {
            throw new AppException('提现失败：提现金额错误');
        }
        return $array;
    }



    /**
     * 收入提现数据验证
     * @param $income
     * @throws AppException
     */
    private function validatorIncomeData($income)
    {
        if ($income['income'] < 1) {
            throw new AppException($income['type_name'] . '提现金额不能小于1元');
        }

        $amount = $this->getIncomeModel()->whereIn('id', explode(',', $income['type_id']))->sum('amount');
        if ($amount != $income['income']) {
            throw new AppException($income['type_name'] . '提现金额错误！');
        }

        $roll_out_limit = array_get($this->income_set, 'roll_out_limit', 0);
        $roll_out_limit = empty($roll_out_limit) ? 0 : $roll_out_limit;

        if ($amount < $roll_out_limit) {
            throw new AppException($income['type_name'] . '提现金额不能小于'.$roll_out_limit.'元');
        }

        $poundage = $this->poundageMath($income['income'], $this->poundage_rate);
        $service_tax = $this->poundageMath(($income['income'] - $poundage), $this->service_tax_rate);
        if (($income['income'] - $poundage - $service_tax) < 1) {
            throw new AppException($income['type_name'] . '扣除手续费、劳务税金额不能小于1元');
        }
    }



    /**
     * 通过 income_type 和 type_id 修改对应收入记录状态
     * @param $income_type
     * @param $type_id
     */
    private function updateIncomeStatus($income_type, $type_id)
    {
        //杨雷分销插件 独立规则
        $this->updatePluginMethod($income_type, $type_id);

        //修改收入记录状态
        $data = ['status' => 1,'pay_status'=> 0];
        if ($this->isFreeAudit()) {
            $data = ['status' => 1,'pay_status'=> 2];
        }
        $result = Income::where('member_id', \YunShop::app()->getMemberId())->whereIn('id', explode(',', $type_id))->update($data);
        if (!$result) {
            throw new AppException('提现失败:' . $income_type . '收入记录更新失败');
        }
    }



    /**
     * 杨雷分销插件 独立规则
     * @param $income_type
     * @param $type_id
     */
    private function updatePluginMethod($income_type, $type_id)
    {
        $configs = Config::get('income');
        foreach ($configs as $config) {
            if (isset($config['name']) && ($income_type == $config['class'])) {
                $income = \Yunshop\Commission\models\Income::whereIn('id', explode(',', $type_id))->get();
                foreach ($income as $item) {
                    $config['class']::$config['name']([$config['value'] => 1], ['id' => $item->incometable_id]);
                }
            }
        }
    }



    /**
     * 是否配置银行卡信息
     * @return bool|string
     */
    private function getBankStatus()
    {
        if (!WithdrawManualService::getBankStatus()) {
            return '请先完善您个人信息中支付宝信息';
        }
        return true;
    }



    /**
     * 是否配置微信信息
     * @return bool|string
     */
    private function getWeChatStatus()
    {
        if (!WithdrawManualService::getWeChatStatus()) {
            return '请先完善您个人信息中的微信信息';
        }
        return true;
    }



    /**
     * 是否配置支付宝信息
     * @return bool|string
     */
    private function getAlipayStatus()
    {
        if (!WithdrawManualService::getAlipayStatus()) {
            return '请先完善您个人信息中银行卡信息';
        }
        return true;
    }




    /**
     * set 手续费比例值
     * @param int $poundage_rate
     * @return int|mixed
     */
    private function setPoundageRate($income_type)
    {
        !isset($this->income_set) && $this->income_set = $this->setIncomeSet($income_type);

        $value = array_get($this->income_set, 'poundage_rate', 0);

        //如果使用 提现到余额独立手续费
        if ($this->isUseBalanceSpecialSet()) {
            $value = array_get($this->withdraw_set, 'special_poundage', 0);
        }
        return $this->poundage_rate = empty($value) ? 0 : $value;
    }



    /**
     * set 劳务劳务税比例值
     * @param int $service_tax_rate
     * @return int|mixed
     */
    private function setServiceTaxRate()
    {
        $value = array_get($this->withdraw_set, 'servicetax_rate', 0);

        //如果使用 提现到余额独立劳务税
        if ($this->isUseBalanceSpecialSet()) {
            $value = array_get($this->withdraw_set, 'special_service_tax', 0);
        }
        return $this->service_tax_rate = empty($value) ? 0 : $value;
    }



    /**
     * 提现到余额独立手续费比例
     * @return int|mixed
     */
    private function setSpecialPoundageRate()
    {
        $value = array_get($this->withdraw_set, 'special_poundage', 0);

        return $this->special_poundage_rate = empty($value) ? 0 : $value;
    }



    /**
     * 提现到余额独立劳务税
     * @return int|mixed
     */
    private function setSpecialServiceTaxRate()
    {
        $value = array_get($this->withdraw_set, 'special_service_tax', 0);

        return $this->special_service_tax_rate = empty($value) ? 0 : $value;
    }




    /**
     * 是否使用余额独立手续费、劳务税
     * @return bool
     */
    private function isUseBalanceSpecialSet()
    {
        if ($this->pay_way == Withdraw::WITHDRAW_WITH_BALANCE &&
            $this->getBalanceSpecialSet()
        ) {
            return true;
        }
        return false;
    }



    /**
     * 是否满足免审核条件，满足 true
     * @return bool
     */
    private function isFreeAudit()
    {
        $result = false;
        if ($this->pay_way == Withdraw::WITHDRAW_WITH_BALANCE ||
            $this->pay_way == Withdraw::WITHDRAW_WITH_WECHAT
        ) {
            $result = array_get($this->withdraw_set, 'free_audit', false);
        }

        if ($result) {
            return true;
        }
        return false;
    }




    /**
     * 是否开启提现到余额独立手续费、劳务税
     * @return bool
     */
    private function getBalanceSpecialSet()
    {
        return empty(array_get($this->withdraw_set, 'balance_special', 0)) ? false : true;
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



    /*
     * 获取收入提现全局设置
     * @return mixed
     */
    private function setWithdrawSet()
    {
        return $this->withdraw_set = Setting::get('withdraw.income');
    }



    /**
     * 获取收入类型独立设置
     * @param $income_type
     * @return mixed
     */
    private function setIncomeSet($income_type)
    {
        return $this->income_set = Setting::get('withdraw.' . $income_type);
    }



    /**
     * 获取对应 class 实例
     * @param $class
     * @return mixed
     */
    private function getIncomeModel()
    {
        return Income::uniacid()->canWithdraw()
            ->where('member_id', \YunShop::app()->getMemberId());
        //->where('incometable_type', $this->item['class']);
    }



    private function getPostValue()
    {
        //$withdraw_data = $this->testData();
        $withdraw_data = \YunShop::request()->data;
        if (!$withdraw_data) {
            throw new AppException('未检测到提现提交数据');
        }
        if ($withdraw_data['total']['amounts'] < 1) {
            throw new AppException('提现金额不能小于1元');
        }
        return $withdraw_data;
    }


    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }


    /**
     * 可提现数据 item
     * @return array
     */
    private function getItemData($key, $income)
    {
        $this->withdraw_amounts = $this->getIncomeModel()->where('incometable_type', $income['class'])->sum('amount');

        $poundage = $this->poundageMath($this->withdraw_amounts, $this->poundage_rate);
        $service_tax = $this->poundageMath($this->withdraw_amounts - $poundage, $this->service_tax_rate);


        $special_poundage = $this->poundageMath($this->withdraw_amounts, $this->special_poundage_rate);
        $special_service_tax = $this->poundageMath(($this->withdraw_amounts - $special_poundage), $this->special_service_tax_rate);
        $can = $this->incomeIsCanWithdraw();
        if (in_array($income['type'], ['StoreCashier', 'StoreWithdraw'])) {
            $can = true;
        }
        return [
            'type'              => $income['class'],
            'key_name'          => $income['type'],
            'type_name'         => $this->getLangTitle($key) ? $this->getLangTitle($key) : $income['title'],
            'income'            => $this->withdraw_amounts,
            'poundage'          => $poundage,
            'poundage_rate'     => $this->poundage_rate,
            'servicetax'        => $service_tax,
            'servicetax_rate'   => $this->service_tax_rate,
            'roll_out_limit'    => $this->getIncomeAmountFetter(),
            'can'               => $can,
            'selected'          => $this->incomeIsCanWithdraw(),
            'type_id'           => $this->getIncomeTypeIds($income['class']),
            'special_poundage'  => $special_poundage,
            'special_poundage_rate'  => $this->special_poundage_rate,
            'special_service_tax'    => $special_service_tax,
            'special_service_tax_rate' => $this->special_service_tax_rate,
        ];
    }


    /**
     * 提现最小额度
     * @return string
     */
    private function getIncomeAmountFetter()
    {
        $value = array_get($this->income_set,'roll_out_limit', 0);
        return empty($value) ? 0 : $value;
    }



    /**
     * 是否可以提现
     * @return bool
     */
    private function incomeIsCanWithdraw()
    {
        if (bccomp($this->withdraw_amounts,$this->getIncomeAmountFetter(),2) == -1 || bccomp($this->withdraw_amounts,0,2) != 1) {
            return false;
        }
        return true;
    }

    /**
     * 获取 item 对应 id 集
     * @return string
     */
    private function getIncomeTypeIds($income_class)
    {
        if ($this->incomeIsCanWithdraw()) {
            $type_ids = '';
            foreach ($this->getIncomeModel()->where('incometable_type', $income_class)->get() as $ids) {
                $type_ids .= $ids->id . ",";
            }
            return $type_ids;
        }
        return '';
    }



    /**
     * 获取提现数据 data
     * @return array
     */
    private function getWithdrawRecordData($income)
    {
        $poundage = $this->poundageMath($income['income'], $this->poundage_rate);
        $service_tax = $this->poundageMath(($income['income'] - $poundage), $this->service_tax_rate);
        $actual_amounts = $income['income'] - $poundage - $service_tax;

        return [
            //'withdraw_sn'       => Pay::setUniacidNo(\YunShop::app()->uniacid),
            'withdraw_sn'       => Withdraw::createOrderSn('WS', 'withdraw_sn'),
            'uniacid'           => \YunShop::app()->uniacid,
            'member_id'         => \YunShop::app()->getMemberId(),
            'type'              => $income['type'],
            'type_name'         => $income['type_name'],
            'type_id'           => $income['type_id'],
            'amounts'           => $income['income'],
            'poundage'          => $poundage,
            'poundage_rate'     => $this->poundage_rate,
            'actual_poundage'   => $poundage,
            'actual_amounts'    => $actual_amounts,
            'servicetax'        => $service_tax,
            'servicetax_rate'   => $this->service_tax_rate,
            'actual_servicetax' => $service_tax,
            'pay_way'           => $this->pay_way,
            'manual_type'       => !empty($this->withdraw_set['manual_type']) ? $this->withdraw_set['manual_type'] : 1,
            'status'            => $this->withdraw_status,
            'audit_at'          => $this->isFreeAudit() ? time() : null,
            'pay_at'            => $this->isFreeAudit() ? time() : null,
            'arrival_at'        => $this->isFreeAudit() ? time() : null,
            'created_at'        => time(),
            'updated_at'        => time(),
        ];
    }


    private function getBalancePayData($data, $remark)
    {
        return [

            'member_id'     => $data['member_id'],
            'remark'        => $remark,
            'source'        => ConstService::SOURCE_INCOME,
            'relation'      => $data['withdraw_sn'],
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $data['member_id'],
            'change_value'  => $data['actual_amounts']
        ];
    }





    /************************ todo 杨雷原代码 *********************************/





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
        $incomeModel = Income::getIncomesList($search)->where('member_id', \YunShop::app()->getMemberId())->paginate(20);
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




    public function testData()
    {
        $data = [
            'total' => [
                'amounts' => 1816.01,
                'poundage' => 181.6,
                'pay_way' => 'balance',
            ],
            'withdrawal' => [
                [
                    'type' => 'Yunshop\Commission\models\CommissionOrder',
                    'key_name' => 'commission',
                    'type_name' => '分销佣金',
                    'type_id' => '7469',
                    'income' => '350.00',
                    'poundage' => '35.00',
                    'poundage_rate' => '10',
                    'servicetax' => '31.50',
                    'servicetax_rate' => '10',
                    'can' => '1',
                    'roll_out_limit' => '10',
                    'selected' => 1,
                ],
                [
                    'type' => 'Yunshop\Merchant\common\models\MerchantBonusLog',
                    'key_name' => 'merchant',
                    'type_name' => '招商分红',
                    'type_id' => '6902',
                    'income' => '10.00',
                    'poundage' => '2.00',
                    'poundage_rate' => '20',
                    'servicetax' => '0.80',
                    'servicetax_rate' => '10',
                    'can' => '1',
                    'roll_out_limit' => '10',
                    'selected' => 1,
                ]
            ]
        ];
        return $data;
    }



}
