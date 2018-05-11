<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
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

    public function getPluginData()
    {
        $plugins = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());
        $installed = $plugins->getPlugins();
        
        return view('admin.plugins',[
            'installed' => $installed
        ]);
    }

    public function getPluginList()
    {
        $dividend['name'] = '分润类';
        $industry['name'] = '行业类';
        $marketing['name'] = '营销类';
        $tool['name'] = '工具类';
        $recharge['name'] = '生活充值';
        $api['name'] = '接口类';

        $pluginsModel = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());
        $plugins = Config::get('plugins_menu');
        foreach ($plugins as $key => $plugin) {
            $type = $plugin['type'];
            switch ($type) {
                case 'dividend':
                    $dividend[$key] = $plugin;
                    $dividend[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
                case 'industry':
                    $industry[$key] = $plugin;
                    $industry[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
                case 'marketing':
                    $marketing[$key] = $plugin;
                    $marketing[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
                case 'tool':
                    $tool[$key] = $plugin;
                    $tool[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
                case 'recharge':
                    $recharge[$key] = $plugin;
                    $recharge[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
                case 'api':
                    $api[$key] = $plugin;
                    $api[$key]['description'] = $pluginsModel->getPlugin($key)->description;
                break;
            }
        }
        //dd($plugins);exit;
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

}