<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
if (!$_W['isfounder']) {
    message('无权访问!');
}
 
$op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
load()->func('communication');
load()->func('file');
if ($op == 'display') {
    //先看是否注册，没注册的要注册
    define('CLOUD_URL', 'http://115.29.33.155/web/index.php?c=account&a=register');
    $data['domain'] = $_SERVER['HTTP_HOST'];
    $data['signature'] = 'sz_cloud_register';
    $res = ihttp_request(CLOUD_URL, $data);
    if(!$res){
        exit('通讯失败,请检查网络');
    }
    $content = json_decode($res['content'], 1);
    if(!$content['status']){
        exit($content['msg']);
    }

    $versionfile = IA_ROOT . '/addons/sz_yi/version.php';
    $updatedate  = date('Y-m-d H:i', filemtime($versionfile));
    $version     = SZ_YI_VERSION;
} else if ($op == 'check') {
    set_time_limit(0); 
    $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
    define('CLOUD_UPGRADE_URL', 'http://115.29.33.155/web/index.php?c=account&a=upgrade');
    $data['version'] = SZ_YI_VERSION;
    $data['method'] = 'upgrade';
    $res = ihttp_post(CLOUD_UPGRADE_URL, $data);
    $res = json_decode($res['content'], 1);
    if(!$res){
        die(json_encode(array('result' => 0, 'message' =>'通讯失败,请检查网络' . ". ")));
    }

    $filecount = 1;
    if($res['msg'] == 'new'){
        $filecount = 0; 
        die(json_encode(array('result' => 0, 'message' =>'恭喜您！已经是最新版本' . ". ")));
    }

    $newVersion = array_pop($res);
    $newVersion = $newVersion['version'];

    $log = '';
    die(json_encode(array(
            'result' => 1,
            'version' => $newVersion, 
            'filecount' => 2,//count($files),
            'upgrade' => false,
            'log' =>  str_replace("\r\n","<br/>", $log)
        )));
    /*
    $resp    = ihttp_post(SZ_YI_AUTH_URL, array(
        'type' => 'check',
        'ip' => $auth['ip'],
        'id' => $auth['id'],
        'code' => $auth['code'],
        'domain' => $auth['domain'],
        'version' => $version,
        'manual'=>1
    )); 
    $templatefiles = "";
    $ret = @json_decode($resp['content'], true);
    if (is_array($ret)) {
	  $templatefiles = "";
        if ($ret['result'] == 1) {
            $files = array();
            if (!empty($ret['files'])) {
                foreach ($ret['files'] as $file) {
                    $entry = IA_ROOT . "/addons/sz_yi/" . $file['path'];
                    if (!is_file($entry) || md5_file($entry) != $file['hash']) {

                        $files[] = array('path' => $file['path'], 'download' => 0);
						
		     if( is_file($entry) && strexists($entry, 'template/mobile') && strexists($entry, '.html') ){
			  $templatefiles.= "/".$file['path']."\r\n";
		     }
                    }
                } 
            } 
           cache_write('cloud:modules:upgrade', array('files'=>$files,'version'=>$ret['version'],'upgrade'=>$ret['upgrade']));
           $log = base64_decode($ret['log']);
           if(!empty($templatefiles)){
		 
	        $log="<br/><b>模板变化:</b><br/>".$templatefiles."\r\n".$log;
           }
            die(json_encode(array(
                'result' => 1,
                'version' => $ret['version'], 
                'filecount' => count($files),
                'upgrade' => !empty($ret['upgrade']),
                'log' =>  str_replace("\r\n","<br/>", $log)
            )));
        }
    }
    die(json_encode(array('result' => 0, 'message' =>$resp['content'] . ". ")));
     */
} else if ($op == 'download') {
	//更新版本
    define('CLOUD_UPGRADE_URL', 'http://115.29.33.155/web/index.php?c=account&a=upgrade');
    $data['version'] = SZ_YI_VERSION;
    $data['method'] = 'upgrade';
    $res = ihttp_request(CLOUD_UPGRADE_URL, $data);
    //print_r($res);
    if(!$res){
        die(json_encode(array('result' => 0, 'msg' => '通讯失败,请检查网络')));
    }
    $res = json_decode($res['content'], 1);
    if($res['msg'] == 'new'){
        die(json_encode(array('result' => 0, 'msg' => '已经是最新程序')));
    }

    foreach($res as $v){
        if($v['version'] == SZ_YI_VERSION){
            continue;
        }
        $filename = 'http://115.29.33.155/data/upgrade_zip/'.$v['version'].'.zip';
        curl_download($filename, IA_ROOT. '/addons/sz_yi/upgrade.zip');

        $zip = new ZipArchive; 
        $res = $zip->open(IA_ROOT. '/addons/sz_yi/upgrade.zip'); 
        if ($res === TRUE) { 
            //chmod_dir(IA_ROOT. '/addons/sz_yi/', '0755');
            //解压缩到文件夹 
            $zip->extractTo(IA_ROOT.'/addons'); 
            $zip->close(); 
            //echo "更新版本{$v['version']}成功<br>";
            //die(json_encode(array('result' => 1, 'total' => count($files), 'success' => $success)));
            $version = file_get_contents(IA_ROOT .'/addons/sz_yi/version.php');
            $v = preg_replace('/define\(\'SZ_YI_VERSION\', \'(.+)\'\)/', 'define(\'SZ_YI_VERSION\', \''.$v['version'].'\')',$version);
            file_put_contents(IA_ROOT .'/addons/sz_yi/version.php', $v);

        } else { 
            die(json_encode(array('result' => 0, 'msg' => '解压失败')));
        } 
    }
    die(json_encode(array('result' => 2)));
} else if ($op == 'checkversion') {
	
	file_put_contents(IA_ROOT . "/addons/sz_yi/version.php", "<?php if(!defined('IN_IA')) {exit('Access Denied');}if(!defined('SZ_YI_VERSION')) {define('SZ_YI_VERSION', '1.0');}");
	header('location: '.$this->createWebUrl('upgrade'));
	exit;	 
	
}
include $this->template('web/sysset/upgrade');
