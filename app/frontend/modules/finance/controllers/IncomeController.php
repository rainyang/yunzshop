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
    public function getIncomeCount()
    {
        
        $incomeModel = Income::getIncomes()->get();
        $config = \Config::get('income');
        $incomeData['total'] = [
            'title' => '推广收入',
            'type' => 'total',
            'type_name' => '推广佣金',
            'income' => $incomeModel->sum('amount')
        ];
        foreach ($config as $key => $item) {

            $incomeData[$key] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['type_name'],
                'income' => $incomeModel->where('type',$key)->sum('amount')
            ];
        }
        if($incomeData){
            return $this->successJson('获取数据成功!', $incomeData);
        }
        return $this->errorJson('未检测到数据!');
    }

    public function getIncomeList()
    {
        $incomeModel = Income::getIncomeInMonth()->get();
        if($incomeModel){
            return $this->successJson('获取数据成功!', $incomeModel);
        }
        return $this->errorJson('未检测到数据!');
    }

    public function getDetail()
    {
        $id = \YunShop::request()->id;
        $detailModel = Income::getDetailById($id);
        if($detailModel){
            return '{"result":1,"msg":"成功","data":'.$detailModel->first()->detail.'}';
        }
        return $this->errorJson('未检测到数据!');
    }
}