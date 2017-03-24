<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use app\common\services\PermissionService;
use app\common\helpers\Url;


if(!function_exists('can')){
    /**
     * 权限判断
     * @param $item   可以是item 或者是route
     * @param bool $isRoute
     * @return bool
     */
    function can($itemRoute, $isRoute = false)
    {
        if($isRoute == true){
            $item = \app\common\models\Menu::getItemByRoute($itemRoute);
        }else{
            $item = $itemRoute;
        }
        return PermissionService::can($item);
    }
}

if(!function_exists('weAccount')) {
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


if(!function_exists('yzWebUrl')){
    function yzWebUrl($route, $params = [])
    {
        return Url::web($route,$params);
    }
}

if(!function_exists('yzAppUrl')){
    function yzAppUrl($route, $params = [])
    {
        return Url::app($route,$params);
    }
}


if(!function_exists('yzWebFullUrl')){
    function yzWebFullUrl($route, $params = [])
    {
        return Url::absoluteWeb($route,$params);
    }
}

if(!function_exists('yzAppFullUrl')){
    function yzAppFullUrl($route, $params = [])
    {
        return Url::absoluteApp($route,$params);
    }
}

if(!function_exists('yzUrl')){
    function yzUrl($route, $params = [])
    {
        return Url::web($route,$params);
    }
}

if(!function_exists('array_child_kv_exists')){
    function array_child_kv_exists($array, $childKey, $value)
    {
        $result = false;
        if(is_array($array)){
            foreach ($array as $v){
                if(is_array($v) && isset($v[$childKey])){
                    $result = $v[$childKey] == $value;
                }
            }
        }

        return $result;
    }
}

if(!function_exists('widget')){
    function widget($class, $params = [])
    {
        return (new $class($params))->run();
    }
}
if (! function_exists('assets')) {

    function assets($relativeUri)
    {
        // add query string to fresh cache
        if (Str::startsWith($relativeUri, 'styles') || Str::startsWith($relativeUri, 'scripts')) {
            return Url::shopUrl("resources/assets/dist/$relativeUri")."?v=".config('app.version');
        } elseif (Str::startsWith($relativeUri, 'lang')) {
            return Url::shopUrl("resources/$relativeUri");
        } else {
            return Url::shopUrl("resources/assets/$relativeUri");
        }
    }
}
if (! function_exists('static_url')) {

    function static_url($relativeUri)
    {
       return Url::shopUrl('static/' . $relativeUri);
    }
}

if (! function_exists('plugin')) {

    function plugin($id)
    {
        return app('plugins')->getPlugin($id);
    }
}

if (! function_exists('plugin_assets')) {

    function plugin_assets($id, $relativeUri)
    {
        if ($plugin = plugin($id)) {
            return $plugin->assets($relativeUri);
        } else {
            throw new InvalidArgumentException("No such plugin.");
        }
    }
}

if (! function_exists('json')) {

    function json()
    {
        $args = func_get_args();

        if (count($args) == 1 && is_array($args[0])) {
            return Response::json($args[0]);
        } elseif (count($args) == 3 && is_array($args[2])) {
            // the third argument is array of extra fields
            return Response::json(array_merge([
                'errno' => $args[1],
                'msg'   => $args[0]
            ], $args[2]));
        } else {
            return Response::json([
                'errno' => Arr::get($args, 1, 1),
                'msg'   => $args[0]
            ]);
        }
    }
}

if (! function_exists('yz_footer')) {

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
        $customJs && $content .=  '<script>'.$customJs.'</script>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingFooter($extraContents));

        return $content . implode("\n", $extraContents);
    }
}

if (! function_exists('yz_header')) {

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
        $customCss &&  $content .= '<style>'.option("custom_css").'</style>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingHeader($extraContents));

        return $content . implode("\n", $extraContents);
    }
}


if (! function_exists('yz_menu')) {

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
            foreach ((array) @$value['children'] as $childKey => $childValue) {
                if (app('request')->is(@$childValue['link'])) {
                    $active = true;
                }
            }

            $content .= $active ? '<li class="active">' : '<li>';

            if (isset($value['children'])) {
                $content .= '<a href="#"><i class="fa '.$value['icon'].'"></i> <span>'.trans($value['title']).'</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
                // recurse
                $content .= '<ul class="treeview-menu" style="display: none;">'.yz_menu_render($value['children']).'</ul>';
            } else {
                $content .= '<a href="'.url($value['link']).'"><i class="fa '.$value['icon'].'"></i> <span>'.trans($value['title']).'</span></a>';
            }

            $content .= '</li>';
        }

        return $content;
    }
}



if (! function_exists('option')) {
    /**
     * Get / set the specified option value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @param  raw    $raw  return raw value without convertion
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
