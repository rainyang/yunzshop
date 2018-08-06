<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 上午11:14
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\common\facades\Setting;

class DetailController extends PreController
{
    /**
     * 提现记录详情 接口
     *
     * @return string
     */
    public function index()
    {
        return view('withdraw.detail', [
            'item'  => $this->withdrawModel,
            'set'   => Setting::get('plugin.commission'),
        ])->render();
    }


    public function validatorWithdrawModel($withdrawModel){}


}
