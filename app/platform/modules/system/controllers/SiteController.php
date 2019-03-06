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
        $copyright = SystemSetting::settingLoad('copyright', 'system_copyright');

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

        return \Response::json([
            'result' => 1,
            'msg' => '成功',
            'data' => $copyright
        ]);

//        return view('system.site', [
//            'setdata' =>
//        ]);

        /* 站点设置字段名 */
        // 是否关闭站点 status
        // 站点名称 name
        // 平台logo site_logo
        // 浏览器标题图标 title_icon
        // 登录页广告 advertisement
        // 底部信息 information

       /* $set = \Setting::get('system.site');
        $set_data = request()->setdata;
        if ($set_data) {
            if (!\Setting::set('system.site', $set_data)) {
                echo $this->errorJson('失败', '', 'error'); exit;
            }
            echo $this->successJson('成功', ''); exit;
        }
        echo $this->successJson('获取数据成功', $set); exit;*/
    }
}