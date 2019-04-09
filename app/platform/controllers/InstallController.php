<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/4/2
 * Time: 13:36
 */

namespace app\platform\controllers;


use \Illuminate\Support\Facades\DB;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\user\models\AdminUser;

class InstallController
{
    public $user_txt;

    public function __construct()
    {
        $this->user_txt = base_path().'/app/platform/controllers/user.txt';
    }

    public function agreement()
    {
        $file = base_path().'/manifest.xml';
        $con = file_get_contents($file);

        $xmlTag = 'version';
        preg_match_all("/<".$xmlTag.">.*<\/".$xmlTag.">/", $con, $temp);

        // 返回 ] 第一次出现的位置
        $first = strpos($temp[0][0],"]");
        // 返回 [ 最后一次出现的位置
        $end = strripos($temp[0][0],"[")+1;
        $version = substr($temp[0][0], $end, $first-$end);

        return $this->successJson('成功', $version);
    }

    /**
     * 运行环境检测
     */
    public function check()
    {
        // 服务器操作系统
        $ret['server_os'] = php_uname();
        // 服务器域名
        $ret['server_name'] =  $_SERVER['SERVER_NAME'];
        // PHP版本
        $ret['php_version'] = PHP_VERSION;
        // 程序安装目录
        $ret['servier_dir'] = base_path();
        // 磁盘剩余空间
        if(function_exists('disk_free_space')) {
            $ret['server_disk'] = floor(disk_free_space(base_path()) / (1024*1024)).'M';
        } else {
            $ret['server_disk'] = 'unknow';
        }
        // 上传限制
        $ret['upload_size'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';

        $server_info = [
            [
                'name' => '服务器操作系统',
                'value' => $ret['server_os'],
            ],
            [
                'name' => '服务器域名',
                'value' => $ret['server_name'],
            ],
            [
                'name' => 'PHP版本',
                'value' => $ret['php_version'],
            ],
            [
                'name' => '程序安装目录',
                'value' => $ret['servier_dir'],
            ],
            [
                'name' => '磁盘剩余空间',
                'value' => $ret['server_disk'],
            ],
            [
                'name' => '上传限制大小',
                'value' => $ret['upload_size'],
            ],
        ];

        // 检测PHP版本必须为5.6
        $ret['php_version_remark'] = true;
        if(version_compare(PHP_VERSION, '5.6.0') == -1 && version_compare(PHP_VERSION, '5.7') == 1 ) {
            $ret['php_version_remark'] = false;
        }
        // 检测 cURL
        $ret['php_curl'] = extension_loaded('curl') && function_exists('curl_init');
        // cURL 版本
        $ret['php_curl_version'] = curl_version()['version'];
        // 检测 PDO
        $ret['php_pdo'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
        // 检测 openSSL
        $ret['php_openssl'] = extension_loaded('openssl');
        // 检测 GD
        $ret['php_gd'] = extension_loaded('gd');
        // GD 库版本
        $ret['php_gd_version'] = gd_info()['GD Version'];
        // 检测 session.auto_start开启
        $ret['php_session_auto_start'] = strtolower(ini_get('session.auto_start')) == '0' || 'off' ? true : false;
        // 检测 json
        $ret['json'] = extension_loaded('json') && function_exists('json_decode') && function_exists('json_encode');

        $sysytem_environment = [
            [
                'name' => 'PHP版本',
                'need' => '5.6',
                'optimum' => '5.6',
                'check' => $ret['php_version_remark'],
                'value' => $ret['php_version'],
            ],
            [
                'name' => 'cURL',
                'need' => '支持',
                'optimum' => '无限制',
                'check' => $ret['php_curl'],
                'value' => $ret['php_curl_version'],
            ],
            [
                'name' => 'PDO',
                'need' => '支持',
                'optimum' => '无限制',
                'check' => $ret['php_pdo'],
                'value' => $ret['php_pdo'] ? '支持' : '不支持',
            ],
            [
                'name' => 'openSSL',
                'need' => '支持',
                'optimum' => '无限制',
                'check' => $ret['php_openssl'],
                'value' => $ret['php_openssl'] ? '支持' : '不支持',
            ],
            [
                'name' => 'GD',
                'need' => '支持',
                'optimum' => 'GD2',
                'check' => $ret['php_gd'],
                'value' => $ret['php_gd_version'],
            ],
            [
                'name' => 'session.auto_start',
                'need' => '不支持',
                'optimum' => '不支持',
                'check' => $ret['php_session_auto_start'],
                'value' => $ret['php_session_auto_start'] ? '支持' : '不支持',
            ],
            [
                'name' => 'json',
                'need' => '支持',
                'optimum' => '无限制',
                'check' => $ret['json'],
                'value' => $ret['json'] ? '支持' : '不支持',
            ],
        ];

        // 检测 mysql_connect
        $ret['mysql_connect'] = function_exists('mysql_connect');
        // 检测 file_get_content
        $ret['file_get_content'] = function_exists('file_get_contents');
        // 检测 exec
        $ret['exec'] = function_exists('exec');

        $check_function = [
            [
                'name' => 'mysql_connect',
                'need' => '支持',
                'value' => $ret['mysql_connect'] ? '支持' : '不支持',
            ],
            [
                'name' => 'file_get_content',
                'need' => '支持',
                'value' => $ret['file_get_content'] ? '支持' : '不支持',
            ],
            [
                'name' => 'exec',
                'need' => '支持',
                'value' => $ret['exec'] ? '支持' : '不支持',
            ],
        ];


        return $this->successJson('成功', [
            'server_info'=> $server_info,
            'sysytem_environment' => $sysytem_environment,
            'check_function' => $check_function
        ]);
    }

    /**
     * 文件权限设置
     */
    public function filePower()
    {
        $check_filename = [
            '/bootstrap',
            '/storage/framework',
            '/storage/logs'
        ];

        $check = [
            base_path() . '/bootstrap/cache',
            base_path() . '/storage/framework/view',
            base_path() . '/storage/logs',
        ];

        $ret = [];
        for($i=0; $i<count($check); $i++) {
            $ret[$i]['name'] =  $check_filename[$i];
            $ret[$i]['need'] =  '可写';
            $ret[$i]['value'] = (bool)$this->check_writeable($check[$i]);
        }

        return $this->successJson('成功', $ret);
    }

    /**
     * 检查目录是否有可读可写的权限
     * @param $dir
     * @return int
     */
    private function check_writeable($dir) {
        $writeable = 0;
        if(!is_dir($dir)) {
            @mkdir($dir, 0777);
        }
        if(is_dir($dir)) {
            if($fp = fopen("$dir/test.txt", 'w+')) {
                fclose($fp);
                unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     * 账号设置
     */
    public function setInformation()
    {
        $set = request()->set;
        $user = request()->user;

        $filename = base_path().'/.env';
        $env = file_get_contents($filename);

        foreach ($set as $item=>$value) {
            // 获取键第一次出现位置
            $check_env_key = strpos($env, $item);
            // 获取键后面 \n 换行符第一次出现位置
            $check_env_value = strpos($env, "\n", $check_env_key);
            // 得出两个位置之间的内容进行替换
            $num = $check_env_value - $check_env_key;
            if ((bool)$check_env_key) {
                $env = substr_replace($env, "{$item}={$value}", $check_env_key, $num);
            } else {
                $env .= "{$item}=$value\n";
            }
        }

        $result = file_put_contents($filename, $env);

        if (!$result) {
            return $this->errorJson('保存mysql配置数据有误');
        }

        try{
            new \PDO("mysql:host=".$set['DB_HOST'].";dbname=".$set['DB_DATABASE'].";post=".$set['DB_PORT'], $set['DB_USERNAME'], $set['DB_PASSWORD']);
        }catch (\Exception $e){
            return $this->errorJson($e->getMessage());
        }

        fopen($this->user_txt, 'w+');
        file_put_contents($this->user_txt, serialize($user));

        return $this->successJson('成功');
    }

    /**
     * 创建数据
     */
    public function createData()
    {
        ini_set('max_execution_time','0');

        include_once base_path() . '/sql.php';

//        try{
//            exec('php artisan migrate',$result); //执行命令
//        }catch (\Exception $e) {
//            $e->getMessage();
//        }

        $filesystem = app(\Illuminate\Filesystem\Filesystem::class);
        $update     = new \app\common\services\AutoUpdate(null, null, 300);
        $plugins_dir = $update->getDirsByPath('plugins', $filesystem);
        if (!empty($plugins_dir)) {
            try{
                \Artisan::call('update:version', ['version' => $plugins_dir]);
            }catch (\Exception $e) {
                return $this->errorJson($e->getMessage());
            }
        }

        $user_model = new AdminUser;
        if ($user_model->find(1)) {
            return $this->successJson('成功');
        }
        // 取出账号设置的信息
        $user = unserialize(file_get_contents($this->user_txt));

        // 保存站点名称
        $site_name = SystemSetting::settingSave($user['name'], 'copyright', 'system_copyright');
        if (!$site_name) {
            return $this->errorJson('失败', '');
        }

        // 保存超级管理员信息
        if (!$user['username'] || !$user['password']) {
            return $this->errorJson('用户名或密码不能为空');
        } elseif ($user['password'] !== $user['repassword']) {
            return $this->errorJson('两次密码不一致');
        }
        $user['password'] = bcrypt($user['password']);
        unset($user['name']);
        unset($user['repassword']);
        $user_model->fill($user);

        if (!$user_model->save()) {
            $this->errorJson('创建数据失败');
        }

        @unlink(base_path().'/app/platform/controllers/user.txt');

        fopen(base_path()."/bootstrap/install.lock", "w+");
        return $this->successJson('成功');
    }

    private function successJson($message = '成功', $data = [])
    {
        return response()->json([
            'result' => 1,
            'msg' => $message,
            'data' => $data
        ], 200, ['charset' => 'utf-8']);
    }

    private function errorJson($message = '失败', $data = [])
    {
        return response()->json([
            'result' => 0,
            'msg' => $message,
            'data' => $data
        ], 200, ['charset' => 'utf-8']);
    }

    public function delete()
    {
        @unlink(base_path().'/app/platform/controllers/InstallController.php');
    }
}
