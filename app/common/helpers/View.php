<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 20:11
 */

namespace app\common\helpers;


class View
{
    public function render($filename, $data = [])
    {
        $compile = SZ_YI_PATH . "/data/tpl/{$filename}.tpl.php";
        $source = SZ_YI_PATH . "/template/web/{$filename}.html";
        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            shop_template_compile($source, $compile, true);
        }
        include $compile;
    }
}