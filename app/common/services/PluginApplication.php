<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/27
 * Time: 3:23 PM
 */

namespace app\common\services;


use Illuminate\Container\Container;

class PluginApplication extends Container
{
    /**
     * @var Plugin
     */
    private $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function init()
    {
    }
}