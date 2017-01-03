<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'qcloudcos' . DIRECTORY_SEPARATOR . 'auth.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'qcloudcos' . DIRECTORY_SEPARATOR . 'conf.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'qcloudcos' . DIRECTORY_SEPARATOR . 'cosapi.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'qcloudcos' . DIRECTORY_SEPARATOR . 'http_client.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'qcloudcos' . DIRECTORY_SEPARATOR . 'slice_uploading.php');

use qcloudcos\Cosapi;
if (!class_exists('TencentImageModel')) {


    class TencentImageModel extends PluginModel
    {
        private function check_remote_file_exists($url)
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_NOBODY, true);
            $result = curl_exec($curl);
            $found = false;
            if ($result !== false) {
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($statusCode == 200) {
                    $found = true;
                }
            }
            curl_close($curl);
            return $found;
        }

        public function save($url, $config)
        {
            set_time_limit(0);
            if (empty($url)) {
                return '';
            }
            $ext = strrchr($url, ".");
            if ($ext != ".jpeg" && $ext != ".gif" && $ext != ".jpg" && $ext != ".png") {
                return "";
            }
            $filename = random(30) . $ext;
            if (!$this->check_remote_file_exists($url)) {
                return "";
            }
            Cosapi::setTimeout(180);

            Cosapi::setRegion('gz');

            $contents = @file_get_contents($url);
            $storename = file_put_contents(IA_ROOT . "/addons/sz_yi/data/".$filename, $contents);
            $bucket = 'yunzshop';
            $src = IA_ROOT . "/addons/sz_yi/data/".$filename;
            $dst = '/images/' . $filename;
            $folder = '/images/';

            // Create folder in bucket.
            $ret = Cosapi::createFolder($bucket, $folder);

            // Upload file into bucket.
            $ret = Cosapi::upload($bucket, $src, $dst);
            //return 'http://' . trim($config['url']) . "/" . $ret['key'];
        }

        function getConfig()
        {
            $config = array(
                'upload' => 0
            );
            $set = $this->getSet();
            $set['admin'] = m('cache')->getArray('qiniu', 'global');
            if (isset($set['admin']) && is_array($set['admin'])) {
                $config = $set['admin'];
            }
            if ($set['admin']['allow'] == 1) {
                if (isset($set['user']) && is_array($set['user'])) {
                    $config = $set['user'];
                }
            }
            if (empty($config['upload'])) {
                return false;
            }
            return $config;
        }

        function perms()
        {
            return array(
                'qiniu' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'admin' => '万象优图设置-log'
                )
            );
        }
    }
}