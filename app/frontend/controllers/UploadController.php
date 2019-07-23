<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-07-19
 * Time: 13:57
 */

namespace app\frontend\controllers;


use app\common\components\BaseController;
use app\common\services\ImageZip;
use app\platform\modules\system\models\SystemSetting;

class UploadController extends BaseController
{
    public function uploadPic()
    {
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('上传失败.');
        }

        if ($file->getClientSize() > 30*1024*1024) {
            return $this->errorJson('图片过大.');
        }

        $defaultImgType = [
            'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
            'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
        ];

        $setting = SystemSetting::settingLoad('global', 'system_global');

        $remote = SystemSetting::settingLoad('remote', 'system_remote');

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $ext = $file->getClientOriginalExtension(); //文件后缀

        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

        if (in_array($ext, $defaultImgType)) {
            if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions'])) ) {
                return $this->errorJson('非规定类型的文件格式');
            }
            $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024*1024*5; //默认大小为5M
            if ($file->getClientSize() > $defaultImgSize) {
                return $this->errorJson('文件大小超出规定值');
            }
        }

        //本地上传
        \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

        if ($setting['image']['zip_percentage']) {
            //执行图片压缩
            $imagezip = new ImageZip();
            $imagezip->makeThumb(
                yz_tomedia($newOriginalName),
                yz_tomedia($newOriginalName),
                $setting['image']['zip_percentage']
            );
        }

        if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
            $imagezip = new ImageZip();
            $imagezip->makeThumb(
                yz_tomedia($newOriginalName),
                yz_tomedia($newOriginalName),
                $setting['thumb_width']
            );
        }

        //远程上传
        if ($remote['type'] != 0) {
            file_remote_upload($newOriginalName, true, $remote);
        }

        return $this->successJson('上传成功', [
            'img' => \Storage::disk('image')->url($newOriginalName),
            'img_url' => yz_tomedia(\Storage::disk('image')->url($newOriginalName)),
        ]);
    }
}