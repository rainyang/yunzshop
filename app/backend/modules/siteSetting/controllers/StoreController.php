<?php

namespace app\backend\modules\siteSetting\controllers;

use app\common\components\BaseController;
use app\common\facades\SiteSetting;
use app\common\helpers\Url;

class StoreController extends BaseController
{
    public function index()
    {
        $setting = request()->input('setting');

        SiteSetting::set('base', $setting);

        return $this->successJson("设置保存成功", Url::absoluteWeb('siteSetting.index.index'));
    }
}