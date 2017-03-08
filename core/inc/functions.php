<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
//新版微擎里有方法冲突,tpl_form_field_image在compat.biz里也有...
if (!defined('IS_API')) {
    load()->func('tpl');
}

function getExpress($express, $expresssn)
{
    $url = sprintf(SZ_YI_EXPRESS_URL, $express, $expresssn, time());
    //$url = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$express}&fromWeb=null&postid={$expresssn}";
    load()->func('communication');
    $resp = ihttp_request($url);
    $content = $resp['content'];

    if (empty($content)) {
        return array();
    }

    $content = json_decode($content);

    return $content->data;
}

/*
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
 */
function code62($x) {
    $show = '';
    while($x > 0) {
        $s = $x % 62;
        if ($s > 35) {
            $s = chr($s+61);
        } elseif ($s > 9 && $s <=35) {
            $s = chr($s + 55);
        }
        $show .= $s;
        $x = floor($x/62);
    }
    return $show;
}

function shorturl($url) {
    $url = crc32($url);
    $result = sprintf("%u", $url);
    //return $url;
    //return $result;
    return code62($result);
}

function sz_tpl_form_field_date($name, $value = '', $withtime = false)
{
    $s = '';
    if (!defined('TPL_INIT_DATA')) {
        $s = '
			<script type="text/javascript">
				require(["datetimepicker"], function(){
					$(function(){
						$(".datetimepicker").each(function(){
							var option = {
								lang : "zh",
								step : "30",
								timepicker : ' . (!empty($withtime) ? "true" : "false") .
            ',closeOnDateSelect : true,
			format : "Y-m-d' . (!empty($withtime) ? ' H:i:s"' : '"') .
            '};
			$(this).datetimepicker(option);
		});
	});
});
</script>';
        define('TPL_INIT_DATA', true);
    }
    $withtime = empty($withtime) ? false : true;
    if (!empty($value)) {
        $value = strexists($value, '-') ? strtotime($value) : $value;
    } else {
        $value = TIMESTAMP;
    }
    $value = ($withtime ? date('Y-m-d H:i:s', $value) : date('Y-m-d', $value));
    $s .= '<input type="text" name="' . $name . '"  value="' . $value . '" placeholder="请选择日期时间" class="datetimepicker form-control" style="padding-left:12px;" />';
    return $s;
}

function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
        if (stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        }
    }
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile',
            'WindowsWechat'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
//通过两个经纬度信息获取距离
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters

    /*
      Convert these degrees to radians
      to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

function chmod_dir($dir, $chmod = '')
{
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir . '/' . $file)) {
                    if ($file != '.' && $file != '..') {
                        $path = $dir . '/' . $file;
                        $chmod ? chmod($path, $chmod) : FALSE;
                        chmod_dir($path);
                    }
                } else {
                    $path = $dir . '/' . $file;
                    $chmod ? chmod($path, $chmod) : FALSE;
                }
            }
        }
        closedir($handle);
    }
}

function curl_download($url, $dir)
{
    $ch = curl_init($url);
    $fp = fopen($dir, "wb");
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $res = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $res;
}

function send_sms($account, $pwd, $mobile, $code, $type = 'check', $name = '', $title = '', $total = '', $tel = '')
{
    if ($type == 'check') {
        $content = "您的验证码是：" . $code . "。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

    } elseif ($type == 'verify') {
        $verify_set = m('common')->getSetData();
        $allset = iunserializer($verify_set['plugins']);
        if (is_array($allset) && !empty($allset['verify']['code_template'])) {
            $content = sprintf($allset['verify']['code_template'], $code, $title, $total, $name, $mobile, $tel);
        } else {
            $content = "提醒您，您的核销码为：" . $code . "，订购的票型是：" . $title . "，数量：" . $total . "张，购票人：" . $name . "，电话：" . $mobile . "，门店电话：" . $tel . "。请妥善保管，验票使用！";

        }

    }

    //$smsrs = file_get_contents('http://115.29.33.155/sms.php?method=Submit&account='.$account.'&password='.$pwd.'&mobile=' . $mobile . '&content='.urldecode($content));
    $smsrs = file_get_contents('http://106.ihuyi.cn/webservice/sms.php?method=Submit&account=' . $account . '&password=' . $pwd . '&mobile=' . $mobile . '&content=' . urldecode($content));
    return xml_to_array($smsrs);
}

function send_sms_alidayu($mobile, $code, $templateType)
{
    $set = m('common')->getSysset();
    include IA_ROOT . "/addons/sz_yi/core/alifish/TopSdk.php";
    //$appkey = '23355246';
    //$secret = '0c34a4887d2f52a6365a266bb3b38d25';

    switch ($templateType) {
        case 'reg':
            $templateCode = $set['sms']['templateCode'];
            $params = @explode("\n", $set['sms']['product']);
            break;
        case 'forget':
            $templateCode = $set['sms']['templateCodeForget'];
            $params = @explode("\n", $set['sms']['forget']);
            break;
        default:
            $templateCode = $set['sms']['templateCode'];
            break;
    }

    $c = new TopClient;
    $c->appkey = $set['sms']['appkey'];
    $c->secretKey = $set['sms']['secret'];
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($set['sms']['signname']);
    if (count($params) > 1) {
        $nparam['code'] = "{$code}";
        foreach ($params as $param) {
            $param = trim($param);
            $explode_param = explode("=", $param);
            $nparam[$explode_param[0]] = "{$explode_param[1]}";
        }
        //print_r(json_encode($nparam));exit;
        $req->setSmsParam(json_encode($nparam));
    } else {
        $req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"{$set['sms']['product']}\"}");
    }

    $req->setRecNum($mobile);
    $req->setSmsTemplateCode($templateCode);
    $resp = $c->execute($req);
    //print_r($resp);exit;
    return objectArray($resp);
}

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

function YZredirect($url, $sec = 0)
{
    echo "<meta http-equiv=refresh content='{$sec}; url={$url}'>";
    exit;
}

function m($name = '')
{
    static $_modules = array();
    if (isset($_modules[$name])) {
        return $_modules[$name];
    }
    $model = SZ_YI_CORE . "model/" . strtolower($name) . '.php';
    if (!is_file($model)) {
        die(' Model ' . $name . ' Not Found!');
    }
    require $model;
    $class_name = 'Sz_DYi_' . ucfirst($name);
    $_modules[$name] = new $class_name();
    return $_modules[$name];
}

function isEnablePlugin($name)
{
    $plugins = m("cache")->getArray("plugins", "global");
    if ($plugins) {
        foreach ($plugins as $p) {
            if ($p['identity'] == $name) {
                if ($p['status']) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    } else {
        return pdo_fetchcolumn("select count(*) from " . tablename('yz_plugin') . ' where identity=:identity and status=1', array(
            ':identity' => $name
        ));

    }
}

function p($name = '')
{
    return false;
    if (!isEnablePlugin($name)) {
        return false;
    }
    if ($name != 'perm' && !is_app()) {
        static $_perm_model;
        if (!$_perm_model) {
            $perm_model_file = SZ_YI_PLUGIN . 'perm/model.php';
            if (is_file($perm_model_file)) {
                require $perm_model_file;
                $perm_class_name = 'PermModel';
                $_perm_model = new $perm_class_name('perm');
            }
        }
        if ($_perm_model) {
            if (!$_perm_model->check_plugin($name)) {
                return false;
            }
        }
    }


    static $_plugins = array();
    if (isset($_plugins[$name])) {
        return $_plugins[$name];
    }
    $model = SZ_YI_PLUGIN . strtolower($name) . '/model.php';
    if (!is_file($model)) {
        return false;
    }
    require $model;
    $class_name = ucfirst($name) . 'Model';
    $_plugins[$name] = new $class_name($name);
    return $_plugins[$name];
}

function byte_format($input, $dec = 0)
{
    $prefix_arr = array(
        ' B',
        'K',
        'M',
        'G',
        'T'
    );
    $value = round($input, $dec);
    $i = 0;
    while ($value > 1024) {
        $value /= 1024;
        $i++;
    }
    $return_str = round($value, $dec) . $prefix_arr[$i];
    return $return_str;
}

function save_media($url)
{
    load()->func('file');
    $config = array(
        'qiniu' => false
    );
    $plugin = p('qiniu');
    if ($plugin) {
        $config = $plugin->getConfig();
        if ($config) {
            if (strexists($url, $config['url'])) {
                return $url;
            }
            $qiniu_url = $plugin->save(tomedia($url), $config);
            if (empty($qiniu_url)) {
                return $url;
            }
            return $qiniu_url;
        }
        return $url;
    }
    return $url;
}

function save_remote($url)
{

}

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

function get_last_day($year, $month)
{
    return date('t', strtotime("{$year}-{$month} -1"));
}

function show_message($msg = '', $url = '', $type = 'success')
{
    $scripts = "<script language='javascript'>require(['core'],function(core){ core.message('" . $msg . "','" . $url . "','" . $type . "')})</script>";
    die($scripts);
}

function show_json($status = 1, $return = null,$variable = null)
{
    $ret = array(
        'status' => $status
    );
    if ($return) {
        $ret['result'] = $return;
    }

    if (is_app_api()) {
        return array(
            'status' => $status,
            'variable' => $variable,
            'json' => $return,
        );
    }
    die(json_encode($ret));
}

function is_weixin_show()
{
    $set = m('common')->getSysset('app');
    $isapp = is_app();

    if ($set['base']['wx']['switch'] == '1' && !$isapp) {
        return false;
    }
    return true;
}

function is_weixin()
{
    global $_W;
    if($_GET['app_type'] == 'wechat'){
        return false;
    }
    if ($_W['uniaccount']['level'] == 1 OR $_W['uniaccount']['level'] == 3) {
        return false;
    }
    if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
        return false;
    }
    return true;
}

function is_app_api()
{
    return defined('__MODULE_NAME__') && __MODULE_NAME__ == 'app/api';
}

function b64_encode($obj)
{
    if (is_array($obj)) {
        return urlencode(base64_encode(json_encode($obj)));
    }
    return urlencode(base64_encode($obj));
}

function b64_decode($str, $is_array = true)
{
    $str = base64_decode(urldecode($str));
    if ($is_array) {
        return json_decode($str, true);
    }
    return $str;
}

function create_image($img)
{
    $ext = strtolower(substr($img, strrpos($img, '.')));
    if ($ext == '.png') {
        $thumb = imagecreatefrompng($img);
    } else if ($ext == '.gif') {
        $thumb = imagecreatefromgif($img);
    } else {
        $thumb = imagecreatefromjpeg($img);
    }
    return $thumb;
}

function get_authcode()
{
    $auth = get_auth();
    return empty($auth['code']) ? '' : $auth['code'];
}

function get_auth()
{
    global $_W;
    $set = pdo_fetch('select sets from ' . tablename('sz_yi_sysset') . ' order by id asc limit 1');
    $sets = iunserializer($set['sets']);
    if (is_array($sets)) {
        return is_array($sets['auth']) ? $sets['auth'] : array();
    }
    return array();
}

function check_shop_auth($url = '', $type = 's')
{
    global $_W, $_GPC;
    if ($_W['ispost'] && $_GPC['do'] != 'auth') {
        $auth = get_auth();
        load()->func('communication');
        $domain = $_SERVER['HTTP_HOST'];
        $ip = gethostbyname($domain);
        $setting = setting_load('site');
        $id = isset($setting['site']['key']) ? $setting['site']['key'] : '0';
        if (empty($type) || $type == 's') {
            $post_data = array(
                'type' => $type,
                'ip' => $ip,
                'id' => $id,
                'code' => $auth['code'],
                'domain' => $domain
            );
        } else {
            $post_data = array(
                'type' => 'm',
                'm' => $type,
                'ip' => $ip,
                'id' => $id,
                'code' => $auth['code'],
                'domain' => $domain
            );
        }
        $resp = ihttp_post($url, $post_data);
        $status = $resp['content'];
        if ($status != '1') {
            message(base64_decode('6K+35Yiw5b6u6LWe5a6Y5pa56LSt5LmwLeS6uuS6uuWVhuWfjuaooeWdly1iYnMuMDEyd3ouY29tIQ=='), '', 'error');
        }
    }
}

$my_scenfiles = array();
function my_scandir($dir)
{
    global $my_scenfiles;
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != "." && $file != ".git" && $file != "tmp"  && $file != "data") {
                if (is_dir($dir . "/" . $file)) {
                    my_scandir($dir . "/" . $file);
                } else {
                    $my_scenfiles[] = $dir . "/" . $file;
                }
            }
        }
        closedir($handle);
    }
}

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

function shop_template_parse($str, $inmodule = false)
{
    $str = template_parse($str, $inmodule);
    $str = preg_replace('/{ifp\s+(.+?)}/', '<?php if(cv($1)) { ?>', $str);
    $str = preg_replace('/{ifpp\s+(.+?)}/', '<?php if(cp($1)) { ?>', $str);
    $str = preg_replace('/{ife\s+(\S+)\s+(\S+)}/', '<?php if( ce($1 ,$2) ) { ?>', $str);
    return $str;
}

function ce($permtype = '', $item = null)
{
    $perm = p('perm');
    if ($perm) {
        return $perm->check_edit($permtype, $item);
    }
    return true;
}

function cv($permtypes = '')
{
    return true;
    $perm = p('perm');
    if ($perm) {
        return $perm->check_perm($permtypes);
    }
    return true;
}

function ca($permtypes = '')
{
    return true;

    if (!cv($permtypes)) {
        message('您没有权限操作，请联系管理员!', '', 'error');
    }
}

function cp($pluginname = '')
{
    return false;

    $perm = p('perm');
    if ($perm) {
        return $perm->check_plugin($pluginname);
    }
    return true;
}

function cpa($pluginname = '')
{
    if (!cp($pluginname)) {
        message('您没有权限操作，请联系管理员!', '', 'error');
    }
}

function plog($type = '', $op = '')
{
    $perm = p('perm');
    if ($perm) {
        $perm->log($type, $op);
    }
}

//stdClass Object 转 数组
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

if (!function_exists('tpl_form_field_category_3level')) {
    function tpl_form_field_category_3level($name, $parents, $children, $parentid, $childid, $thirdid)
    {
        return tpl_form_field_category_level3($name, $parents, $children, $parentid, $childid, $thirdid);
    }
}

if (function_exists('tpl_form_field_category_2level') == false) {
    function tpl_form_field_category_2level($name, $parents, $children, $parentid, $childid, $thirdid)
    {
        return tpl_form_field_category_level2($name, $parents, $children, $parentid, $childid, $thirdid);
    }
}

function tpl_form_field_category_level3($name, $parents, $children, $parentid, $childid, $thirdid)
{
    $html = '
<script type="text/javascript">
	window._' . $name . ' = ' . json_encode($children) . ';
</script>';
    if (!defined('TPL_INIT_CATEGORY_THIRD')) {
        $html .= '
<script type="text/javascript">
	function renderCategoryThird(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_child\');
                                                      $selectThird = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择二级分类</option>\';
                                                      var html1 = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
                                                                        $selectThird.html(html1);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
                                                    $selectThird.html(html1);
		});
	}
        function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
			';
        define('TPL_INIT_CATEGORY_THIRD', true);
    }
    $html .= '<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategoryThird(this,\'' . $name . '\')">
			<option value="0">请选择一级分类</option>';
    $ops = '';
    foreach ($parents as $row) {
        $html .= '
			<option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
    }
    $html .= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]" onchange="renderCategoryThird1(this,\'' . $name . '\')">
			<option value="0">请选择二级分类</option>';
    if (!empty($parentid) && !empty($children[$parentid])) {
        foreach ($children[$parentid] as $row) {
            $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
    }
    $html .= '
		</select>
	</div>
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $name . '_third" name="' . $name . '[thirdid]">
			<option value="0">请选择三级分类</option>';
    if (!empty($childid) && !empty($children[$childid])) {
        foreach ($children[$childid] as $row) {
            $html .= '
			<option value="' . $row['id'] . '"' . (($row['id'] == $thirdid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
    }
    $html .= '</select>
	</div>
</div>';
    return $html;
}

function tpl_form_field_category_level2($name, $parents, $children, $parentid, $childid)
{
    $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
    if (!defined('TPL_INIT_CATEGORY')) {
        $html .= '
        <script type="text/javascript">
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
        define('TPL_INIT_CATEGORY', true);
    }

    $html .=
        '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
    $ops = '';
    foreach ($parents as $row) {
        $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
    }
    $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]">
                    <option value="0">请选择二级分类</option>';
    if (!empty($parentid) && !empty($children[$parentid])) {
        foreach ($children[$parentid] as $row) {
            $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
    }
    $html .= '
                </select>
            </div>
        </div>
    ';
    return $html;
}

/**
 * 推送消息
 *
 * @param $customer_id_array
 * @param $message
 * @return array
 */
function sent_message($customer_id_array, $message)
{
    preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $message, $matches);

    if (empty($customer_id_array) || empty($matches[0])) {
        return false;
    }

    require IA_ROOT . '/addons/sz_yi/core/inc/plugin/vendor/leancloud/src/autoload.php';

    $setdata = m("cache")->get("sysset");
    $set = unserialize($setdata['sets']);

    $app = $set['app']['base'];

    LeanCloud\LeanClient::initialize($app['leancloud']['id'], $app['leancloud']['key'], $app['leancloud']['master'] . ",master");


    $customer_id_array_str = json_encode($customer_id_array, JSON_UNESCAPED_UNICODE);
    $post_data = '{"from_peer": "58",
                "to_peers": ' . $customer_id_array_str . ',
                "message": "{\"_lctype\":-1,\"_lctext\":\"' . $message . '\", \"_lcattrs\":{ \"clientId\":\"58\", \"clientName\":\"商城助手\", \"clientIcon\":\"http://192.168.1.108/image/icon.png\" }}"
                , "conv_id": "5721da8b71cfe4006b3f362b", "transient": false}';
    $data = json_decode($post_data, true);
    $lean_push = new LeanCloud\LeanMessage($data);
    $response = $lean_push->send();
    return $response;
}

function is_app()
{
    if(defined('__MODULE_NAME__') && __MODULE_NAME__ == 'app/api'){
        return true;
    }
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $yunzhong = (strpos($agent, 'yunzhong')) ? true : false;
    if ($yunzhong) {
        return true;
    }

    return false;
}
if (!function_exists("ddump")) {
    function ddump($var, $echo = true, $label = null, $strict = true){
        defined('IS_TEST')||define('IS_TEST',1);
        return dump($var, $echo, $label, $strict);
    }

}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
if (!function_exists("dump")) {

    function dump($var, $echo = true, $label = null, $strict = true)
    {
        if (!defined('IS_TEST')) {
            return;
        }
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else
            return $output;
    }
}
if (!function_exists("is_test")) {

    function is_test()
    {
        return defined('IS_TEST');
    }
}
if (!function_exists("array_part")) {
    function array_part($key, $array)
    {
        if (is_string($key)) {
            $key = explode(',', $key);
        }
        if (!is_array($array)) {
            $array = array();
        }
        foreach ($key as $key_item) {
            if (isset($array[$key_item])) {
                $res_array[$key_item] = $array[$key_item];
            } else {
                $res_array[$key_item] = '';
            }
        }
        return $res_array;
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
if (!function_exists("pdo_sql_debug")) {

    function pdo_sql_debug($sql, $placeholders)
    {
        foreach ($placeholders as $k => $v) {
            $sql = preg_replace('/' . $k . '/', "'" . $v . "'", $sql);
        }
        return $sql;
    }
}
/**
 * 对变量进行 JSON 编码
 * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
 * @return string 返回 value 值的 JSON 形式
 */
function json_encode_ex($value)
{
    $value2 = '{"result":"1","msg":"\u6210\u529f","data":{"total":"3","list":[{"id":"333","total":"1","isopenchannel":"0","isforceyunbi":"0","yunbi_deduct":"0.00","goodsid":"250","stock":"863","optionstock":"","maxbuy":"0","usermaxbuy":"0","title":"\u6d77\u5357\u519c\u57a6\u767d\u6c99\u7eff\u8336 \u6d77\u5357\u7279\u4ea7\u94c1\u76d2\u88c5 \u6b63\u54c1\u4e00\u7ea7150g\u514b\u94c1\u76d2\u7eff\u8336\u4e13\u4f9b","thumb":"http:\/\/demo.yunzshop.com\/attachment\/images\/sz_yi\/3\/2016\/12\/zCkvFPXIFx4JFX4fFXxk4xt2IcN36c.jpg","marketprice":"158.00","productprice":"0.00","optiontitle":"","optionid":"0","specs":"","option_ladders":""},{"id":"332","total":"1","isopenchannel":"0","isforceyunbi":"0","yunbi_deduct":"0.00","goodsid":"252","stock":"857","optionstock":"","maxbuy":"0","usermaxbuy":"0","title":"\u4e4c\u9f99\u8336 \u6709\u673a\u8336 \u7279\u7ea7\u9999\u6d53 \u897f\u5ca9\u5c71\u8336 \u9ad8\u5c71\u8336 500g \u8336\u738b","thumb":"http:\/\/demo.yunzshop.com\/attachment\/images\/sz_yi\/3\/2016\/12\/N7rzsBSNpI87i643P61735785ziLSl.jpg","marketprice":"290.00","productprice":"0.00","optiontitle":"","optionid":"0","specs":"","option_ladders":""},{"id":"331","total":"1","isopenchannel":"0","isforceyunbi":"0","yunbi_deduct":"0.00","goodsid":"253","stock":"48062","optionstock":"","maxbuy":"0","usermaxbuy":"0","title":"\u7279\u60e0\u5305\u90ae \u6e56\u5357\u5b89\u5316\u9ed1\u8336 \u6b63\u54c1\u767d\u6c99\u6eaa\u91d1\u82b1\u624b\u538b\u832f\u7816\u5929\u5c16\u8336 \u5929\u832f\u83361kg","thumb":"http:\/\/demo.yunzshop.com\/attachment\/images\/sz_yi\/3\/2016\/12\/lQ2WEw382SE4a7qae89wq334294I8W.jpg","marketprice":"390.00","productprice":"0.00","optiontitle":"","optionid":"0","specs":"","option_ladders":""}],"totalprice":"838.00","difference":"","ischannelpay":"0","verify_goods_ischannelpick":"","verify_goods_ischannelpay":"","virtual_currency":"1","yunbi_title":""}}';
    $result = json_encode($value);
    if($value2 === $result){
        dump($value2);

    }
    return $result;
}
if (!function_exists("getExitInfo")) {

    function getExitInfo()
    {
        function shutdown_find_exit()
        {
            ddump($GLOBALS['dbg_stack']);
        }

        register_shutdown_function('shutdown_find_exit');
        function write_dbg_stack()
        {
            $GLOBALS['dbg_stack'] = debug_backtrace();
        }

        register_tick_function('write_dbg_stack');
        declare(ticks = 1);
    }
}