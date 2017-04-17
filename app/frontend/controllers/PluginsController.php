<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\frontend\controllers;


use app\common\components\BaseController;
use app\common\repositories\OptionRepository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use app\common\services\PluginManager;

class PluginsController extends BaseController
{

    public function getPluginData()
    {
        $plugins = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());
        $enableds = $plugins->getEnabledPlugins()->toArray();

        foreach ($enableds as &$enabled) {
            unset($enabled['path']);
        }

        if($enableds){
            return $this->successJson('获取数据成功!', $enableds);
        }
        return $this->errorJson('未检测到数据!');
    }

}