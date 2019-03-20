<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use app\common\services\PermissionService;
use app\common\helpers\Url;
use Ixudra\Curl\Facades\Curl;

if (!function_exists("yz_tpl_ueditor")) {
    function yz_tpl_ueditor($id, $value = '', $options = array())
    {
        $s = '';
        if (!defined('TPL_INIT_UEDITOR')) {
            $s .= '<script type="text/javascript" src="../addons/yun_shop/app/common/components/ueditor/ueditor.config.js"></script><script type="text/javascript" src="../addons/yun_shop/app/common/components/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="../addons/yun_shop/app/common/components/ueditor/lang/zh-cn/zh-cn.js"></script><link href="/web/resource/components/webuploader/webuploader.css" rel="stylesheet"><link href="/web/resource/components/webuploader/style.css" rel="stylesheet">';
        }
        $options['height'] = empty($options['height']) ? 200 : $options['height'];
        $s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
        $s .= "
	<script type=\"text/javascript\">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': {$options['height']},
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multi : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			UE.registerUI('myinsertvideo',function(editor,uiName){
    editor.registerCommand(uiName, {
        execCommand:function(){
            require(['../addons/yun_shop/static/js/fileUploader.min.js'],
                function(uploader){
                    uploader.show(function(video){
                        if (!video) {
                            return;
                        } else {
                            var videoType = video.isRemote ? 'iframe' : 'video';
                            editor.execCommand('insertvideo', {
                                'url' : video.url,
                                'width' : '100%',
                                'height' : 200
                            }, videoType);
                        }
                    }, {type:'video'});
                }
            );
        }
    });
    var btn = new UE.ui.Button({
        name: '插入视频',
        title: '插入视频',
        cssRules :'background-position: -320px -20px',
        onclick:function () {
            editor.execCommand(uiName);
        }
    });
    editor.addListener('selectionchange', function () {
        var state = editor.queryCommandState(uiName);
        if (state == -1) {
            btn.setDisabled(true);
            btn.setChecked(false);
        } else {
            btn.setDisabled(false);
            btn.setChecked(state);
        }
    });
    return btn;
}, 20);
			" . (!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '') . "
	</script>";
        return $s;
    }

}
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
            return request()->getSchemeAndHttpHost() . substr($src, strpos($src, 'addons/'));
        }
        //如果远程地址中包含本地host也检测是否远程图片
        if (strexists($src, request()->getSchemeAndHttpHost()) && !strexists($src, '/addons/')) {
            $urls = parse_url($src);
            $src = $t = substr($urls['path'], strpos($urls['path'], 'images'));
        }
        $t = strtolower($src);
        if (strexists($t, 'http://') || strexists($t, 'https://') || substr($t, 0, 2) == '//') {
            return $src;
        }

        if ($local_path || empty(YunShop::app()->setting['remote']['type']) || file_exists(base_path('../../') . '/' . YunShop::app()->config['upload']['attachdir'] . '/' . $src)) {
            if (env('APP_Framework') == 'platform') {
                $src = request()->getSchemeAndHttpHost() . '/' . $src;
            } else {
                $src = request()->getSchemeAndHttpHost() . '/attachment/' . $src;
            }
        } else {
            $src = YunShop::app()->attachurl_remote . $src;
        }
        return $src;
    }
}

function yz_tomedia($src, $local_path = false)
{
    if (env('APP_Framework') == 'platform') {
        $setting = [];
    } else {
        global $_W;
        $setting = \setting_load();
    }

    if (empty($src)) {
        return '';
    }
    $os = \app\common\helpers\Client::osType();

    if (strexists($src, 'addons/')) {
        if ($os == \app\common\helpers\Client::OS_TYPE_IOS) {
            $url_dz = request()->getSchemeAndHttpHost() . substr($src, strpos($src, '/addons/'));
            return 'https:' . substr($url_dz, strpos($url_dz, '//'));
        }
        return request()->getSchemeAndHttpHost() . substr($src, strpos($src, '/addons/'));
    }
    //判断是否是本地带域名图片地址
    $local = strtolower($src);
    if (strexists($src, '/attachment/')) {
        if ($os == \app\common\helpers\Client::OS_TYPE_IOS) {
            $url_dz = request()->getSchemeAndHttpHost() . substr($src, strpos($src, '/attachment/'));
            return 'https:' . substr($url_dz, strpos($url_dz, '//'));
        }
        if (strexists($local, 'http://') || strexists($local, 'https://') || substr($local, 0, 2) == '//') {
            return $src;
        } else {
            return request()->getSchemeAndHttpHost() . substr($src, strpos($src, '/attachment/'));
        }
    }


    //如果远程地址中包含本地host也检测是否远程图片
    if (strexists($src, request()->getSchemeAndHttpHost()) && !strexists($src, '/addons/')) {
        $urls = parse_url($src);
        $src = $t = substr($urls['path'], strpos($urls['path'], 'images'));
    }
    $t = strtolower($src);
    if (strexists($t, 'http://') || strexists($t, 'https://') || substr($t, 0, 2) == '//') {
        return 'https:' . substr($src, strpos($src, '//'));
    }

    if ($local_path || empty($setting['remote']['type']) || file_exists(base_path('../../') . '/' . $_W['config']['upload']['attachdir'] . '/' . $src)) {
        if (strexists($src, '/attachment/')) {
            $src = request()->getSchemeAndHttpHost() . $src;
        } else {
            $src = request()->getSchemeAndHttpHost() . '/attachment/' . $src;
        }
    } else {
        if ($setting['remote']['type'] == 1) {
            $attachurl_remote = $setting['remote']['ftp']['url'] . '/';
        } elseif ($setting['remote']['type'] == 2) {
            $attachurl_remote = $setting['remote']['alioss']['url'] . '/';
        } elseif ($setting['remote']['type'] == 3) {
            $attachurl_remote = $setting['remote']['qiniu']['url'] . '/';
        } elseif ($setting['remote']['type'] == 4) {
            $attachurl_remote = $setting['remote']['cos']['url'] . '/';
        }

        $src = $attachurl_remote . $src;
    }

    if (!config('app.debug')) {
        $src = 'https:' . substr($src, strpos($src, '//'));
    }

    return $src;
}

if (!function_exists("replace_yunshop")) {
    function replace_yunshop($url)
    {
        $moduleName = \Config::get('app.module_name');
        return str_replace(DIRECTORY_SEPARATOR . "addons" . DIRECTORY_SEPARATOR . $moduleName, "", $url);
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
                $row = yz_tomedia($row);
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
                        $list[$field] = yz_tomedia($list[$field]);
                    }
                    if (is_array($value) && isset($value[$field])) {
                        $value[$field] = yz_tomedia($value[$field]);
                    }
                }
            }
            return $list;
        } else {
            foreach ($fields as $field) {
                if (isset($list[$field])) {
                    $list[$field] = yz_tomedia($list[$field]);
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
        \app\common\services\Utils::mkdirs($path);
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
        /*if(config('app.menu_key') != 'menu'){
            return true;
        }*/
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

if (!function_exists('yzPluginUrl')) {
    function yzPluginUrl($route, $params = [])
    {
        return Url::plugin($route, $params);
    }
}

if (!function_exists('yzPluginFullUrl')) {
    function yzPluginFullUrl($route, $params = [])
    {
        return Url::absolutePlugin($route, $params);
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

if (!function_exists('yzDiyFullUrl')) {
    function yzDiyFullUrl($route, $params = [])
    {
        return Url::absoluteDiyApp($route, $params);
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
                    $result += $v[$childKey] == $value;
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
        //$optionsData = $options->get();
        //return $optionsData[$key]['option_value'];
        return $options->get($key, $default, $raw)['option_value'];
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

if (!function_exists('sdd')) {
    function sdd()
    {
        global $testDd;
        $testDd = !$testDd;
    }
}
if (!function_exists('tdd')) {
    function tdd()
    {
        global $testDd;
        if ($testDd) {
            dd(func_get_args());
        }
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
    function createNo($prefix, $length = 6, $numeric = FALSE)
    {
        return $prefix . date('YmdHis') . \app\common\helpers\Client::random($length, $numeric);
    }
}
if (!function_exists('yz_array_set')) {
    function yz_array_set(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }
}
if (!function_exists('trace_log')) {
    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    function trace_log(){
        return app('Log.trace');
    }
}

if (!function_exists('debug_log')) {
    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    function debug_log(){
        return app('Log.debug');
    }
}

if (!function_exists('getIp')) {
    /**
     * 获取登录的 ip 地址
     * @return string
     */
    function getIp()
    {
        static $ip = '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
            return $ip;
        } else {
            return '127.0.0.1';
        }
    }
}

if (!function_exists('randNum')) {
    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
     function randNum($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}

if (!function_exists('file_random_name')) {
    function file_random_name($dir, $ext)
    {
        do {
            $filename = random(30) . '.' . $ext;
        } while (file_exists($dir . $filename));

        return $filename;
    }
}

if (!function_exists('random')) {
    function random($length, $numeric = FALSE)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}

if (!function_exists('is_error')) {
    function is_error($data)
    {
        if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('mkdirs')) {
    function mkdirs($path)
    {
        if (!is_dir($path)) {
            mkdirs(dirname($path));
            mkdir($path);
        }

        return is_dir($path);
    }
}

if (!function_exists('file_image_quality')) {
    function file_image_quality($src, $to_path, $ext, $global)
    {
        $quality = intval($global['zip_percentage']);
        if ($quality <= 0 || $quality >= 100) {
            return;
        }

        if (filesize($src) / 1024 > 5120) {
            return;
        }

        $result = \app\platform\modules\system\models\Image::create($src, $ext)->saveTo($to_path, $quality);
        return $result;
    }
}

if (!function_exists('safe_gpc_path')) {
    function safe_gpc_path($value, $default = '') {
        $path = safe_gpc_string($value);
        $path = str_replace(array('..', '..\\', '\\\\' ,'\\', '..\\\\'), '', $path);

        if (empty($path) || $path != $value) {
            $path = $default;
        }

        return $path;
    }
}

if (!function_exists('safe_gpc_string')) {
    function safe_gpc_string($value, $default = '')
    {
        $value = safe_bad_str_replace($value);
        $value = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $value);

        if (empty($value) && $default != $value) {
            $value = $default;
        }
        return $value;
    }
}

if (!function_exists('array_elements')) {
    function array_elements($keys, $src, $default = FALSE)
    {
        $return = array();
        if (!is_array($keys)) {
            $keys = array($keys);
        }
        foreach ($keys as $key) {
            if (isset($src[$key])) {
                $return[$key] = $src[$key];
            } else {
                $return[$key] = $default;
            }
        }
        return $return;
    }
}

if (!function_exists('sizecount')) {
    function sizecount($size)
    {
        if ($size >= 1073741824) {
            $size = round($size / 1073741824 * 100) / 100 . ' GB';
        } elseif ($size >= 1048576) {
            $size = round($size / 1048576 * 100) / 100 . ' MB';
        } elseif ($size >= 1024) {
            $size = round($size / 1024 * 100) / 100 . ' KB';
        } else {
            $size = $size . ' Bytes';
        }
        return $size;
    }
}

if (!function_exists('file_image_thumb')) {
    function file_image_thumb($srcfile, $desfile = '', $width = 0, $global)
    {
        if (intval($width) == 0) {
            $width = intval($global['thumb_width']);
        }
        if (!$desfile) {
            $ext = pathinfo($srcfile, PATHINFO_EXTENSION);
            $srcdir = dirname($srcfile);
            do {
                $desfile = $srcdir . '/' . random(30) . ".{$ext}";
            } while (file_exists($desfile));
        }

        $des = dirname($desfile);
        if (!file_exists($des)) {
            if (!mkdirs($des)) {
                return 1;
            }
        } elseif (!is_writable($des)) {
            return 2;
        }
        $org_info = @getimagesize($srcfile);
        if ($org_info) {
            if ($width == 0 || $width > $org_info[0]) {
                copy($srcfile, $desfile);
                return str_replace(base_path() . '/', '', $desfile);
            }
        }
        $scale_org = $org_info[0] / $org_info[1];
        $height = $width / $scale_org;
        $desfile = \app\platform\modules\system\models\Image::create($srcfile)->resize($width, $height)->saveTo($desfile);
        if (!$desfile) {
            return false;
        }

        return str_replace(base_path() . '/', '', $desfile);
    }
}

if (!function_exists('file_is_image')) {
    function file_is_image($url)
    {
        if (!parse_path($url)) {
            return false;
        }
        $pathinfo = pathinfo($url);
        $extension = strtolower($pathinfo['extension']);

        return !empty($extension) && in_array($extension, array('jpg', 'jpeg', 'gif', 'png'));
    }
}

if (!function_exists('file_remote_upload')) {
    function file_remote_upload($filename, $auto_delete_local = true, $remote)
    {
        if (!$remote['type']) {
            return false;
        }
        if ($remote['type'] == '2') {
//            load()->library('oss');
//            load()->model('attachment');
            $buckets = attachment_alioss_buctkets($remote['alioss']['key'], $remote['alioss']['secret']);
            $host_name = $remote['alioss']['internal'] ? '-internal.aliyuncs.com' : '.aliyuncs.com';
            $endpoint = 'http://' . $buckets[$remote['alioss']['bucket']]['location'] . $host_name;
            try {
                $ossClient = new \OSS\OssClient($remote['alioss']['key'], $remote['alioss']['secret'], $endpoint);
                $ossClient->uploadFile($remote['alioss']['bucket'], $filename, base_path() . $filename);
            } catch (\OSS\Core\OssException $e) {
                return [
                    'status' => 1,
                    'msg' => $e->getMessage()
                ];
            }
            if ($auto_delete_local) {
                file_delete($filename);
            }
        } elseif ($remote['type'] == '4') {
            if ($remote['cos']['local']) {
//                load()->library('cos');
                qcloudcos\Cosapi::setRegion($remote['cos']['local']);
                $uploadRet = qcloudcos\Cosapi::upload($remote['cos']['bucket'], base_path() . $filename, '/' . $filename, '', 3 * 1024 * 1024, 0);
            } else {
//                load()->library('cosv3');
                $uploadRet = \Qcloud_cos\Cosapi::upload($remote['cos']['bucket'], base_path() . $filename, '/' . $filename, '', 3 * 1024 * 1024, 0);
            }
            if ($uploadRet['code'] != 0) {
                return [
                    'status' => $uploadRet['code'],
                    'msg' => ''
                ];
            }
            if ($auto_delete_local) {
                file_delete($filename);
            }
        }
    }
}

if (!function_exists('attachment_alioss_buctkets')) {
    function attachment_alioss_buctkets($key, $secret)
    {
        $url = 'http://oss-cn-beijing.aliyuncs.com';
        try {
            $ossClient = new \OSS\OssClient($key, $secret, $url);
        } catch(\OSS\Core\OssException $e) {
            return $this->error(1, $e->getMessage());
        }
        try {
            $bucketlistinfo = $ossClient->listBuckets();
        } catch(\OSS\Core\OssException $e) {
            return $this->error(1, $e->getMessage());
        }
        $bucketlistinfo = $bucketlistinfo->getBucketList();
        $bucketlist = array();
        foreach ($bucketlistinfo as &$bucket) {
            $bucketlist[$bucket->getName()] = array('name' => $bucket->getName(), 'location' => $bucket->getLocation());
        }
        return $bucketlist;
    }
}

if (!function_exists('file_delete')) {
    function file_delete($file)
    {
        if (empty($file)) {
            return false;
        }
        if (file_exists($file)) {
            @unlink($file);
        }
        if (file_exists(base_path() . '/' . $file)) {
            @unlink(base_path() . '/' . $file);
        }

        return true;
    }
}

if (!function_exists('safe_gpc_html')) {
    function safe_gpc_html($value, $default = '')
    {
        if (empty($value) || !is_string($value)) {
            return $default;
        }
        $value = safe_bad_str_replace($value);

        $value = safe_remove_xss($value);
        if (empty($value) && $value != $default) {
            $value = $default;
        }
        return $value;
    }
}

if (!function_exists('safe_bad_str_replace')) {
    function safe_bad_str_replace($string)
    {
        if (empty($string)) {
            return '';
        }
        $badstr = array("\0", "%00", "%3C", "%3E", '<?', '<%', '<?php', '{php', '../');
        $newstr = array('_', '_', '&lt;', '&gt;', '_', '_', '_', '_', '.._');
        $string = str_replace($badstr, $newstr, $string);

        return $string;
    }
}

if (!function_exists('safe_remove_xss')) {
    function safe_remove_xss($val)
    {
        $val = preg_replace('/([\x0e-\x19])/', '', $val);
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val);
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val);
        }
        preg_match_all('/href=[\'|\"](.*?)[\'|\"]|src=[\'|\"](.*?)[\'|\"]/i', $val, $matches);
        $url_list = array_merge($matches[1], $matches[2]);
        $encode_url_list = array();
        if (!empty($url_list)) {
            foreach ($url_list as $key => $url) {
                $val = str_replace($url, 'we7_' . $key . '_we7placeholder', $val);
                $encode_url_list[] = $url;
            }
        }
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'frameset', 'ilayer', 'bgsound', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', '@import');
        $ra = array_merge($ra1, $ra2);
        $found = true;
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2);
                $val = preg_replace($pattern, $replacement, $val);
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }
        if (!empty($encode_url_list) && is_array($encode_url_list)) {
            foreach ($encode_url_list as $key => $url) {
                $val = str_replace('we7_' . $key . '_we7placeholder', $url, $val);
            }
        }
        return $val;
    }
}

if (!function_exists('file_move')) {
    function file_move($filename, $dest)
    {
        mkdirs(dirname($dest));
        if (is_uploaded_file($filename)) {
            move_uploaded_file($filename, $dest);
        } else {
            rename($filename, $dest);
        }
//        @chmod($filename, $_W['config']['setting']['filemode']);
        return is_file($dest);
    }
}

if (!function_exists('pagination')) {
    function pagination($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '', 'callbackfuncname' => ''))
    {
        $pdata = array(
            'tcount' => 0,
            'tpage' => 0,
            'cindex' => 0,
            'findex' => 0,
            'pindex' => 0,
            'nindex' => 0,
            'lindex' => 0,
            'options' => ''
        );
        if (!$context['before']) {
            $context['before'] = 5;
        }
        if (!$context['after']) {
            $context['after'] = 4;
        }

        if ($context['ajaxcallback']) {
            $context['isajax'] = true;
        }

        if ($context['callbackfuncname']) {
            $callbackfunc = $context['callbackfuncname'];
        }

        $pdata['tcount'] = $total;
        $pdata['tpage'] = (!$pageSize || $pageSize < 0) ? 1 : intval(ceil($total / $pageSize));
        if ($pdata['tpage'] <= 1) {
//            dd($pdata['tpage'], $pageSize, $total);
            return '';
        }
        $cindex = $pageIndex;
        $cindex = min($cindex, $pdata['tpage']);
        $cindex = max($cindex, 1);
        $pdata['cindex'] = $cindex;
        $pdata['findex'] = 1;
        $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
        $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
        $pdata['lindex'] = $pdata['tpage'];

        if ($context['isajax']) {
            if (empty($url)) {
                $url = '/index.php/admin/system/upload/image' . '?' . http_build_query($_GET);
            }
            $pdata['faa'] = 'href="javascript:;" page="' . $pdata['findex'] . '" ' . ($callbackfunc ? 'ng-click="' . $callbackfunc . '(\'' . $url . '\', \'' . $pdata['findex'] . '\', this);"' : '');
            $pdata['paa'] = 'href="javascript:;" page="' . $pdata['pindex'] . '" ' . ($callbackfunc ? 'ng-click="' . $callbackfunc . '(\'' . $url . '\', \'' . $pdata['pindex'] . '\', this);"' : '');
            $pdata['naa'] = 'href="javascript:;" page="' . $pdata['nindex'] . '" ' . ($callbackfunc ? 'ng-click="' . $callbackfunc . '(\'' . $url . '\', \'' . $pdata['nindex'] . '\', this);"' : '');
            $pdata['laa'] = 'href="javascript:;" page="' . $pdata['lindex'] . '" ' . ($callbackfunc ? 'ng-click="' . $callbackfunc . '(\'' . $url . '\', \'' . $pdata['lindex'] . '\', this);"' : '');
        } else {
            if ($url) {
                $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
                $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
                $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
                $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
            } else {
                $_GET['page'] = $pdata['findex'];
                $pdata['faa'] = 'href="' . '/index.php/admin/system/upload/image' . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['pindex'];
                $pdata['paa'] = 'href="' . '/index.php/admin/system/upload/image' . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['nindex'];
                $pdata['naa'] = 'href="' . '/index.php/admin/system/upload/image' . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['lindex'];
                $pdata['laa'] = 'href="' . '/index.php/admin/system/upload/image' . '?' . http_build_query($_GET) . '"';
            }
        }

        $html = '<div><ul class="pagination pagination-centered">';
        $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
        empty($callbackfunc) && $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";

        if (!$context['before'] && $context['before'] != 0) {
            $context['before'] = 5;
        }
        if (!$context['after'] && $context['after'] != 0) {
            $context['after'] = 4;
        }

        if ($context['after'] != 0 && $context['before'] != 0) {
            $range = array();
            $range['start'] = max(1, $pdata['cindex'] - $context['before']);
            $range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
            if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
                $range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
                $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
            }
            for ($i = $range['start']; $i <= $range['end']; $i++) {
                if ($context['isajax']) {
                    $aa = 'href="javascript:;" page="' . $i . '" ' . ($callbackfunc ? 'ng-click="' . $callbackfunc . '(\'' . $url . '\', \'' . $i . '\', this);"' : '');
                } else {
                    if ($url) {
                        $aa = 'href="?' . str_replace('*', $i, $url) . '"';
                    } else {
                        $_GET['page'] = $i;
                        $aa = 'href="?' . http_build_query($_GET) . '"';
                    }
                }
                if (!empty($context['isajax'])) {
                    $html .= ($i == $pdata['cindex'] ? '<li class="active">' : '<li>') . "<a {$aa}>" . $i . '</a></li>';
                } else {
                    $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
                }
            }
        }

        if ($pdata['cindex'] < $pdata['tpage']) {
            empty($callbackfunc) && $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
            $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
        }
        $html .= '</ul></div>';
        return $html;
    }
}

if (!function_exists('http_build_query')) {
    function http_build_query($formdata, $numeric_prefix = null, $arg_separator = null)
    {
        if (!is_array($formdata))
            return false;
        if ($arg_separator == null)
            $arg_separator = '&';
        return http_build_recursive($formdata, $arg_separator);
    }
}