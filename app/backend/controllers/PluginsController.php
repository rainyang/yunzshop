<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\exceptions\AdminException;
use app\common\helpers\Url;
use app\common\repositories\OptionRepository;
use Datatables;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use app\common\services\PluginManager;
use Illuminate\Support\Facades\Config;

class PluginsController extends BaseController
{
    public function showManage()
    {
        return view('public.admin.plugins');
    }

    public function config($name, Request $request)
    {
        $plugin = plugin($name);

        if ($plugin && $plugin->isEnabled() && $plugin->hasConfigView()) {
            return $plugin->getConfigView();
        } else {
            abort(404, trans('admin.plugins.operations.no-config-notice'));
        }
    }

    public function manage()
    {
        $plugins = app('app\common\services\PluginManager');
        $plugin = plugin($name = \YunShop::request()->name);
        if ($plugin) {
            // pass the plugin title through the translator
            $plugin->title = trans($plugin->title);

            switch (\YunShop::request()->action) {
                case 'enable':
                    $plugins->enable($name);
                    return $this->message('启用成功!', Url::absoluteWeb('plugins.get-plugin-data'));
//                    return json(trans('admin.plugins.operations.enabled', ['plugin' => $plugin->title]), 0);

                case 'disable':
                    $plugins->disable($name);
                    return $this->message('禁用成功!', Url::absoluteWeb('plugins.get-plugin-data'));
//                    return json(trans('admin.plugins.operations.disabled', ['plugin' => $plugin->title]), 0);

                case 'delete':
                    $plugins->uninstall($name);
                    return $this->message('删除成功!', Url::absoluteWeb('plugins.get-plugin-data'));
//                    return json(trans('admin.plugins.operations.deleted'), 0);

                default:
                    # code...
                    break;
            }
        }
    }

    public function batchMange() {
        $plugins = app('app\common\services\PluginManager');
        $names =  explode(',',\YunShop::request()->names);
        foreach ($names as $name) {
            $plugin = plugin($name);
            if ($plugin) {
                $plugin->title = trans($plugin->title);
                switch (\YunShop::request()->action) {
                    case 'enable':
                        $plugins->enable($name);
                        break;
                    case 'disable':
                        $plugins->disable($name);
                        break;
                    default:
                        die(json_encode(array(
                            "result" => 0,
                            "error" => "操作错误"
                        )));
                        break;
                }
            }
        }
    }

    public function getPluginData()
    {
        $installed = app('plugins')->getPlugins();
        return view('admin.plugins',[
            'installed' => $installed
        ]);
    }

    public function getPluginList()
    {

//        $dividend['name'] = '分润类';
//        $industry['name'] = '行业类';
//        $marketing['name'] = '营销类';
//        $tool['name'] = '工具类';
//        $recharge['name'] = '生活充值';
//        $api['name'] = '接口类';

        $plugins = Config::get('plugins_menu');//全部插件
        foreach ($plugins as $key => $plugin) {
            $type = $plugin['type'];
            switch ($type) {
                case 'dividend'://分润类
                    $dividend[$key] = $plugin;
                    $dividend[$key]['description'] = app('plugins')->getPlugin($key)->description;
                break;
                case 'industry'://行业类
                    $industry[$key] = $plugin;
                    $industry[$key]['description'] = app('plugins')->getPlugin($key)->description;
                break;
                case 'marketing'://营销类
                    $marketing[$key] = $plugin;
                    $marketing[$key]['description'] = app('plugins')->getPlugin($key)->description;
                break;
                case 'tool'://工具类
                    $tool[$key] = $plugin;
                    $tool[$key]['description'] = app('plugins')->getPlugin($key)->description;
                break;
                case 'recharge'://生活充值类
                    $recharge[$key] = $plugin;
                    $recharge[$key]['description'] = app('plugins')->getPlugin($key)->description;
                break;
                case 'api'://接口类
                    $api[$key] = $plugin;
//                    if (!$pluginsModel->getPlugin($key)) {
//                        $api[$key]['description'] = $pluginsModel->getPlugin(str_replace("_","-",$key));
//                    } else {
//                        $api[$key]['description'] = $pluginsModel->getPlugin($key);
//                    }
//                    $api[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
            }
        }

        return view('admin.pluginslist',[
            'plugins' => $plugins,
            'dividend' => $dividend,
            'industry' => $industry,
            'marketing' => $marketing,
            'tool' => $tool,
            'recharge' => $recharge,
            'api' => $api,
        ]);
    }

    public function setTopShow() {
        $data = request()->input();
        $data['action'] ?  : app('plugins')->enTopShow($data['name'],1);
        if ($data['action']) {
            app('plugins')->enTopShow($data['name'],0);
            return $this->message('取消顶部栏成功',Url::absoluteWeb('plugins.getPluginList'));
        } else {
            app('plugins')->enTopShow($data['name'],1);
            return $this->message('添加顶部栏成功',Url::absoluteWeb('plugins.getPluginList'));
        }
    }

}