<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/30
 * Time: 下午3:56
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class BalanceController extends BaseController
{
    //余额基础设置页面
    public function index()
    {
        $balance = Setting::get('balance.recharge');

        $requestModel = \YunShop::request()->balance;
        if ($requestModel) {
            //dd($requestModel);
            $requestModel[''] = '';
            if (Setting::set('balance.recharge', $requestModel)) {
                return $this->message('余额基础设置保存成功', Url::absoluteWeb('finance.balance.index'));
            } else {
                $this->error('余额基础设置保存失败！！');
            }
        }

        return view('finance.balance.index', [
            'balance' => $balance,
            'pager' => ''
        ])->render();
    }

    //用户余额管理
    public function member()
    {
        $pageSize = 5;
        $memberList = Member::getMembers()->paginate($pageSize);
        $pager = PaginationHelper::show($memberList->total(), $memberList->currentPage(), $memberList->perPage());

        //todo 搜索，会员组，会员等级显示

        return view('finance.balance.member', [
            'memberList'    => $memberList,
            'pager'         => $pager,
            'memberLevel'   => '',
            'memberGroup'   => ''
        ])->render(
    }

    //
    public function recharge()
    {

    }


}
