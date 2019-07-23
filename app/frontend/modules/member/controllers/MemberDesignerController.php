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
use Yunshop\Designer\services\DesignerService;

class MemberDesignerController extends ApiController
{
     public function index()
     {
         $res = [];
         $res['status'] = false;
         $res['data'] = [];
         if (app('plugins')->isEnabled('designer')) {
            $designer =  $this->getDesigner();
            if($designer->datas)
            {
                $datas = (new DesignerService())->getMemberData($designer->datas);

                $memberData = $this->getMemberData();
                foreach ($datas as $dkey=>$design)
                {
                    if($design['temp'] == 'membercenter')
                    {
                       if($design['params']['memberredlove'] == true || $design['params']['memberwhitelove'] == true){
                           if(!app('plugins')->isEnabled('love')){
                               $datas[$dkey]['params']['memberredlove'] = false;
                               $datas[$dkey]['params']['memberwhitelove'] = false;
                           }
                       }
                    }
                    if($design['temp'] == 'membertool')
                    {
                         foreach ($design['data']['part'] as $pkey=>$par)
                         {
                             if(!in_array($par['name'],$memberData['tools'])){
                                 unset($datas[$dkey]['data']['part'][$pkey]);
                             }
                         }
                    }
                    if($design['temp'] == 'membermerchant')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(!in_array($par['name'],$memberData['merchants'])){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                    }
                    if($design['temp'] == 'membermarket')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(!in_array($par['name'],$memberData['markets'])){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                    }
                    if($design['temp'] == 'memberasset')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(!in_array($par['name'],$memberData['assets'])){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                    }
                    if($design['temp'] == 'membercarorder')
                    {
                        if (!app('plugins')->isEnabled('net-car')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'memberhotelorder')
                    {
                        if (!app('plugins')->isEnabled('hotel')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'memberleaseorder')
                    {
                        if (!app('plugins')->isEnabled('lease-toy')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'membergoruporder')
                    {
                        if (!app('plugins')->isEnabled('fight-groups')) {
                            unset($datas[$dkey]);
                        }
                    }
                }
                $res['data'] = $datas;
                $res['status'] = true;
            }
         }

         return $this->successJson('成功', $res);
     }

    /**
     * 获取可用模板
     */
     private function getDesigner()
     {
         $designer =  MemberDesigner::uniacid()
             ->whereRaw('FIND_IN_SET(?,page_type)', [\Yunshop::request()->type])
             ->where(['shop_page_type'=>MemberDesigner::PAGE_MEMBER_CENTER,'is_default'=>1])
             ->first();
         return $designer;
     }

    /**
     * @return array
     * 获取可用插件按钮
     */
     private function getMemberData()
     {
         $arr = (new \app\common\services\member\MemberCenterService())->getMemberData();
         $tools = ['m-collection','m-footprint','m-address','m-info'];
         $merchants = [];
         $markets = ['m-erweima','m-pinglun','m-guanxi','m-coupon'];
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