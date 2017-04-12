<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
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

                    return json(trans('admin.plugins.operations.enabled', ['plugin' => $plugin->title]), 0);

                case 'disable':
                    $plugins->disable($name);

                    return json(trans('admin.plugins.operations.disabled', ['plugin' => $plugin->title]), 0);

                case 'delete':
                    $plugins->uninstall($name);

                    return json(trans('admin.plugins.operations.deleted'), 0);

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


//        $pluginsData = Datatables::of($installed)
//            ->setRowId('plugin-{{ $name }}')
//            ->editColumn('title', function ($plugin) {
//                return trans($plugin->title);
//            })
//            ->editColumn('description', function ($plugin) {
//                return trans($plugin->description);
//            })
//            ->editColumn('author', function ($plugin) {
//                return "<a href='{$plugin->url}' target='_blank'>".trans($plugin->author)."</a>";
//            })
//            ->addColumn('status', function ($plugin) {
//                return trans('admin.plugins.status.'.($plugin->isEnabled() ? 'enabled' : 'disabled'));
//            })
//            ->addColumn('operations', function ($plugin) {
//                dd($plugin);
//                //return view('vendor.admin-operations.plugins.operations', compact('plugin'))->render();
//            })
//            ->make(true);

        return view('admin.plugins',[
            'installed' => $installed
        ]);
    }

}