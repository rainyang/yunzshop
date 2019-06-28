<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-19
 * Time: 16:56
 */

namespace app\backend\modules\excelRecharge\controllers;


use app\common\components\BaseController;

class ConfirmController extends BaseController
{
    public function index()
    {

    }

    /**
     * 批量充值类型
     *
     * @return string
     */
    private function batchType()
    {
        return request()->batch_type;
    }

    /**
     * 批量充值Excel文件
     *
     * @return array|\Illuminate\Http\UploadedFile|null
     */
    private function excelFile()
    {
        return request()->file('batch_recharge');
    }
}
