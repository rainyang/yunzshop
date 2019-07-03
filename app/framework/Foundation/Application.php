<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/6
 * Time: 下午3:30
 */

namespace app\framework\Foundation;

use app\framework\Routing\RoutingServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;

class Application extends \Illuminate\Foundation\Application
{
    public $makingRouteList;

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * @param null $file
     * @return string
     */
    public function getRoutesPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . $file;
    }

    public function getRoutesDataPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'data' . $file;
    }

    public function getUrlRoutesPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'urlRoutes' . $file;
    }

    public function getFrontendPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'frontend';
    }

    public function getBackendPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'backend';
    }

    public function getPluginsPath()
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'plugins';
    }

    public function getPaymentPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'payment';
    }

    public function makingRouteList()
    {
        // todo 暂时解决
        return (bool)$this->makingRouteList;

    }
}