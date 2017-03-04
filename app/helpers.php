<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

if (! function_exists('defined_or_null')) {
    function defined_or_null($value){
        if(isset($value)){
            return $value;
        }
        return null;
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
            return url("resources/assets/dist/$relativeUri")."?v=".config('app.version');
        } elseif (Str::startsWith($relativeUri, 'lang')) {
            return url("resources/$relativeUri");
        } else {
            return url("resources/assets/$relativeUri");
        }
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

if (! function_exists('bs_footer')) {

    function yz_footer($page_identification = "")
    {
        $content = "";

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

        $content .=  '<script>'.option("custom_js").'</script>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingFooter($extraContents));

        return $content . implode("\n", $extraContents);
    }
}

if (! function_exists('bs_header')) {

    function yz_header($page_identification = "")
    {
        $content = "";

        $styles = [
            assets('styles/app.min.css'),
            assets('styles/skins/'.Option::get('color_scheme').'.min.css')
        ];

        if ($page_identification !== "") {
            $styles[] = assets("styles/$page_identification.css");
        }

        foreach ($styles as $style) {
            $content .= "<link rel=\"stylesheet\" href=\"$style\">\n";
        }

        $content .= '<style>'.option("custom_css").'</style>';

        $extraContents = [];

        Event::fire(new app\common\events\RenderingHeader($extraContents));

        return $content . implode("\n", $extraContents);
    }
}

if (! function_exists('yz_favicon')) {

    function yz_favicon()
    {
        // fallback to default favicon
        $url = Str::startsWith($url = (option('favicon_url') ?: config('options.favicon_url')), 'http') ? $url : assets($url);

        return <<< ICONS
<link rel="shortcut icon" href="$url">
<link rel="icon" type="image/png" href="$url" sizes="192x192">
<link rel="apple-touch-icon" href="$url" sizes="180x180">
ICONS;
    }
}

if (! function_exists('yz_menu')) {

    function yz_menu($type)
    {
        $menu = config('menu');

        Event::fire($type == "user" ? new app\common\events\ConfigureUserMenu($menu)
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
