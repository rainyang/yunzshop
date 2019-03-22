<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/28
 * Time: 10:06
 */

namespace app\platform\modules\system\controllers;


use app\platform\controllers\BaseController;
use app\platform\modules\system\models\Attachment;
use app\platform\modules\system\models\SystemSetting;

class AttachmentController extends BaseController
{
    public $remote;

    public function __construct()
    {

        $this->remote = SystemSetting::settingLoad('remote', 'system_remote');

    }

    public function globals()
    {
        $post_max_size = ini_get('post_max_size');
        $post_max_size = $post_max_size > 0 ? bytecount($post_max_size) / 1024 : 0;
        $upload_max_filesize = ini_get('upload_max_filesize');
        $global = SystemSetting::settingLoad('global', 'system_global');
        $set_data = request()->upload;

        if ($set_data) {
            $validate = $this->validate($this->rules(''), $set_data, $this->message());
            if ($validate) {
                return $validate;
            }
            $attach = Attachment::saveGlobal($set_data, $post_max_size);

            if ($attach['result']) {
                return $this->successJson('成功');
            } else {
                return $this->errorJson($attach['msg']);
            }
        }

        $config['image_extentions'] = ['0' => 'gif', '1' => 'jpg', '2' => 'jpeg', '3' => 'png'];
        $config['image_limit'] = 5000;
        $config['audio_extentions'] = ['0' => 'mp3'];
        $config['audio_limit'] = 5000;

        if (!$global) {
            $global = $config;
        }

        $global['thumb_width'] = intval($global['thumb_width']);
        if (!$global['thumb_width']) {
            $global['thumb_width'] = 800;
        }

        if ($global['image_extentions']['0']) {
            $global['image_extentions'] = implode("\n", $global['image_extentions']);
        }

        if ($global['audio_extentions']['0']) {
            $global['audio_extentions'] = implode("\n", $global['audio_extentions']);
        }

        if (!$global['zip_percentage']) {
            $global['zip_percentage'] = 100;
        }

        return $this->successJson('成功', [
            'global' => $global,
            'post_max_size' => $post_max_size,
            'upload_max_filesize' => $upload_max_filesize
        ]);
    }

    public function remote()
    {
        $alioss = request()->alioss;
        $cos = request()->cos;

        if ($alioss || $cos) {
            if ($alioss) {
                $validate  = $this->validate($this->rules(1), $alioss, $this->message());
            } else {
                $validate  = $this->validate($this->rules(2), $cos, $this->message());
            }
            if ($validate) {
                return $validate;
            }

            $attach = Attachment::saveRemote($alioss, $cos, $this->remote);

            if ($attach['result']) {
                return $this->successJson('成功');
            } else {
                return $this->errorJson($attach['msg']);
            }
        }

        return $this->successJson('成功', $this->remote);
    }

    public function validate(array $rules, \Request $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return $this->errorJson('失败', $validator->errors()->all());
        }
    }

    public function rules($param)
    {
        $rules = [];
        if (request()->path() == "admin/system/globals") {
            $rules = [
                'image_extentions' => 'required',
                'image_limit' => 'required',
                'audio_extentions' => 'required',
                'audio_limit' => 'required',
            ];
        }

        if ($param == 1) {
            $rules = [
                'key' => 'required',
                'secret' => 'required',
            ];
        } elseif ($param == 2) {
            $rules = [
                'appid' => 'required',
                'secretid' => 'required',
                'secretkey' => 'required',
                'bucket' => 'required',
            ];
        }

        return $rules;
    }

    public function message()
    {
        return [
            'image_extentions.required' => '图片后缀不能为空.',
            'image_limit.required' => '图片上传大小不能为空.',
            'audio_extentions.required' => '音频视频后缀不能为空.',
            'audio_limit.required' => '音频视频大小不能为空.',
            'key' => '阿里云OSS-Access Key ID不能为空',
            'secret' => '阿里云OSS-Access Key Secret不能为空',
            'appid' => '请填写APPID',
            'secretid' => '请填写SECRETID',
            'secretkey' => '请填写SECRETKEY',
            'bucket' => '请填写BUCKET'
        ];
    }

    public function bucket()
    {
        $key = request()->key;
        $secret = request()->secret;

        $buckets = attachment_alioss_buctkets($key, $secret);
        if (is_error($buckets)) {
            return $this->errorJson($buckets['message']);
        }

        $bucket_datacenter = attachment_alioss_datacenters();
        $bucket = array();
        foreach ($buckets as $key => $value) {
            $value['loca_name'] = $key. '@@'. $bucket_datacenter[$value['location']];
            $value['value'] = $key. '@@'. $value['location'];
            $bucket[] = $value;
        }

        return $this->successJson('成功', $bucket);
    }

    public function oss()
    {
        $alioss = request()->alioss;
        $secret = strexists($alioss['secret'], '*') ? $this->remote['alioss']['secret'] : $alioss['secret'];
        $buckets = attachment_alioss_buctkets($alioss['key'], $secret);
        list($bucket, $url) = explode('@@', $alioss['bucket']);

        $result = attachment_newalioss_auth($alioss['key'], $secret, $bucket, $alioss['internal']);
        if (is_error($result)) {
            return $this->error('OSS-Access Key ID 或 OSS-Access Key Secret错误，请重新填写');
        }
        $ossurl = $buckets[$bucket]['location'].'.aliyuncs.com';
        if ($alioss['url']) {
            if (!strexists($alioss['url'], 'http://') && !strexists($alioss['url'],'https://')) {
                $url = 'http://'. trim($alioss['url']);
            } else {
                $url = trim($alioss['url']);
            }
            $url = trim($url, '/').'/';
        } else {
            $url = 'http://'.$bucket.'.'.$buckets[$bucket]['location'].'.aliyuncs.com/';
        }
        $filename = 'MicroEngine.ico';
        $response = ihttp_request($url. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
        if (is_error($response)) {
            return $this->errorJson('配置失败，阿里云访问url错误');
        }
        if (intval($response['code']) != 200) {
            return $this->errorJson('配置失败，阿里云访问url错误,请保证bucket为公共读取的');
        }
        $image = getimagesizefromstring($response['content']);
        if ($image && strexists($image['mime'], 'image')) {
            return $this->successJson('配置成功');
        } else {
            return $this->errorJson('配置失败，阿里云访问url错误');
        }
    }

    public function cos()
    {
        $cos = request()->cos;

        $secretkey = strexists($cos['secretkey'], '*') ? $this->remote['cos']['secretkey'] : trim($cos['secretkey']);
        $bucket =  str_replace("-{$cos['appid']}", '', trim($cos['bucket']));

        if (!$cos['url']) {
            $cos['url'] = sprintf('https://%s-%s.cos%s.myqcloud.com', $bucket, $cos['appid'], $cos['local']);
        }
        $cos['url'] = rtrim($cos['url'], '/');
        $auth= attachment_cos_auth($bucket, $cos['appid'], $cos['secretid'], $secretkey, $cos['local']);

        if (is_error($auth)) {
            return $this->errorJson('配置失败，请检查配置' . $auth['message']);
        }
        $filename = 'MicroEngine.ico';
        $response = ihttp_request($cos['url']. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
        if (is_error($response)) {
            return $this->errorJson('配置失败，腾讯cos访问url错误');
        }
        if (intval($response['code']) != 200) {
            return $this->errorJson('配置失败，腾讯cos访问url错误,请保证bucket为公共读取的');
        }
        $image = getimagesizefromstring($response['content']);
        if ($image && strexists($image['mime'], 'image')) {
            return $this->successJson('配置成功');
        } else {
            return $this->errorJson('配置失败，腾讯cos访问url错误');
        }
    }
}