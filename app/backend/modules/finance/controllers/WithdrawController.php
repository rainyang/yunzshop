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
            return $this->message('数据不存在或已被删除!','',error);
        }

        dd($withdrawModel->toArray());
        return view('finance.withdraw.withdraw-info', [
            'item' => $withdrawModel,
            'set' => $set,
        ])->render();
    }

}