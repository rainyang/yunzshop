<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/10
 * Time: 下午5:07
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;

class ShareController  extends BaseController
{
    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid;
    }

    /**
     * 商城设置
     * @return mixed
     */
    public function index()
    {
        $share = Setting::get('share');
        $requestModel = \YunShop::request()->shop;
        if ($requestModel) {
            if (Setting::set('shop.shop', $requestModel)) {
                return $this->message('商城设置成功', Url::absoluteWeb('setting.shop.index'));
            } else {
                $this->error('商城设置失败');
            }
        }

        return view('setting.shop.shop', [
            'set' => $shop
        ])->render();
    }
}