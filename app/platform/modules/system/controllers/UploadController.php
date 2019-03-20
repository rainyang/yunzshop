<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/6
 * Time: 14:01
 */
namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use app\platform\modules\system\models\Loader;
use app\platform\modules\application\models\CoreAttach;
use app\platform\modules\application\models\WechatAttachment;

class UploadController extends BaseController
{
    protected $global;
    protected $uniacid;
    protected $remote;


    public function __construct()
    {
        $this->global = SystemSetting::settingLoad('global', 'system_global');
        $this->remote = SystemSetting::settingLoad('remote', 'system_remote');
        $this->uniacid = \YunShop::app()->uniacid ?  : 0 ;
    }

    public function upload()
    {
        $dest_dir = $this->destDir();

        $option = array_elements(array('uploadtype', 'global', 'dest_dir'), $_POST);
        $option['width'] = intval($option['width']);
        $option['global'] = request()->global;

//        dd( \Auth::guard('admin')->user()->uid, $this->global, $this->remote);
        $type = in_array(request()->upload_type, array('image','audio','video')) ? request()->upload_type : 'image';

        if (!empty($option['global'])) {
            $setting['folder'] = "{$type}s/global/";
            if (! empty($dest_dir)) {
                $setting['folder'] .= '' . $dest_dir . '/';
            }
        } else {
            $setting['folder'] = "{$type}s/{$this->uniacid}";
            if (empty($dest_dir)) {
                $setting['folder'] .= '/' . date('Y/m/');
            } else {
                $setting['folder'] .= '/' . $dest_dir . '/';
            }
        }

        if (!$_FILES['file']['name']) {
            return $this->errorJson('上传失败, 请选择要上传的文件！');
        }
        if ($_FILES['file']['error'] != 0) {
            return $this->errorJson('上传失败, 请重试.');
        }

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $size = intval($_FILES['file']['size']);
        $originname = $_FILES['file']['name'];
        $folder = "static/upload/{$type}s/{$this->uniacid}" . '/'.date('Y/m/');
        $filename = file_random_name(base_path() . '/' . $folder, $ext);
        $file = $this->file_upload($_FILES['file'], $type, $folder . $filename, true);
        switch ($file) {
            case 1:
                return $this->successJson('成功', '');
                break;
            case 2:
                return $this->errorJson('没有上传内容', '');
                break;
            case 3:
                return $this->errorJson('未知的上传类型', '');
                break;
            case 4:
                return $this->errorJson('不允许上传此类文件', '');
                break;
            case 5:
                return $this->errorJson("上传的文件超过大小限制，请上传小于 {$this->global['image_limit']}k 的文件");
                break;
            case 6:
                return $this->errorJson('保存上传文件失败', '');
                break;
        }

        if (is_error($file)) {
            return $this->successJson('成功', $file['message']);
        }
        $pathname = $file['path'];
        $fullname = base_path() . '/' . $pathname;
        return $this->saveData($type, $fullname, $originname, $ext, $filename, $dest_dir['module_upload_dir'], $pathname, $option);
    }

    public function destDir()
    {
        $dest_dir = request()->dest_dir;
        if (preg_match('/^[a-zA-Z0-9_\/]{0,50}$/', $dest_dir, $out)) {
            $dest_dir = trim($dest_dir, '/');
            $pieces = explode('/', $dest_dir);
            if(count($pieces) > 3){
                $dest_dir = '';
            }
        } else {
            $dest_dir = '';
        }
        $module_upload_dir = '';
        if($dest_dir != '') {
            $module_upload_dir = sha1($dest_dir);
        }

        return [
            'dest_dir' => $dest_dir,
            'module_upload_dir' => $module_upload_dir
        ];
    }

    public function file_upload($file, $type = 'image', $name = '', $compress = false)
    {
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');

        if (!$file) {
            return 2;
        }
        if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
            return 3;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        switch ($type) {
            case 'image':
                $allowExt = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'ico');
                $limit = $this->global['image_limit'];
                break;
            case 'thumb':
            case 'voice':
            case 'audio':
                $allowExt = array('mp3', 'wma', 'wav', 'amr');
                $limit = $this->global['audio_limit'];
                break;
            case 'video':
                $allowExt = array('rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4');
                $limit = $this->global['audio_limit'];
                break;
        }
        $setting = $this->global[$type.'_extentions'];
        if ($setting) {
            $allowExt = array_merge($setting, $allowExt);
        }
        if (!in_array(strtolower($ext), $allowExt) || in_array(strtolower($ext), $harmtype)) {
            return 4;
        }
        if ($limit && $limit * 1024 < filesize($file['tmp_name'])) {
            return 5;
        }

        $result = array();
        if (!$name || $name == 'auto') {
            $path = "static\upload/{$type}s/{$this->uniacid}" . '/'.date('Y/m/');
            mkdirs(base_path() . '/' . $path);
            $filename = file_random_name(base_path() . '/' . $path, $ext);

            $result['path'] = $path . $filename;
        } else {
            mkdirs(dirname(base_path() . '/' . $name));
            if (!strexists($name, $ext)) {
                $name .= '.' . $ext;
            }
            $result['path'] = $name;
        }

        $save_path = base_path() . '/' . $result['path'];
        if (!file_move($file['tmp_name'], $save_path)) {
            return 6;
        }

        if ($type == 'image' && $compress) {
            file_image_quality($save_path, $save_path, $ext, $this->global);
        }

        $result['success'] = true;

        return $result;
    }

    public function saveData($type, $fullname, $originname, $ext, $filename, $module_upload_dir, $pathname, $option)
    {
//        if ($do == 'fetch' || $do == 'upload') {
        if ($type == 'image') {


            $thumb = !$this->global['thumb'] ? 0 : 1;
            $width = intval($this->global['thumb_width']);
            if (isset($option['thumb'])) {
                $thumb = !$option['thumb'] ? 0 : 1;
            }
            if (isset($option['width']) && $option['width']) {
                $width = intval($option['width']);
            }
            if ($thumb == 1 && $width > 0) {
                $thumbnail = file_image_thumb($fullname, '', $width, $this->global);
                if ($thumbnail == 1) {
                    return $this->errorJson('创建目录失败');
                } elseif ($thumbnail == 2) {
                    return $this->errorJson('目录无法写入');
                }
                @unlink($fullname);
                if (is_error($thumbnail)) {
                    return $this->successJson($thumbnail['message']);
                } else {
                    $filename = pathinfo($thumbnail, PATHINFO_BASENAME);
                    $pathname = $thumbnail;
                    $fullname = base_path() . '/' . $pathname;
                }
            }
        }

        $info = array(
            'name' => $originname,
            'ext' => $ext,
            'filename' => $pathname,
            'attachment' => $pathname,
            'url' => tomedia($pathname),
            'is_image' => $type == 'image' ? 1 : 0,
            'filesize' => filesize($fullname),
            'group_id' => intval(request()->group_id)
        );
        if ($type == 'image') {
            $size = getimagesize($fullname);
            $info['width'] = $size[0];
            $info['height'] = $size[1];
        } else {
            $size = filesize($fullname);
            $info['size'] = sizecount($size);
        }
        if ($this->remote['type']) {
            $remotestatus = file_remote_upload($pathname, true, $this->remote);
            switch ($remotestatus['status']) {
                case 1:
                    return $this->errorJson($remotestatus['msg'], '');
                    break;
                case -62:
                    return $this->errorJson('输入的appid有误', '');
                    break;
                case -79:
                    return $this->errorJson('输入的SecretID有误', '');
                    break;
                case -97:
                    return $this->errorJson('输入的SecretKEY有误', '');
                    break;
                case -166:
                    return $this->errorJson('输入的bucket有误', '');
                    break;
            }

            if (is_error($remotestatus)) {
                file_delete($pathname);
                return $this->errorJson('远程附件上传失败，请检查配置并重新上传', '');
            } else {
                file_delete($pathname);
                $info['url'] = tomedia($pathname);
            }
        }

        $core_attach = CoreAttach::create([
            'uniacid' => $this->uniacid,
            'uid' => \Auth::guard('admin')->user()->uid,
            'filename' => safe_gpc_html(htmlspecialchars_decode($originname, ENT_QUOTES)),
            'attachment' => $pathname ? : '',
            'type' => $type == 'image' ? 1 : ($type == 'audio'||$type == 'voice' ? 2 : 3),
            'module_upload_dir' => $module_upload_dir,
            'group_id' => intval(request()->group_id)
        ]);

        if ($core_attach) {
            $info['state'] = 'SUCCESS';
            return json_encode($info);
            return $this->successJson('成功', $info);
        } else {
            return $this->errorJson('失败');
        }
//        }
    }


    public function image()
    {
        $year = request()->year;
        $month = request()->month;
        $page = max(1, intval(request()->page));
        $groupid = intval(request()->groupid);
        $page_size = 24;
        $islocal = request()->local == 'local';
        $is_local_image = $islocal == 'local' ? true : false;
        if ($page<=1) {
            $page = 0;
            $offset = ($page)*$page_size;
        } else {
            $offset = ($page-1)*$page_size;
        }

        if(!$is_local_image) {
            $core_attach =  new WechatAttachment;
        } else {
            $core_attach = new CoreAttach;
        }
        $dest_dir = $this->destDir();
        $core_attach = $core_attach->uniacid()->where('module_upload_dir', $dest_dir['module_upload_dir']);
//        $attachment_table = table('attachment');
//        $attachment_table = $attachment_table->local($is_local_image);
//        $attachment_table->searchWithUniacid($uniacid);
//        $attachment_table->searchWithUploadDir($module_upload_dir);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
        }
        if ($groupid > 0) {
            $core_attach = $core_attach->where('group_id', $groupid);
        }
        if ($groupid == 0) {
            $core_attach = $core_attach->where('group_id', -1);
        }
        if ($year || $month) {
            $start_time = strtotime("{$year}-{$month}-01");
            $end_time = strtotime('+1 month', $start_time);
            $core_attach = $core_attach->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        }
        if ($islocal) {
            $core_attach = $core_attach->where('type', 1);
        } else {
            $core_attach = $core_attach->where('type', 'image');
        }
//        $attachment_table->searchWithPage($page, $page_size);

        $count = $core_attach->orderby('created_at', 'desc')->get()->count();
        $core_attach = $core_attach->orderby('created_at', 'desc')->offset($offset)->limit($page_size)->get();

//        $list = $attachment_table->searchAttachmentList();
//        $total = $attachment_table->getLastQueryTotal();

        foreach ($core_attach as &$meterial) {
            if ($islocal) {
                $meterial['url'] = tomedia($meterial['attachment']);
                unset($meterial['uid']);
            } else {
                $meterial['attach'] = tomedia($meterial['attachment'], true);
                $meterial['url'] = $meterial['attach'];
            }
        }

        $pager = pagination($count, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => '1'));
        $result = array('items' => $core_attach, 'pager' => $pager);

        $array = [
            'message' => [
                'erron' => 0,
                'message' => $result
            ],
            'redirect' => '',
            'type' => 'ajax'
        ];

        return \GuzzleHttp\json_encode($array);
//        return $this->successJson('', $result);
//        iajax(0, $result);
    }


}