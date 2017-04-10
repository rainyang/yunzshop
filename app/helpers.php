<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use app\common\services\PermissionService;
use app\common\helpers\Url;

if (!function_exists("html_images")) {

    function html_images($detail = '')
    {
        $detail = htmlspecialchars_decode($detail);
        preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]?))[\'|\"].*?[\/]?>/", $detail, $imgs);
        $images = array();
        if (isset($imgs[1])) {
            foreach ($imgs[1] as $img) {
                $im = array(
                    "old" => $img,
                    "new" => save_media($img)
                );
                $images[] = $im;
            }
        }
        foreach ($images as $img) {
            $detail = str_replace($img['old'], $img['new'], $detail);
        }
        return $detail;
    }
}
if (!function_exists("xml_to_array")) {
    function xml_to_array($xml)
    {
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if (preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $subxml = $matches[2][$i];
                $key = $matches[1][$i];
                if (preg_match($reg, $subxml)) {
                    $arr[$key] = xml_to_array($subxml);
                } else {
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
}

if (!function_exists("tomedia")) {
    /**
     * 获取附件的HTTP绝对路径
     * @param string $src 附件地址
     * @param bool $local_path 是否直接返回本地图片路径
     * @return string
     */
    function tomedia($src, $local_path = false)
    {
        if (empty($src)) {
            return '';
        }
        if (strexists($src, 'addons/')) {
            return YunShop::app()->siteroot . substr($src, strpos($src, 'addons/'));
        }
        //如果远程地址中包含本地host也检测是否远程图片
        if (strexists($src, YunShop::app()->siteroot) && !strexists($src, '/addons/')) {
            $urls = parse_url($src);
            $src = $t = substr($urls['path'], strpos($urls['path'], 'images'));
        }
        $t = strtolower($src);
        if (strexists($t, 'http://') || strexists($t, 'https://') || substr($t, 0, 2) == '//') {
            return $src;
        }
        if ($local_path || empty(YunShop::app()->setting['remote']['type']) || file_exists(base_path('../../') . '/' . YunShop::app()->config['upload']['attachdir'] . '/' . $src)) {
            $src = YunShop::app()->siteroot . YunShop::app()->config['upload']['attachdir'] . '/' . $src;
        } else {
            $src = YunShop::app()->attachurl_remote . $src;
        }
        return $src;
    }
}
if (!function_exists("strexists")) {
    /**
     * 判断字符串是否包含子串
     * @param string $string 在该字符串中进行查找
     * @param string $find 需要查找的字符串
     * @return boolean
     */
    function strexists($string, $find)
    {
        return !(strpos($string, $find) === false);
    }
}
if (!function_exists("set_medias")) {
    function set_medias($list = array(), $fields = null)
    {
        if (empty($fields)) {
            foreach ($list as &$row) {
                $row = tomedia($row);
            }
            return $list;
        }
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        if (is_array2($list)) {
            foreach ($list as $key => &$value) {
                foreach ($fields as $field) {
                    if (isset($list[$field])) {
                        $list[$field] = tomedia($list[$field]);
                    }
                    if (is_array($value) && isset($value[$field])) {
                        $value[$field] = tomedia($value[$field]);
                    }
                }
            }
            return $list;
        } else {
            foreach ($fields as $field) {
                if (isset($list[$field])) {
                    $list[$field] = tomedia($list[$field]);
                }
            }
            return $list;
        }
    }
}
if (!function_exists('is_array2')) {
    function is_array2($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                return is_array($v);
            }
            return false;
        }
        return false;
    }
}

if (!function_exists("show_json")) {
    function show_json($status = 1, $return = null, $variable = null)
    {
        $ret = array(
            'status' => $status
        );
        if ($return) {
            $ret['result'] = $return;
        }

        if (Yunshop::isApi()) {
            return array(
                'status' => $status,
                'variable' => $variable,
                'json' => $return,
            );
        }
        die(json_encode($ret));
    }
}
if (!function_exists("array_column")) {

    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string)$params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int)$params[2];
            } else {
                $paramsIndexKey = (string)$params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string)$row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}

if (!function_exists('shop_template_compile')) {
    function shop_template_compile($from, $to, $inmodule = false)
    {
        $path = dirname($to);
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $content = shop_template_parse(file_get_contents($from), $inmodule);

        file_put_contents($to, $content);
    }
}

if (!function_exists('shop_template_parse')) {
    function shop_template_parse($str, $inmodule = false)
    {
        $str = template_parse($str, $inmodule);
        $str = preg_replace('/{ifp\s+(.+?)}/', '<?php if(cv($1)) { ?>', $str);
        $str = preg_replace('/{ifpp\s+(.+?)}/', '<?php if(cp($1)) { ?>', $str);
        $str = preg_replace('/{ife\s+(\S+)\s+(\S+)}/', '<?php if( ce($1 ,$2) ) { ?>', $str);
        return $str;
    }
}
if (!function_exists('objectArray')) {
    function objectArray($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = objectArray($value);
            }
        }
        return $array;
    }
}


if (!function_exists('my_link_extra')) {

    function my_link_extra($type = 'content')
    {

        $content = "";

        $extraContents = [];

        Event::fire(new app\common\events\RenderingMyLink($extraContents));

        return $type == 'content' ? $content . implode("\n", $extraContents) : implode("\n",
            array_keys($extraContents));
    }
}

if (!function_exists('can')) {
    /**
     * 权限判断
     * @param $item   可以是item 或者是route
     * @param bool $isRoute
     * @return bool
     */
    function can($itemRoute, $isRoute = false)
    {
        if ($isRoute == true) {
            $item = \app\common\models\Menu::getItemByRoute($itemRoute);
        } else {
            $item = $itemRoute;
        }
        return PermissionService::can($item);
    }
}

if (!function_exists('weAccount')) {
    /**
     * 获取微擎账号体系
     * @return NULL|WeAccount
     */
    function weAccount()
    {
        load()->model('account');
        return WeAccount::create();
    }
}


if (!function_exists('yzWebUrl')) {
    function yzWebUrl($route, $params = [])
    {
        return Url::web($route, $params);
    }
}

if (!function_exists('yzAppUrl')) {
    function yzAppUrl($route, $params = [])
    {
        return Url::app($route, $params);
    }
}

if (!function_exists('yzApiUrl')) {
    function yzApiUrl($route, $params = [])
    {
        return Url::api($route, $params);
    }
}

if (!function_exists('yzWebFullUrl')) {
    function yzWebFullUrl($route, $params = [])
    {
        return Url::absoluteWeb($route, $params);
    }
}

if (!function_exists('yzAppFullUrl')) {
    function yzAppFullUrl($route, $params = [])
    {
        return Url::absoluteApp($route, $params);
    }
}

if (!function_exists('yzUrl')) {
    function yzUrl($route, $params = [])
    {
        return Url::web($route, $params);
    }
}

if (!function_exists('array_child_kv_exists')) {
    function array_child_kv_exists($array, $childKey, $value)
    {
        $result = false;
        if (is_array($array)) {
            foreach ($array as $v) {
                if (is_array($v) && isset($v[$childKey])) {
                    $result = $v[$childKey] == $value;
                }
            }
        }

        return $result;
    }
}

if (!function_exists('widget')) {
    function widget($class, $params = [])
    {
        return (new $class($params))->run();
    }
}
if (!function_exists('assets')) {

    function assets($relativeUri)
    {
        // add query string to fresh cache
        if (Str::startsWith($relativeUri, 'styles') || Str::startsWith($relativeUri, 'scripts')) {
            return Url::shopUrl("resources/assets/dist/$relativeUri") . "?v=" . config('app.version');
        } elseif (Str::startsWith($relativeUri, 'lang')) {
            return Url::shopUrl("resources/$relativeUri");
        } else {
            return Url::shopUrl("resources/assets/$relativeUri");
        }
    }
}
if (!function_exists('static_url')) {

    function static_url($relativeUri)
    {
        return Url::shopUrl('static/' . $relativeUri);
    }
}

if (!function_exists('plugin')) {

    function plugin($id)
    {
        return app('plugins')->getPlugin($id);
    }
}

if (!function_exists('plugin_assets')) {

    function plugin_assets($id, $relativeUri)
    {
        if ($plugin = plugin($id)) {
            return $plugin->assets($relativeUri);
        } else {
            throw new InvalidArgumentException("No such plugin.");
        }
    }
}

if (!function_exists('json')) {

    function json()
    {
        $args = func_get_args();

        if (count($args) == 1 && is_array($args[0])) {
            return Response::json($args[0]);
        } elseif (count($args) == 3 && is_array($args[2])) {
            // the third argument is array of extra fields
            return Response::json(array_merge([
                'errno' => $args[1],
                'msg' => $args[0]
            ], $args[2]));
        } else {
            return Response::json([
                'errno' => Arr::get($args, 1, 1),
                'msg' => $args[0]
            ]);
        }
    }
}

if (!function_exists('yz_footer')) {

    function yz_footer($page_identification = "")
    {
        $content = "";
        /*
                $scripts = [
                    assets('scripts/app.min.js'),
                    assets('lang/'.config('app.locale').'/locale.js'),
                ];

                if ($page_identification !== "") {
                    $scripts[] = assets("scripts/$page_identification.js");
                }

                foreach ($scripts as $script) {
                    $content .= "<script type=\"text/javascript\" src=\"$script\"></script>\n";
                }
        */
        $customJs = option("custom_js");
        $customJs && $content .= '<script>' . $customJs . '</script>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingFooter($extraContents));

        return $content . implode("\n", $extraContents);
    }
}

if (!function_exists('yz_header')) {

    function yz_header($pageIdentification = "")
    {
        $content = "";
        /*
                $styles = [
                    assets('styles/app.min.css'),
                    assets('styles/skins/'.Option::get('color_scheme').'.min.css')
                ];

                if ($pageIdentification !== "") {
                    $styles[] = assets("styles/$pageIdentification.css");
                }

                foreach ($styles as $style) {
                    $content .= "<link rel=\"stylesheet\" href=\"$style\">\n";
                }
        */
        $customCss = option("custom_css");
        $customCss && $content .= '<style>' . option("custom_css") . '</style>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingHeader($extraContents));

        return $content . implode("\n", $extraContents);
    }
}


if (!function_exists('yz_menu')) {

    function yz_menu($type)
    {
        $menu = config('menu');

        Event::fire($type == "member" ? new app\common\events\ConfigureMemberMenu($menu)
            : new app\common\events\ConfigureAdminMenu($menu));

        if (!isset($menu[$type])) {
            throw new InvalidArgumentException;
        }

        return yz_menu_render($menu[$type]);
    }

    function yz_menu_render($data)
    {
        $content = "";

        foreach ($data as $key => $value) {
            $active = app('request')->is(@$value['link']);

            // also set parent as active if any child is active
            foreach ((array)@$value['children'] as $childKey => $childValue) {
                if (app('request')->is(@$childValue['link'])) {
                    $active = true;
                }
            }

            $content .= $active ? '<li class="active">' : '<li>';

            if (isset($value['children'])) {
                $content .= '<a href="#"><i class="fa ' . $value['icon'] . '"></i> <span>' . trans($value['title']) . '</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
                // recurse
                $content .= '<ul class="treeview-menu" style="display: none;">' . yz_menu_render($value['children']) . '</ul>';
            } else {
                $content .= '<a href="' . url($value['link']) . '"><i class="fa ' . $value['icon'] . '"></i> <span>' . trans($value['title']) . '</span></a>';
            }

            $content .= '</li>';
        }

        return $content;
    }
}


if (!function_exists('option')) {
    /**
     * Get / set the specified option value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @param  raw $raw return raw value without convertion
     * @return mixed
     */
    function option($key = null, $default = null, $raw = false)
    {
        $options = app('options');

        if (is_null($key)) {
            return $options;
        }

        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $options->set($innerKey, $innerValue);
            }
            return;
        }

        return $options->get($key, $default, $raw);
    }
}
if (!function_exists('float_greater')) {
    function float_greater($number, $other_number)
    {
        return bccomp($number, $other_number) === 1;
    }
}
if (!function_exists('float_lesser')) {
    function float_lesser($number, $other_number)
    {
        return bccomp($number, $other_number) === -1;
    }
}
if (!function_exists('float_equal')) {
    function float_equal($number, $other_number)
    {
        return bccomp($number, $other_number) === 0;
    }

}

  /*
   * 生成一个随机订单号：如果需要唯一性，请自己验证重复调用
   *
   * @params string $prefix 标示 SN RV
   * @params bool $numeric 是否为纯数字
   *
   * @return mixed
   * @Author yitian */
if (!function_exists('createNo')) {
    function createNo($prefix, $numeric = FALSE)
    {
        return $prefix . date('YmdHis') . \app\common\helpers\Client::random(6, $numeric);
    }
}