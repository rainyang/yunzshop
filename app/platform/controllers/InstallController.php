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
    /**
     * 运行环境检测
     */
    public function check()
    {
        // 服务器操作系统
        $ret['server_os'] = php_uname();
        // 服务器域名
        $ret['server_name'] =  $_SERVER['SERVER_NAME'];
        // Web服务器环境
        $ret['server_environment'] = PHP_OS;
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
        $ret['php_session'] = strtolower(ini_get('session.auto_start')) == '0' || 'off' ? true : false;
        // 检测 json
        $ret['json'] = extension_loaded('json') && function_exists('json_decode') && function_exists('json_encode');



        // 检测 mysql_connect
        $ret['mysql_connect'] = function_exists('mysql_connect');
        // 检测 file_get_content
        $ret['file_get_content'] = function_exists('file_get_contents');
        // 检测 exec
        $ret['exec'] = function_exists('exec');


        return $this->successJson('成功', $ret);
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
            $ret[][$check_filename[$i]] = $this->check_writeable($check[$i]);
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
     * 创建数据
     */
    public function createData()
    {
        ini_set('max_execution_time','0');

        include_once base_path() . '/sql.php';

        exec('php artisan migrate',$result); //执行命令

        return $this->successJson('成功', $result);
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
            DB::connection('mysql')->getPdo();
        }catch (\Exception $e){
            return $this->errorJson($e->getMessage());
        }

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
        $user_model = new AdminUser;
        $user_model->fill($user);

        if ($user_model->save()) {
           return $this->successJson('创建数据成功');
        } else {
            return $this->errorJson('创建数据失败');
        }
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
}