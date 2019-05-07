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
        $copyright['name'] ? : $copyright['name'] = "芸众商城管理系统";
        $copyright['site_logo'] ? : $copyright['site_logo'] = yz_tomedia("/static/images/site_logo.png");
        $copyright['title_icon'] ? : $copyright['title_icon'] =yz_tomedia("/static/images/title_icon.png");
        $copyright['advertisement'] ? : $copyright['advertisement'] = yz_tomedia("/static/images/advertisement.jpg");
        $copyright['information'] ? : $copyright['information'] = '<p>&copy; 2019&nbsp;<a href=\"https://www.yunzshop.com/\" target=\"_blank\" rel=\"noopener\">Yunzhong.</a>&nbsp;All Rights Reserved. 广州市芸众信息科技有限公司&nbsp;&nbsp;<a href=\"http://www.miitbeian.gov.cn/\" target=\"_blank\" rel=\"noopener\">&nbsp;粤ICP备17018310号-1</a>&nbsp;Powered by Yunzhong&nbsp;</p> <p><a href=\"https://www.yunzshop.com/\" target=\"_blank\" rel=\"noopener\">系统使用教程：www.yunzshop.com</a>&nbsp; &nbsp;&nbsp;<a href=\"https://www.yunzshop.com/plugin.php?id=it618_video:index\" target=\"_blank\" rel=\"noopener\">视频教程</a></p>';

        if ($set_data) {
            $site = SystemSetting::settingSave($set_data, 'copyright', 'system_copyright');
            if ($set_data['title_icon']) {
                $title_icon = file_get_contents($set_data['title_icon']);
                file_put_contents(base_path().'/favicon.ico', $title_icon);
            }
            if ($site) {
                return $this->successJson('成功', '');
            } else {
                return $this->errorJson('失败', '');
            }
        }

        if ($copyright) {
            return $this->successJson('成功', $copyright);
        } else {
            return $this->errorJson('没有检测到数据', '');
        }
    }
}