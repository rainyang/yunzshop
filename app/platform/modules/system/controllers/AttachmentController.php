<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/28
 * Time: 10:06
 */
namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;

class AttachmentController extends BaseController
{
    public function index()
    {
        $post_max_size = ini_get('post_max_size');
        $post_max_size = $post_max_size > 0 ? $this->bytecount($post_max_size) / 1024 : 0;
        $upload_max_filesize = ini_get('upload_max_filesize');
        $global = SystemSetting::where('key', 'global')->pluck('value');
        $remote = SystemSetting::where('key', 'remote')->pluck('value');
        $set_data = request()->upload;
        $alioss = request()->alioss;
        $cos = request()->cos;
        if ($set_data) {
            $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');

            $set_data['thumb_width'] = intval(trim($set_data['width']));

            if ($set_data['thumb'] && !$set_data['thumb_width']) {
                echo '<script type="text/javascript"> alert("请设置图片缩略宽度"); location.href="/index.php/admin/system/attachment"; </script>';
            }

            if (!$set_data['image_extentions']) {
                echo '<script type="text/javascript"> alert("请添加支持的图片附件后缀类型"); location.href="/index.php/admin/system/attachment"; </script>';
            }
            if (!empty($set_data['image_extentions'])) {
                $set_data['image_extentions'] = explode("\n", $set_data['image_extentions']);
                foreach ($set_data['image_extentions'] as $key => &$row) {
                    $row = trim($row);
                    if (in_array($row, $harmtype)) {
                        unset($set_data['image_extentions'][$key]);
                        continue;
                    }
                }
            }
            if (!$set_data['image_extentions']['0']) {
                echo '<script type="text/javascript"> alert("请添加支持的图片附件后缀类型"); location.href="/index.php/admin/system/attachment"; </script>';
            }

            $set_data['image_limit'] = max(0, min(intval(trim($set_data['image_limit'])), $post_max_size));
            if (!$set_data['image_limit']) {
                echo '<script type="text/javascript"> alert("请设置图片上传支持的文件大小, 单位 KB."); location.href="/index.php/admin/system/attachment"; </script>';
            }

            $zip_percentage = intval($set_data['zip_percentage']);
            if ($zip_percentage <= 0 || $zip_percentage > 100) {
                $set_data['image']['zip_percentage'] = 100;
            }

            if (!$set_data['audio_extentions']) {
                echo '<script type="text/javascript"> alert("请添加支持的图片附件后缀类型"); location.href="/index.php/admin/system/attachment"; </script>';
            }
            if (!empty($set_data['audio_extentions'])) {
                $set_data['audio_extentions'] = explode("\n", $set_data['audio_extentions']);
                foreach ($set_data['audio_extentions'] as $key => &$row) {
                    $row = trim($row);
                    if (in_array($row, $harmtype)) {
                        unset($set_data['audio_extentions'][$key]);
                        continue;
                    }
                }
            }
            if (!$set_data['audio_extentions']['0']) {
                echo '<script type="text/javascript"> alert("请添加支持的音频视频附件后缀类型"); location.href="/index.php/admin/system/attachment"; </script>';
            }

            $set_data['audio_limit'] = max(0, min(intval(trim($set_data['audio_limit'])), $post_max_size));
            if (empty($set_data['audio_limit'])) {
                echo '<script type="text/javascript"> alert("请设置音频视频上传支持的文件大小, 单位 KB."); location.href="/index.php/admin/system/attachment"; </script>';
            }

            $set_data = \GuzzleHttp\json_encode($set_data);
            if ($global->isEmpty()) {
                // 添加
                $system_setting = SystemSetting::create([
                    'key' => 'global',
                    'value' => $set_data
                ]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/attachment', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/attachment', '失败', 'failed');
                }
            } else {
                // 修改
                $system_setting = SystemSetting::where('key', 'global')->update(['value' => $set_data]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/attachment', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/attachment', '失败', 'failed');
                }
            }

            //  缩略设置 thumb
            //  缩略宽度 thumb_width
            //  支持图片后缀 image_extentions
            //  支持图片大小 image_limit
            //  图片压缩 zip_percentage
            //  支持音频视频后缀 audio_extentions
            //  支持音频视频大小 audio_limit
        }

        $config['image_extentions'] = ['0' => 'gif', '1' => 'jpg', '2' => 'jpeg', '3' => 'png'];
        $config['image_limit'] = 5000;
        $config['audio_extentions'] = ['0' => 'mp3'];
        $config['audio_limit'] = 5000;

        if ($global->isEmpty()) {
            $global = $config;
        }

        $global = json_decode($global['0']);
        $remote = json_decode($remote['0']);

        $global->thumb_width = intval($global->thumb_width);
        if (!$global->thumb_width) {
            $global->thumb_width = 800;
        }

        if ($global->image_extentions['0']) {
            $global->image_extentions = implode("\n", $global->image_extentions);
        }

        if ($global->audio_extentions['0']) {
            $global->audio_extentions = implode("\n", $global->audio_extentions);
        }

        if (!$global->zip_percentage) {
            $global->zip_percentage = 100;
        }

        if ($alioss || $cos) {
            $remotes = array(
                'type' => intval(request()->type),
                'alioss' => array(
                    'key' => $alioss['key'],
                    'secret' => !(strpos($alioss['secret'], '*') === FALSE) ? $remote['alioss']['secret'] : $alioss['secret'],
                    'bucket' => $alioss['bucket'],
                    'internal' => $alioss['internal']

                    // Access Key ID: key
                    // Access Key Secret: secret
                    // 是否内网上传: internal(0: 否; 1: 是)
                    // Bucket选择: bucket
                    // 自定义URL: url
                ),
                'cos' => array(
                    'appid' => trim($cos['appid']),
                    'secretid' => trim($cos['secretid']),
                    'secretkey' => !(strpos(trim($cos['secretkey']), '*') === FALSE) ? $remote['cos']['secretkey'] : trim($cos['secretkey']),
                    'bucket' => trim($cos['bucket']),
                    'local' => trim($cos['local']),
                    'url' => trim($cos['url'])

                    // APPID: appid
                    // SecretID: secretid
                    // SecretKEY: secretkey
                    // Bucket: bucket
                    // bucket所在区域: local
                    // Url: url
                )
            );

            if ($remotes['type'] == '2') {
                if (trim($remotes['alioss']['key']) == '') {
                    echo '<script type="text/javascript"> alert("阿里云OSS-Access Key ID不能为空"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                if (trim($remotes['alioss']['secret']) == '') {
                    echo '<script type="text/javascript"> alert("阿里云OSS-Access Key Secret不能为空"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                $buckets = $this->attachment_alioss_buctkets($remotes['alioss']['key'], $remotes['alioss']['secret']);
                if ($this->is_error($buckets)) {
                    echo '<script type="text/javascript"> alert("OSS-Access Key ID 或 OSS-Access Key Secret错误，请重新填写"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                list($remotes['alioss']['bucket'], $remotes['alioss']['url']) = explode('@@', $alioss['bucket']);
                if (!$buckets[$remotes['alioss']['bucket']]) {
                    echo '<script type="text/javascript"> alert("Bucket不存在或是已经被删除"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                $remotes['alioss']['url'] = 'http://' . $remotes['alioss']['bucket'] . '.' . $buckets[$remotes['alioss']['bucket']]['location'] . '.aliyuncs.com';
                $remotes['alioss']['ossurl'] = $buckets[$remotes['alioss']['bucket']]['location'] . '.aliyuncs.com';
                if ($alioss['url']) {
                    $url = trim($alioss['url'], '/');
                    if (!strexists($url, 'http://') && !strexists($url, 'https://')) {
                        $url = 'http://' . $url;
                    }
                    $remotes['alioss']['url'] = $url;
                }
            } elseif ($remotes['type'] == '4') {
                if (!$remotes['cos']['appid']) {
                    echo '<script type="text/javascript"> alert("Bucket不存在或是已经被删除"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                if (!$remotes['cos']['secretid']) {
                    echo '<script type="text/javascript"> alert("请填写SECRETID"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                if (!$remotes['cos']['secretkey']) {
                    echo '<script type="text/javascript"> alert("请填写SECRETKEY"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }

                if (!$remotes['cos']['bucket']) {
                    echo '<script type="text/javascript"> alert("请填写BUCKET"); location.href="/index.php/admin/system/attachment"; </script>';
                    exit;
                }
                $remotes['cos']['bucket'] = str_replace("-{$remotes['cos']['appid']}", '', trim($remotes['cos']['bucket']));

                if (!$url) {
                    $url = sprintf('https://%s-%s.cos%s.myqcloud.com', $bucket, $appid, $_GPC['local']);
                }

                if (!$remotes['cos']['url']) {
                    $remotes['cos']['url'] = sprintf('https://%s-%s.cos%s.myqcloud.com', $remotes['cos']['bucket'], $remotes['cos']['appid'], $remotes['cos']['local']);
                }
                $remotes['cos']['url'] = rtrim($remotes['cos']['url'], '/');
                $auth = $this->attachment_cos_auth($remotes['cos']['bucket'], $remotes['cos']['appid'], $remotes['cos']['secretid'], $remotes['cos']['secretkey'], $remotes['cos']['local']);

                if ($this->is_error($auth)) {
                    $message = $auth['message'];
                    echo "<script type='text/javascript'> alert('$message'); location.href='/index.php/admin/system/attachment'; </script>";
                    exit;
                }
            }

            $set_data = \GuzzleHttp\json_encode($set_data);
            if ($global->isEmpty()) {
                // 添加
                $system_setting = SystemSetting::create([
                    'key' => 'global',
                    'value' => $set_data
                ]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/attachment', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/attachment', '失败', 'failed');
                }
            } else {
                // 修改
                $system_setting = SystemSetting::where('key', 'global')->update(['value' => $set_data]);
                if ($system_setting) {
                    return $this->commonRedirect('/admin/system/attachment', '成功');
                } else {
                    return $this->commonRedirect('/admin/system/attachment', '失败', 'failed');
                }
            }


        }

        return view('system.attachment', [
            'upload' => $global,
            'post_max_size' => $post_max_size,
            'upload_max_filesize' => $upload_max_filesize,
            'remote' => $remote
        ]);
    }


    public function bytecount($str)
    {
        if (strtolower($str[strlen($str) -1]) == 'b') {
            $str = substr($str, 0, -1);
        }
        if(strtolower($str[strlen($str) -1]) == 'k') {
            return floatval($str) * 1024;
        }
        if(strtolower($str[strlen($str) -1]) == 'm') {
            return floatval($str) * 1048576;
        }
        if(strtolower($str[strlen($str) -1]) == 'g') {
            return floatval($str) * 1073741824;
        }
    }


    public function attachment_alioss_buctkets($key, $secret)
    {
        $this->load()->library('oss');
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

    public function load()
    {
        static $loader;
        if(empty($loader)) {
            $loader = new \app\platform\modules\system\models\Loader;
        }
        return $loader;
    }

    public function is_error($data)
    {
        if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
            return false;
        } else {
                return true;
        }
    }

    public function error($errno, $message = '')
    {
        return array(
            'errno' => $errno,
            'message' => $message,
        );
    }

    public function attachment_cos_auth($bucket,$appid, $key, $secret, $bucket_local = '') {
        if (!is_numeric($appid)) {
            return $this->error(-1, '传入appid值不合法, 请重新输入');
        }
        if (!preg_match('/^[a-zA-Z0-9]{36}$/', $key)) {
            return $this->error(-1, '传入secretid值不合法，请重新传入');
        }
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $secret)) {
            return $this->error(-1, '传入secretkey值不合法，请重新传入');
        }
        if (!empty($bucket_local)) {
            $con = $original = @file_get_contents(IA_ROOT.'/framework/library/cosv4.2/qcloudcos/conf.php');
            if (empty($con)) {
                $conf_content = base64_decode("PD9waHANCg0KbmFtZXNwYWNlIHFjbG91ZGNvczsNCg0KY2xhc3MgQ29uZiB7DQogICAgLy8gQ29zIHBocCBzZGsgdmVyc2lvbiBudW1iZXIuDQogICAgY29uc3QgVkVSU0lPTiA9ICd2NC4yLjInOw0KICAgIGNvbnN0IEFQSV9DT1NBUElfRU5EX1BPSU5UID0gJ2h0dHA6Ly9yZWdpb24uZmlsZS5teXFjbG91ZC5jb20vZmlsZXMvdjIvJzsNCg0KICAgIC8vIFBsZWFzZSByZWZlciB0byBodHRwOi8vY29uc29sZS5xY2xvdWQuY29tL2NvcyB0byBmZXRjaCB5b3VyIGFwcF9pZCwgc2VjcmV0X2lkIGFuZCBzZWNyZXRfa2V5Lg0KICAgIGNvbnN0IEFQUF9JRCA9ICcnOw0KICAgIGNvbnN0IFNFQ1JFVF9JRCA9ICcnOw0KICAgIGNvbnN0IFNFQ1JFVF9LRVkgPSAnJzsNCg0KICAgIC8qKg0KICAgICAqIEdldCB0aGUgVXNlci1BZ2VudCBzdHJpbmcgdG8gc2VuZCB0byBDT1Mgc2VydmVyLg0KICAgICAqLw0KICAgIHB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZ2V0VXNlckFnZW50KCkgew0KICAgICAgICByZXR1cm4gJ2Nvcy1waHAtc2RrLScgLiBzZWxmOjpWRVJTSU9OOw0KICAgIH0NCn0NCg==");
                file_put_contents(IA_ROOT.'/framework/library/cosv4.2/qcloudcos/conf.php', $conf_content);
                $con = $original = $conf_content;
            }
            $con = preg_replace('/const[\s]APP_ID[\s]=[\s]\'.*\';/', 'const APP_ID = \''.$appid.'\';', $con);
            $con = preg_replace('/const[\s]SECRET_ID[\s]=[\s]\'.*\';/', 'const SECRET_ID = \''.$key.'\';', $con);
            $con = preg_replace('/const[\s]SECRET_KEY[\s]=[\s]\'.*\';/', 'const SECRET_KEY = \''.$secret.'\';', $con);
            file_put_contents(IA_ROOT.'/framework/library/cosv4.2/qcloudcos/conf.php', $con);
            load()->library('cos');
            qcloudcos\Cosapi :: setRegion($bucket_local);
            qcloudcos\Cosapi :: setTimeout(180);
            $uploadRet = qcloudcos\Cosapi::upload($bucket, ATTACHMENT_ROOT.'images/global/MicroEngine.ico', '/MicroEngine.ico','',3 * 1024 * 1024, 0);
        } else {
            load()->library('cosv3');
            $con = $original = @file_get_contents(IA_ROOT.'/framework/library/cos/Qcloud_cos/Conf.php');
            if (empty($con)) {
                $conf_content = base64_decode("PD9waHANCm5hbWVzcGFjZSBRY2xvdWRfY29zOw0KDQpjbGFzcyBDb25mDQp7DQogICAgY29uc3QgUEtHX1ZFUlNJT04gPSAndjMuMyc7DQoNCiAgICBjb25zdCBBUElfSU1BR0VfRU5EX1BPSU5UID0gJ2h0dHA6Ly93ZWIuaW1hZ2UubXlxY2xvdWQuY29tL3Bob3Rvcy92MS8nOw0KICAgIGNvbnN0IEFQSV9WSURFT19FTkRfUE9JTlQgPSAnaHR0cDovL3dlYi52aWRlby5teXFjbG91ZC5jb20vdmlkZW9zL3YxLyc7DQogICAgY29uc3QgQVBJX0NPU0FQSV9FTkRfUE9JTlQgPSAnaHR0cDovL3dlYi5maWxlLm15cWNsb3VkLmNvbS9maWxlcy92MS8nOw0KICAgIC8v6K+35YiwaHR0cDovL2NvbnNvbGUucWNsb3VkLmNvbS9jb3Pljrvojrflj5bkvaDnmoRhcHBpZOOAgXNpZOOAgXNrZXkNCiAgICBjb25zdCBBUFBJRCA9ICcnOw0KICAgIGNvbnN0IFNFQ1JFVF9JRCA9ICcnOw0KICAgIGNvbnN0IFNFQ1JFVF9LRVkgPSAnJzsNCg0KDQogICAgcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRVQSgpIHsNCiAgICAgICAgcmV0dXJuICdjb3MtcGhwLXNkay0nLnNlbGY6OlBLR19WRVJTSU9OOw0KICAgIH0NCn0NCg0KLy9lbmQgb2Ygc2NyaXB0DQo=");
                file_put_contents(IA_ROOT.'/framework/library/cos/Qcloud_cos/Conf.php', $conf_content);
                $con = $original = $conf_content;
            }
            $con = preg_replace('/const[\s]APPID[\s]=[\s]\'.*\';/', 'const APPID = \''.$appid.'\';', $con);
            $con = preg_replace('/const[\s]SECRET_ID[\s]=[\s]\'.*\';/', 'const SECRET_ID = \''.$key.'\';', $con);
            $con = preg_replace('/const[\s]SECRET_KEY[\s]=[\s]\'.*\';/', 'const SECRET_KEY = \''.$secret.'\';', $con);
            file_put_contents(IA_ROOT.'/framework/library/cos/Qcloud_cos/Conf.php', $con);
            $uploadRet = Qcloud_cos\Cosapi::upload($bucket, ATTACHMENT_ROOT.'images/global/MicroEngine.ico', '/MicroEngine.ico','',3 * 1024 * 1024, 0);
        }
        if ($uploadRet['code'] != 0) {
            switch ($uploadRet['code']) {
                case -62:
                    $message = '输入的appid有误';
                    break;
                case -79:
                    $message = '输入的SecretID有误';
                    break;
                case -97:
                    $message = '输入的SecretKEY有误';
                    break;
                case -166:
                    $message = '输入的bucket有误';
                    break;
                case -133:
                    $message = '请确认你的bucket是否存在';
                    break;
                default:
                    $message = $uploadRet['message'];
            }
            if (empty($bucket_local)) {
                file_put_contents(IA_ROOT.'/framework/library/cos/Qcloud_cos/Conf.php', $original);
            } else {
                file_put_contents(IA_ROOT.'/framework/library/cosv4.2/qcloudcos/Conf.php', $original);
            }
            return error(-1, $message);
        }
        return true;
    }
}