<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/7 下午4:11
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;

use app\common\models\MemberShopInfo;
use app\common\components\ApiController;
use app\common\events\finance\AfterIncomeWithdrawEvent;
use app\common\facades\Setting;
use app\common\models\Income;
use app\common\services\finance\IncomeFreeAuditService;
use app\common\services\finance\IncomeService;
use app\common\services\Pay;
use app\common\services\PayFactory;
use app\frontend\modules\finance\models\Withdraw;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\CommissionOrder;

class IncomeWithdrawController extends ApiController
{
    protected $pageSize = 20;

    private $income_set;

    private $key;

    private $key_set;

    private $item;

    private $amount;

    private $pay_way;



    public function test()
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
        $result = $this->saveWithdraw($data);
        dd($result);
    }

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


    //***************************************VS YITIAN******************************************

    /**
     * 可提现数据接口【完成】
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
            $data = [
                'data' => $incomeData,
                'setting' => ['balance_special' => $this->getBalanceSpecialSet()]
            ];
            return $this->successJson('获取数据成功!', $data);
        }
        return $this->errorJson('未检测到数据!');
    }


    /**
     * 收入提现接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveWithdraw()
    {
        //dd($this->isFreeAudit());
        $withdrawData = \YunShop::request()->data;
        //file_put_contents(storage_path('logs/income.log'),print_r($withdrawData,true));
        if (!$withdrawData) {
            return $this->errorJson('未检测到数据!');
        }

        //附值
        $this->getIncomeSet();
        $this->pay_way = $withdrawData['total']['pay_way'];


        //如果提现到支付宝，验证会员支付宝配置
        if (!$this->getMemberAlipaySet() && $this->pay_way == 'alipay') {
            return $this->errorJson('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }


        //记录提现数据
        Log::info("POST - Withdraw Total ", print_r($withdrawData['total'],true));
        Log::info("POST - Withdraw Data ", print_r($withdrawData,true));



        //提现 item 提现额度验证
        $validator = $this->validatorWithdrawItem($withdrawData['withdrawal']);
        if ($validator !== true) {
            return $this->errorJson($validator);
        }

        Log::info("收入提现:提现数据验证通过");


        DB::beginTransaction();
        $ids = $this->withdrawRecordsSave($withdrawData);
        if (!$ids) {
            DB::rollBack();
            return $this->errorJson('提现失败!');
        }
        DB::commit();
        return $this->successJson('提现成功!');
    }


    /**
     * 是否满足免审核条件，满足返回 true , 不满足返回 false
     * @return bool
     */
    private function isFreeAudit()
    {
        if ($this->freeAuditStatus() && ($this->pay_way == 'balance' || $this->pay_way == 'wechat')) {
            return true;
        }
        return false;
    }


    /**
     * 是否开启免审核【免审核（提现到微信，提现到余额），直接审核】
     * @return bool
     */
    private function freeAuditStatus()
    {
        return Setting::get('withdraw.income.free_audit') ? true : false;
    }


    /**
     * 提现记录保存、数据操作
     * @param array $withdrawData
     * @return mixed
     */
    private function withdrawRecordsSave($withdrawData =[])
    {
        $data =[];
        foreach ($withdrawData['withdrawal'] as $item) {
            $this->item = $item;
            $this->amount = $item['income'];
            $this->key = $item['key_name'];
            $this->getKeySet();


            $data[] = $this->getWithdrawRecordData();
            $this->setIncomeAndOrder($item['type'], $item['type_id']);
        }
        $withdrawData['total']['member_id'] = \YunShop::app()->getMemberId();
        $withdrawData['withdrawal'] = $data;
        event(new AfterIncomeWithdrawEvent($withdrawData));
        Log::info("Withdraw - data", $data);

        //是否免审核
        if ($this->isFreeAudit()) {
            return $this->incomeFreeAudit($data);
        }

        return Withdraw::insert($data);
    }


    private function incomeFreeAudit($incomes = [])
    {
        $freeAudit = new IncomeFreeAuditService();


        foreach ($incomes as $key => $item) {
            $withdrawModel = new Withdraw();

            $withdrawModel->fill($item);
            //直接标为以审核状态
            $withdrawModel->status = 1;
            $withdrawModel->pay_at = time();
            $withdrawModel->save();

            $result = $freeAudit->incomeFreeAudit($withdrawModel,$this->pay_way);

            if (!$result) {
                Log::info('提现失败:' . $item['type_name'] . '免审核失败!');
                return false;
                break;
            }

            unset($withdrawModel);
        }
        return true;
    }


    /**
     * 获取提现数据 data
     * @return array
     */
    private function getWithdrawRecordData()
    {
        return [
            'withdraw_sn'       => Pay::setUniacidNo(\YunShop::app()->uniacid),
            'uniacid'           => \YunShop::app()->uniacid,
            'member_id'         => \YunShop::app()->getMemberId(),
            'type'              => $this->item['type'],
            'type_name'         => $this->item['type_name'],
            'type_id'           => $this->item['type_id'],
            'amounts'           => $this->item['income'],
            'poundage'          => $this->getWithdrawPoundage(),
            'poundage_rate'     => $this->getWithdrawPoundageRate(),
            'actual_poundage'   => $this->getWithdrawPoundage(),    //审核使用
            'actual_amounts'    => $this->getItemWithdrawAmount(),
            'servicetax'        => $this->getWithdrawServiceTax(),
            'servicetax_rate'   => $this->getWithdrawServiceTaxRate(),
            'actual_servicetax' => $this->getWithdrawServiceTax(),  //审核使用
            'pay_way'           => $this->pay_way,
            'manual_type'       => Setting::get('withdraw.income')['manual_type'] ?: 1,
            'status'            => 0,
            'created_at'        => time(),
            'updated_at'        => time(),
        ];
    }


    /**
     * 杨雷 未修改
     * @param $type
     * @param $typeId
     */
    private function setIncomeAndOrder($type, $typeId)
    {
        static::setIncome($type, $typeId);
        //static::setCommissionOrder($type, $typeId);

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
    private function setIncome($type, $typeId)
    {
        Log::info('setIncome');
        if ($this->isFreeAudit()) {
            Income::where('member_id', \YunShop::app()->getMemberId())
                ->whereIn('id', explode(',', $typeId))
                ->update(['status' => 1,'pay_status'=> 2]);
        } else {
            Income::where('member_id', \YunShop::app()->getMemberId())
                ->whereIn('id', explode(',', $typeId))
                ->update(['status' => 1,'pay_status'=> 0]);
        }
    }


    /**
     * 提现 item 提现额度验证
     * @param array $withdrawData
     * @return bool|string
     */
    private function validatorWithdrawItem($withdrawData =[])
    {
        $amount = 0;
        foreach ($withdrawData as $item) {

            $this->key = $item['key_name'];
            $this->item = $item;
            $this->getKeySet();

            $this->amount = $this->getIncomeModel()->whereIn('id', explode(',', $item['type_id']))->sum('amount');
            if (bccomp($this->amount, $this->item['income'], 2) != 0) {
                return '提现失败:' . $this->item['type_name'] . '提现金额错误!';
            }
            if (bccomp($this->amount, $this->getItemAmountFetter(), 2) == -1) {
                return '提现失败:' . $this->item['type_name'] . '未达到提现标准!';
            }
            $amount += $this->getItemWithdrawAmount();
        }
        $this->amount = $amount;
        return true;
    }


    /**
     * 实际提现劳务税
     * @return string
     */
    private function getWithdrawServiceTax()
    {
        return $this->isUseBalanceSpecialSet() ? $this->getBalanceSpecialServiceTax() : $this->getItemServiceTax();
    }

    /**
     * 实际提现手续费
     * @return string
     */
    private function getWithdrawPoundage()
    {
        return $this->isUseBalanceSpecialSet() ? $this->getBalanceSpecialPoundage() : $this->getItemPoundage();
    }

    /**
     * 实际提现劳务税比例
     * @return string
     */
    private function getWithdrawServiceTaxRate()
    {
        return $this->isUseBalanceSpecialSet() ? $this->getBalanceSpecialServiceTaxRate() : $this->getItemServiceRate();
    }

    /**
     * 实际提现手续费比例
     * @return string
     */
    private function getWithdrawPoundageRate()
    {
        return $this->isUseBalanceSpecialSet() ? $this->getBalanceSpecialPoundageRate() : $this->getItemPoundageRate();
    }

    private function isUseBalanceSpecialSet()
    {
        if ($this->pay_way == 'balance' && $this->getBalanceSpecialSet()) {
            return true;
        }
        return false;
    }


    /**
     * 提现数据提交，最终 item 可提现金额
     * @return int
     */
    private function getItemWithdrawAmount()
    {
        //如果提现到余额 && 提现到余额开启独立设置
        if ($this->pay_way == 'balance' && $this->getBalanceSpecialSet()) {
            $amount = bcsub($this->amount,$this->getBalanceSpecialPoundage(),2);
            return bcsub($amount,$this->getBalanceSpecialServiceTax(),2);
        }
        $amount = bcsub($this->amount,$this->getItemPoundage(),2);
        return bcsub($amount,$this->getItemServiceTax(),2);
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
            'type_name'         => $this->getLangTitle($this->key) ? $this->getLangTitle($this->key) : $this->item['title'],
            'income'            => $this->amount,
            'poundage'          => $this->getItemPoundage(),
            'poundage_rate'     => $this->getItemPoundageRate(),
            'servicetax'        => $this->getItemServiceTax(),
            'servicetax_rate'   => $this->getItemServiceRate(),
            'roll_out_limit'    => $this->getItemAmountFetter(),
            'can'               => $this->itemIsCanWithdraw(),
            'selected'          => $this->itemIsCanWithdraw(),
            'type_id'           => $this->getItemTypeIds(),
            'special_poundage'  => $this->getBalanceSpecialPoundage(),
            'special_poundage_rate'  => $this->getBalanceSpecialPoundageRate(),
            'special_service_tax'    => $this->getBalanceSpecialServiceTax(),
            'special_service_tax_rate' => $this->getBalanceSpecialServiceTaxRate(),
        ];
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
     * 提现到余额独立劳务税
     * @return string
     */
    private function getBalanceSpecialServiceTax()
    {
        return $this->poundageMath(($this->amount - $this->getBalanceSpecialPoundage()),$this->getBalanceSpecialServiceTaxRate());
    }


    /**
     * 提现到余额独立手续费
     * @return string
     */
    private function getBalanceSpecialPoundage()
    {
        return $this->poundageMath($this->amount,$this->getBalanceSpecialPoundageRate());
    }



    /**
     * 提现到余额独立劳务税比例
     * @return string
     */
    private function getBalanceSpecialPoundageRate()
    {
        return $this->getBalanceSpecialSet() ? $this->judgmentValue($this->income_set['special_poundage']) : '0';
    }


    /**
     * 提现到余额独立手续费比例
     * @return string
     */
    private function getBalanceSpecialServiceTaxRate()
    {
        return $this->getBalanceSpecialSet() ? $this->judgmentValue($this->income_set['special_service_tax']) : '0';
    }


    /**
     * 是否开启提现到余额独立手续费、劳务税
     * @return bool
     */
    private function getBalanceSpecialSet()
    {
        return $this->income_set['balance_special'] ? true : false;
    }


    /**
     * 对应 key (提现挂件) 是否可以提现
     * @return bool
     */
    private function itemIsCanWithdraw()
    {
        if (bccomp($this->amount,$this->getItemAmountFetter(),2) == -1 || bccomp($this->amount,0,2) != 1) {
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
     * 获取 item 对应手续费
     * @return string
     */
    private function getItemPoundage()
    {
        return $this->poundageMath($this->amount,$this->getItemPoundageRate());
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
     * 获取 item 对应劳务税比例
     * @return string
     */
    private function getItemServiceRate()
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
     * 获取 key (提现挂件) 可提现金额
     * @return mixed
     */
    private function getIncomeModelSum()
    {
        return $this->amount = $this->getIncomeModel()->where('incometable_type', $this->item['class'])->sum('amount');
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
