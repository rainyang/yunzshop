<?php

namespace app\common\components;

use app\common\helpers\Url;

/**
 * controller基类
 *
 * User: jan
 * Date: 21/02/2017
 * Time: 21:20
 */
class BaseController
{
    //当前模块名数组
    public $modules = [];
    //当前控制器
    public $controller = '';
    //当前action
    public $action = '';

    /**
     * 渲染视图
     *
     * ```php
     * $this->render('index',['list'=>$list]);
     *
     * 模板名可直接写 当前action名，方法会自动补全路径，如果名称里有/则不补全
     *
     * ```
     * @param $filename     模板名
     * @param array $data 模板变量
     * @param bool $return 是否返回模板内容
     * @return mixed
     */
    public function render($filename, $data = [], $return = false)
    {
        if (strpos($filename, '/') === false) {
            $filename = $this->controller . '/' . $filename;
            $this->modules && $filename = implode('/', $this->modules) . '/' . $filename;
        }

        $dataVar = ['var' => objectArray(\YunShop::app()), 'request' => objectArray(\YunShop::request())];
        is_array($data) && $dataVar = array_merge($data, $dataVar);
        extract($dataVar);

        $content = include $this->template($filename, $data);
        if ($return == true) {
            return $content;
        } else {
            echo $content;
        }

    }

    /**
     * 编译并获取模板路径
     * @param $filename
     * @return string
     */
    public function template($filename)
    {
        strpos($filename, 'web/') !== false && $filename = str_replace('web/', '', $filename);
        $compile = SZ_YI_PATH . "/data/tpl/{$filename}.tpl.php";
        $source = SZ_YI_PATH . "/template/web/{$filename}.html";
        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            shop_template_compile($source, $compile, true);
        }
        return $compile;
    }

    /**
     * 生成后台url
     * @param $route
     * @return string
     */
    public function createWebUrl($route)
    {
        return Url::web($route);
    }

    /**
     * 生成插件url
     * @param $route
     * @return string
     */
    public function createPluginWebUrl($route)
    {
        return Url::web($route);
    }

    /**
     * 生成前台Url
     * @param $route
     * @return string
     */
    public function createMobileUrl($route)
    {
        return Url::app($route);
    }
}