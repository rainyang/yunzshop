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
        $set_data = request()->upload;
        if ($set_data) {
            $harmtype = array('asp','php','jsp','js','css','php3','php4','php5','ashx','aspx','exe','cgi');

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
            if($zip_percentage <=0 || $zip_percentage > 100) {
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
                    'key'       => 'global',
                    'value'     => $set_data
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

        $config['image_extentions'] = ['0' => 'gif', '1' => 'jpg', '2' => 'jpeg', '3' => 'png'];
        $config['image_limit'] = 5000;
        $config['audio_extentions'] = ['0' => 'mp3'];
        $config['audio_limit'] = 5000;


        if ($global->isEmpty()) {
            $global = $config;
        }

        $global = json_decode($global['0']);

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

        if(!$global->zip_percentage) {
            $global->zip_percentage = 100;
        }

        return view('system.attachment', [
            'upload' => $global,
            'post_max_size' => $post_max_size,
            'upload_max_filesize' => $upload_max_filesize
        ]);


        //  缩略设置 thumb
        //  缩略宽度 thumb_width
        //  支持图片后缀 image_extentions
        //  支持图片大小 image_limit
        //  图片压缩 zip_percentage
        //  支持音频视频后缀 audio_extentions
        //  支持音频视频大小 audio_limit
    }

    public function bytecount($str) {
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
}