<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 上午11:28
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Income;

class WithdrawController extends BaseController
{
    public function set()
    {
        $set = Setting::get('withdraw.balance');
        $requestModel = \YunShop::request()->withdraw;
        if ($requestModel) {
            foreach ($requestModel as $key => $item) {
                Setting::set('withdraw.' . $key, $item);
            }
            return $this->message('设置保存成功', Url::absoluteWeb('finance.withdraw.set'));
        }
        return view('finance.withdraw.withdraw-set', [
            'set' => $set
        ])->render();
    }

    public function index()
    {

        $pageSize = 20;

        $search = \YunShop::request()->search;

        $list = Withdraw::getWithdrawList($search)->paginate($pageSize);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('finance.withdraw.withdraw-list', [
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }


    public function info()
    {
        $set = Setting::get('plugin.commission');
        $id = intval(\YunShop::request()->id);
        $withdrawModel = Withdraw::getWithdrawById($id)->first();
        if (!$withdrawModel) {
            return $this->message('数据不存在或已被删除!', '', error);
        }
        $withdrawModel = $withdrawModel->toArray();


//        dd($withdrawModel);
        return view('finance.withdraw.withdraw-info', [
            'item' => $withdrawModel,
            'set' => $set,
        ])->render();
    }

    public function dealt()
    {
        $requestData = \YunShop::request();

        if (isset($requestData['submit_check'])) {
            //提交审核
            $request = $this->submitCheck($requestData['id'], $requestData['audit']);

            return $this->message($request['msg'],yzWebUrl("finance.withdraw.info",['id'=>$requestData['id']]));

        } elseif (isset($requestData['submit_pay'])) {
            //打款
            $request = $this->submitPay();
        } elseif (isset($requestData['submit_cancel'])) {
            //重新审核
            $request = $this->submitCancel();
        }

    }

    public function submitCheck($withdrawId, $incomeData)
    {
        $withdraw = Withdraw::getWithdrawById($withdrawId)->first();
        if($withdraw->status !== '0'){
            return ['msg'=>'审核失败,数据不符合提现规则!'];
        }
        $withdrawStatus = "-1";
        foreach ($incomeData as $key => $income) {
            if($income){
                $withdrawStatus = "1";
                Income::updatedIncomePayStatus($key,'1');
            }else{
                Income::updatedIncomePayStatus($key,'-1');
            }
        }
        $request = Withdraw::updatedWithdrawStatus($withdrawId,$withdrawStatus);
        if($request){
            return ['msg'=>'审核成功!'];
        }
        return ['msg'=>'审核失败!'];
    }

    public static function submitPay()
    {

    }

    public static function submitCancel()
    {

    }
}