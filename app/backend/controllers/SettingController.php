<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;

class SettingController extends BaseController
{

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid;
    }

    public function index()
    {
        $shop = Setting::get('shop.shop');
        $requestModel = \YunShop::request()->shop;
        if ($requestModel) {
            Setting::set('shop.shop',$requestModel);
            $shop = Setting::get('shop.shop');
        }
        //Setting::set('shop.sms',['type'=>'']);

        return view('setting.shop',[
            'set' => $shop
        ])->render();
    }
}