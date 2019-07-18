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
            $designer =  MemberDesigner::uniacid()->where('id',20)->first();
            if($designer->datas)
            {
                $datas = json_decode($designer->datas);
                $memberData = $this->getMemberData();
                foreach ($datas as $dkey=>$design)
                {
                    if($design->temp == 'membertool')
                    {
                         foreach ($design->data->part as $pkey=>$par)
                         {
                             if(!in_array($par->name,$memberData['tools'])){
                                 unset($datas[$dkey]->data->part[$pkey]);
                             }
                         }
                    }
                    if($design->temp == 'membermerchant')
                    {
                        foreach ($design->data->part as $pkey=>$par)
                        {
                            if(!in_array($par->name,$memberData['merchants'])){
                                unset($datas[$dkey]->data->part[$pkey]);
                            }
                        }
                    }
                    if($design->temp == 'membermarket')
                    {
                        foreach ($design->data->part as $pkey=>$par)
                        {
                            if(!in_array($par->name,$memberData['markets'])){
                                unset($datas[$dkey]->data->part[$pkey]);
                            }
                        }
                    }
                    if($design->temp == 'memberasset')
                    {
                        foreach ($design->data->part as $pkey=>$par)
                        {
                            if(!in_array($par->name,$memberData['assets'])){
                                unset($datas[$dkey]->data->part[$pkey]);
                            }
                        }
                    }
                }
                $res['data'] = $datas;
                $res['status'] = true;
            }
         }

         return $this->successJson('成功', $res);
     }

     private function getMemberData()
     {
         $arr = (new \app\common\services\member\MemberDesigner())->getMemberData();
         $tools = [];
         $merchants = [];
         $markets = [];
         $assets = [];
         foreach ($arr['tool'] as $v){
            $tools[] = $v['name'];
         }
         foreach ($arr['merchant'] as $v){
             $merchants[] = $v['name'];
         }
         foreach ($arr['market'] as $v){
             $markets[] = $v['name'];
         }
         foreach ($arr['asset_equity'] as $v){
             $assets[] = $v['name'];
         }

         return [
             'tools'=>$tools,
             'merchants'=>$merchants,
             'markets'=>$markets,
             'assets'=>$assets,
         ];
     }
}