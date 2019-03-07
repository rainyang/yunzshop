<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/27
 * Time: 10:53
 */
namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;

class SiteController extends BaseController
{
    public function index()
    {
        $set_data = request()->setdata;
        $copyright = SystemSetting::settingLoad('kk', 'kk');

        if ($set_data) {
            $site = SystemSetting::settingSave($set_data, 'copyright', 'system_copyright');
            if ($site) {
                return \Response::json([
                    'result' => 1,
                    'msg' => '成功',
                    'data' => ''
                ]);
            } else {
                return \Response::json([
                    'result' => 0,
                    'msg' => '失败',
                    'data' => ''
                ]);
            }
        }

        if ($copyright) {
            return \Response::json([
                'result' => 1,
                'msg' => '成功',
                'data' => $copyright
            ]);
        } else {
            return \Response::json([
                'result' => 0,
                'msg' => '失败',
                'data' => ''
            ]);
        }
    }
}