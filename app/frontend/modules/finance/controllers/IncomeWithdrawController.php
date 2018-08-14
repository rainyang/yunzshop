<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/7 下午4:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\services\finance\IncomeService;
use app\frontend\modules\finance\models\Withdraw;

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
            if ($income['type'] == 'StoreCashier' || $income['type'] == 'StoreWithdraw') {
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
     * @param $income_type
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
     * @return mixed
     */
    private function getIncomeModel()
    {
        return Income::uniacid()->canWithdraw()
            ->where('member_id', \YunShop::app()->getMemberId());
        //->where('incometable_type', $this->item['class']);
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



}
