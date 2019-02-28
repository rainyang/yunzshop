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
use app\common\helpers\Url;

class SiteController extends BaseController
{
    public function index()
    {
        $site = SystemSetting::where('key', 'site')->pluck('value');
        $set_data = request()->setdata;
        if ($set_data) {
            $set_data = \GuzzleHttp\json_encode($set_data);
            if ($site->isEmpty()) {
                // 添加
                $system_setting = SystemSetting::create([
                    'key'       => 'site',
                    'value'     => $set_data
                ]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/site', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/site', '失败', 'failed');
                }
            } else {
                // 修改
                $system_setting = SystemSetting::where('key', 'site')->update(['value' => $set_data]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/site', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/site', '失败', 'failed');
                }
            }
        }

//        dd(json_decode($site['0']));

        return view('system.site', [
            'setdata' => json_decode($site['0'])
        ]);

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