<?php
/**
 * Created by PhpStorm.
 * User: PhpStorm
 * Date: 2019/3/18
 * Time: 14:01
 */
namespace app\platform\modules\Application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\application\models\CoreAttach;
use app\common\services\qcloud\Api;
use app\common\services\aliyunoss\OssClient;
use app\common\services\aliyunoss\Core\OssException;

class AllUploadController extends BaseController
{
    protected $proto;
    protected $path;

    public function __construct()
    {
        $this->proto = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';

        $this->path = config('filesystems.disks.syst')['url'].'/'; //实际存放路径
    }

    public function doUpload()
    {
        $setting = SystemSetting::settingLoad();

        $file = request()->file('file');

        //文件大小是否大于所设置的文件最大值
        //文件上传最大执行时间
        $ext = $file->getClientOriginalExtension();

        if ($setting['set'] == 0) {
            //判断是否属于设置的类型
            $defaultImgType = [
                'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
                'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
            ];

            if (in_array($ext, $defaultImgType)) {

                $file_type = 'syst';

                $img_type = $setting['img_suffix_name'] ? explode('\r\n', $setting['img_suffix_name']) : $defaultImgType;

                if (!in_array($ext, $img_type) ) {
                    return $this->errorJson('文件格式不正确');
                }

                $defaultImgSize = $setting['img_size'] ? $setting['img_size'] : 10240;

                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('文件大小超出规定值');
                }

                if ($setting['compression_ratio']) {
                    //执行图片压缩
                }
            }

            //检查音视频文件
            $defaultAudioType = ['AVI', 'ASF', 'WMV', 'AVS', 'FLV', 'MKV', 'MOV', '3GP', 'MP4',
                'MPG', 'MPEG', 'DAT', 'OGM', 'VOB', 'RM', 'RMVB', 'TS', 'TP', 'IFO', 'NSV'];

            $defaultVideoType = [
                'MP3', 'AAC', 'WAV', 'WMA', 'CDA', 'FLAC', 'M4A', 'MID', 'MKA', 'MP2',
                'MPA', 'MPC', 'APE', 'OFR', 'OGG', 'RA', 'WV', 'TTA', 'AC3', 'DTS'];

            if (in_array($ext, $defaultAudioType) || in_array($ext, $defaultVideoType)) {

                $file_type = in_array($ext, $defaultVideoType) ? 'video' : 'audio';

                $img_type = $setting['av_suffix_name'] ? explode('\r\n', $setting['av_suffix_name']) : $defaultAudioType;

                if (!in_array($ext, $img_type) ) {
                    return $this->errorJson('文件格式不正确');
                }
                $defaultAudioSize = $setting['audio_limit'] ? $setting['audio_limit'] : 30702; //音视频最大 30M

                if ($file->getClientSize() > $defaultAudioSize) {
                    return $this->errorJson('文件大小超出规定值');
                }
            }

            //执行本地上传
            $res =  $this->uploadLocal($file, 1, $file_type);
        }
        if ($setting['type'] == 'oss') {
            //阿里OSS
            if ($setting['net'] == 1) {
                //开启内网上传
            } else {
                //外网上传
            }
            $res = $this->OssUpload($file, $setting);
        }

        if ($setting['type'] == 'cos') {
            //腾讯cos
            $res = $this->CosUpload($file, $setting);
        }

        return $this->successJson('ok', $res);
    }

    //本地上传
    public function uploadLocal($file, $uniacid, $file_type)
    {
        if (!$file) {
            return $this->errorJson('请传入正确参数');
        }

        if ($file->isValid()) {
            $originalName = $file->getClientOriginalName(); // 文件原名

            $realPath = $file->getRealPath();   //临时文件的绝对路径

            $ext = $file->getClientOriginalExtension(); //文件扩展名

            $newOriginalName = $this->getNewFileName($originalName, $ext);

            $res = \Storage::disk($file_type)->put($newOriginalName, file_get_contents($realPath));

            if ($res) {
                //存储至数据表中
                $core = new \app\platform\modules\application\models\CoreAttach;

                $d = [
                    'uniacid' => $uniacid,
                    'uid' => \Auth::guard('admin')->user()->uid,
                    'filename' => $originalName,
                    'type' => $file_type == 'syst' ? 1 : 2, //类型1.图片; 2.音乐
                    'attachment' => $newOriginalName,
                    'creatime' => time()
                ];

                $core->fill($d);
                $validate = $core->validator();

                if (!$validate->fails()) {
                    $core->save();
                }
            }

            return $this->proto.$_SERVER['HTTP_HOST'].$this->path.$newOriginalName;
        }
    }

    //获取本地已上传图片的列表 需优化搜索
    public function getLocalList()
    {
        $core = new CoreAttach();
        if (request()->search) {
            $core = $core->search(request()->search);
        }
        $list = $core->paginate()->toArray();

        foreach ($list['data'] as $v) {

            if ($v['attachment']) {

                $data[] = $this->proto.$_SERVER['HTTP_HOST'].$this->path.$v['attachment'];
            }
        }

        return $this->successJson('获取成功', $data);
    }

    //腾讯云上传
    public function CosUpload($file, $setting, $file_type)
    {
        if (!$setting) {
            return $this->errorJson('请配置参数');
        }

        $config = [
            'app_id'     => $setting['appid'],
            'secret_id'  => $setting['secretid'],
            'secret_key' => $setting['secretkey'],
            'bucket'     => $setting['bucket'],
            'region'     => $setting['url']
        ];
        $cos = new Api($config);

        $originalName = $file->getClientOriginalName(); // 文件原名

        $ext = $file->getClientOriginalExtension(); //文件扩展名

        $realPath = $file->getRealPath();   //临时文件的绝对路径

        \Log::info('cos_upload_file_content:', ['name'=> $originalName, 'ext'=>$ext, 'path'=>$realPath]);

        $truePath = $this->getOsPath($file_type);   // COS服务器 bucket 路径

        $newFileName = $this->getNewFileName($originalName, $ext); //新文件名

        \Log::info('cos_upload_path:', $truePath.$newFileName);

        $res = $cos->upload($setting['bucket'], $realPath.'/'.$originalName, $truePath.$newFileName, '', 1);  //执行上传

        \Log::info('cos_upload_res:', $res);

        $url = $setting['url'].$truePath.$newFileName;
        //检查 object 及服务器路径 权限
        // if ($res['code'] == 0 && $res['message'] == 'SUCCESS') {
        //自定义域名拼接文件名
        // }
        return $url;
    }

    //阿里云OSS 单文件外网上传
    public function OssUpload($file, $setting, $file_type)
    {
        if (!$setting['key'] || !$setting['secret'] || !$setting['endpoint'] || !$setting['bucket'] || !$setting['url']) {
            return $this->errorJson('请配置参数');
        }

        $oss = new OssClient($setting['key'], $setting['secret'], $setting['endpoint'], $setting['url']);

        $originalName = $file->getClientOriginalName(); // 文件原名

        $ext = $file->getClientOriginalExtension(); //文件扩展名

        $realPath = $file->getRealPath();   //临时文件的绝对路径

        \Log::info('oss_upload_file_content:', ['name'=> $originalName, 'ext'=>$ext, 'path'=>$realPath]);

        $truePath = substr($this->getOsPath($file_type), 1);   // OSS服务器 bucket 路径

        $newFileName = $this->getNewFileName($originalName, $ext);

        \Log::info('oss_upload_path:', $truePath.$newFileName);

        //检查 object 及服务器路径 权限
        $auth = $oss->getObjectAcl($setting['bucket'], $newFileName);

        if ($auth == 'default') {
            //检查 bucket 权限
            if ($oss->getBucketAcl($setting['bucket']) == 'private' || $auth == 'private') {
                //上传加签
                $signUrl = $oss->signUrl($setting['bucket'], $truePath.$newFileName);
            }
        }

        if ($setting['internal'] == 1) {
            // 使用内网上传
            $data = explode('.', $setting['url']);
            $one = $data[0].'-internal';
            $newUrlData = $one.'.'.$data[1].'.'.$data[2];

            $res = $oss->putObject($setting['bucket'], $truePath.$newFileName, file_get_contents($realPath.'/'.$originalName));

        } else {
            //使用外网上传
            $res = $oss->putObject($setting['bucket'], $truePath.$newFileName, file_get_contents($realPath.'/'.$originalName));
            \Log::info('AliOss_res, and Content', [$res, $realPath.'/'.$originalName]);
            // $domain = $setting['url'] ?  : config('filesystems.disks.oss.endpoint');
        }

        // if ($res['info']['http_code'] == 200) {
        // }
        $url = 'http://'.config('filesystems.disks.oss.bucket').'.'.$domain.'/'.$newFileName;

        return $url;
    }

    /**
     * 生成文件存放路径
     * @param  string $file_type 文件类型:syst图片,audio音频,video视频
     * @return string            路径
     */
    public function getOsPath($file_type)
    {
        $file_type = $file_type == 'syst' ? 'image' : $file_type ;

        $uniacid = \YunShop::app()->uniacid ? : 0 ;

        return '/'.$file_type.'s/'.$uniacid.'/'.date('Y').'/'.date('m').'/';
    }
    /**
     * 获取新文件名
     * @param  string $originalName 原文件名
     * @param  string $ext          文件扩展名
     * @return string               新文件名
     */
    public function getNewFileName($originalName, $ext)
    {
        return date('Ymd').md5($originalName . str_random(6)) . '.' . $ext;
    }

    public function ossTest()
    {
        $oss = new OssClient(config('filesystems.disks.oss.access_id'), config('filesystems.disks.oss.access_key'), config('filesystems.disks.oss.endpoint'));

        $auth = $oss->getObjectAcl(config('filesystems.disks.oss.bucket'), '20190318347e0052aa60ce815f6f58bcd4b15a5e.png');
        // dd($auth);

        // $file = $oss->doesObjectExist(config('filesystems.disks.oss.bucket'), '/test');
        // dd($file)
        // $acl = $oss->getBucketAcl(config('filesystems.disks.oss.bucket')); dd($acl);
        $signUrl = $oss->signUrl(config('filesystems.disks.oss.bucket'), 'test'); dd($signUrl);


        $res = $oss->putObject(
            config('filesystems.disks.oss.bucket'),
            'test/'.md5('2s1sf4s411').'.png',
            file_get_contents('D:\wamp\www\shop\storage\app\public\201903155c8b82a7c16502036.png')
        );


        dd($res);
    }

    public function cosTest()
    {
        $cos = new Api(config('filesystems.disks.cos'));
        $check = '/images/0/2019/03/';
        $originalName = 'aaa222www11'; $ext='png';
        $newFileName = date('Ymd').md5($originalName . str_random(6)) . '.' . $ext;

        $res = $cos->upload(
            config('filesystems.disks.cos.bucket'),
            'D:\wamp\www\shop\storage\app\public\201903145c8a4009ed4838729.png',
            'test/'.date('Y').'/'.$newFileName
        );
        dd($res);

        $file_type = $file_type == 'syst' ? 'images' : $file_type.'s';

        $uniacid = \YunShop::app()->uniacid ? : 0;

        $Syspath = '/images';
        //查看该目录下有无此文件夹
        $first = $cos->listFolder(config('filesystems.disks.cos.bucket'), $Syspath);

        if ($first['code'] == 0 && $first['message'] == 'SUCCESS' && $first['data']['infos']) {

        }
        //查询目录路径
        // $stat = $cos->stat(config('filesystems.disks.cos.bucket'), $Syspath);
        // dd($stat, $Syspath);
        $checkdir = $cos->statFolder(config('filesystems.disks.cos.bucket'), $Syspath); dd($checkdir);
        // $dirLists = $cos->listFolder(config('filesystems.disks.cos.bucket'), '/');
        // dd($dirLists);
        return '/images/'.$uniacid.'/'.date('Y').'/'.date('m');
    }

}