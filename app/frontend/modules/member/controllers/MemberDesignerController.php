<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/16
 * Time: 14:53
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\ApiController;
use Yunshop\Designer\models\MemberDesigner;

class MemberDesignerController extends ApiController
{
     public function index()
     {
         $res = [];
         $res['status'] = false;
         $res['data'] = [];
         if (app('plugins')->isEnabled('designer')) {
            $designer =  MemberDesigner::uniacid()->where('id',1)->first();
            if($designer->datas)
            {
                $res['data'] = json_decode($designer->datas);
                $res['status'] = true;
            }
         }

         return $this->successJson('成功', $res);
     }
}