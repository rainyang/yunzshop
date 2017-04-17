<?php
/**
 * Created by PhpStorm.
 * User: jan
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
        $plugins = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());
        $plugin = plugin($name = \YunShop::request()->name);
        if ($plugin) {
            // pass the plugin title through the translator
            $plugin->title = trans($plugin->title);

            switch (\YunShop::request()->action) {
                case 'enable':
                    $plugins->enable($name);
                    return $this->message('启用成功!', Url::absoluteWeb('plugins.get-plugin-data'));

                case 'disable':
                    $plugins->disable($name);
                    return $this->message('禁用成功!', Url::absoluteWeb('plugins.get-plugin-data'));

                case 'delete':
                    $plugins->uninstall($name);
                    return $this->message('删除成功!', Url::absoluteWeb('plugins.get-plugin-data'));

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

}