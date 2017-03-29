<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/27
 * Time: 下午10:15
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\BaseController;
use app\common\models\Income;

class IncomeController extends BaseController
{
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
        $config = \Config::get('income');
        $incomeData['total'] = [
            'title' => '推广收入',
            'type' => 'total',
            'type_name' => '推广佣金',
            'income' => $incomeModel->sum('amount')
        ];

        foreach ($config as $key => $item) {
            $typeModel = $incomeModel->where('type', $key);
            $incomeData[$key] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['type_name'],
                'income' => $typeModel->sum('amount')
            ];
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
        $incomeModel = Income::getIncomeInMonth()->where('member_id', \YunShop::app()->getMemberId())->get();
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
        $id = \YunShop::request()->id;
        $detailModel = Income::getDetailById($id);
        if ($detailModel) {
            return '{"result":1,"msg":"成功","data":' . $detailModel->first()->detail . '}';
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
            $searchType[$key]['title'] = $config['type_name'];
            $searchType[$key]['type'] = $config['type'];
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
        $set = \Setting::get('income.withdraw.commission', ['roll_out_limit' => '100', 'poundage' => '5']);
        $config = \Config::get('income');

        $incomeModel = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId());
        $incomeModel = $incomeModel->where('status', '0');

        foreach ($config as $key => $item) {
            $incomeModel = $incomeModel->where('type', $key);
            $amount = $incomeModel->sum('amount');


            if(bccomp(100,$set['roll_out_limit'],2) != -1){
                $type_id = '';
                foreach ($incomeModel->get() as $ids) {
                    $type_id .= $ids->type_id.",";
                }
                $incomeData[$key] = [
                    'type' => $item['type'],
                    'type_name' => $item['type_name'],
                    'type_id' => $type_id,
                    'income' => $incomeModel->sum('amount')
                ];
            }else{
                $incomeData[$key] = [
                    'type' => $item['type'],
                    'type_name' => $item['type_name'],
                    'type_id' => '',
                    'income' => $incomeModel->sum('amount')
                ];
            }
        }
        if ($incomeData) {
            $incomeData['set'] = $set;
            return $this->successJson('获取数据成功!', $incomeData);
        }
        return $this->errorJson('未检测到数据!');
    }



}