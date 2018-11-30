<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2018/11/28
 * Time: 13:56
 */

namespace app\backend\modules\member\controllers;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\services\popularize\PopularizePageShowFactory;

class PopularizePageShowController extends BaseController
{
    //微信
    public function wechatSet()
    {
        $info = Setting::get("popularize.wechat");
        if (\Request::isMethod('post')) {
            $set = request()->input('set');
            if (Setting::set("popularize.wechat", $set)) {
                $this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.wechat-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }
        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //微信小程序
    public function miniSet()
    {
        $info = Setting::get("popularize.mini");


        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.mini", $set)) {
                $this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.mini-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //手机浏览器 pc
    public function wapSet()
    {
        $info = Setting::get("popularize.wap");

        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.wap", $set)) {
                $this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.wap-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //app
    public function appSet()
    {
        $info = Setting::get("popularize.app");

        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.app", $set)) {
                $this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.app-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //支付宝
    public function alipaySet()
    {
        $info = Setting::get("popularize.alipay");


        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.alipay", $set)) {
                $this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.alipay-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    /**
     * 获取商城开启的插件
     * @return array 开启的插件页面路由与名称
     */
    protected function getData()
    {
        $lang_set = $this->getLangSet();

        $config = $this->getIncomePageConfig();

        $plugins = $this->getPlugins();

        foreach ($config as $key => $item) {

            $incomeFactory = new PopularizePageShowFactory(new $item['class'], $lang_set);


            if ($plugins[$incomeFactory->getMark()]) {
                $array[] = [
                    'url' => $plugins[$incomeFactory->getMark()],
                    'title' => $incomeFactory->getTitle(),
                    'mark'  => $incomeFactory->getMark(),
                    'status' => 1,
                ];
            } else {
                $array[] = [
                    'url' => $incomeFactory->getAppUrl(),
                    'title' => $incomeFactory->getTitle(),
                    'mark'  => $incomeFactory->getMark(),
                    'status' => 0,
                ];
            }


        }

        return $array;
    }

    protected function getPlugins()
    {
        return [
            'area_dividend' => ['regionalAgencyCenter','applyRegionalAgency'],
        ];
    }

    /**
     * 生成js文件给前端用
     */
    protected function toJson()
    {
        $all_set =  Setting::getByGroup("popularize");

        $string = json_encode($all_set);

        return $string;
    }

    /**
     * 收入页面配置 config
     *
     * @return mixed
     */
    private function getIncomePageConfig()
    {
        return \Config::get('income_page');
    }


    /**
     * 获取商城中的插件名称自定义设置
     *
     * @return mixed
     */
    private function getLangSet()
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);

        return $lang[$lang['lang']];
    }

}