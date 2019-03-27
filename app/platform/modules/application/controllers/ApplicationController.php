<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\UniacidApp;
use app\common\helpers\Cache;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\application\models\AppUser;

class ApplicationController extends BaseController
{
    protected $key = 'application';

    public function index()
    {
        $search = request()->search;
        
        $app = new UniacidApp();

        $ids = self::checkRole();

        if (!is_array($ids)) {
            
            return $this->errorJson($ids);
        }

        if (\Auth::guard('admin')->user()->uid != 1) {

            $list = $app->whereIn('id', $ids)->search($search)->orderBy('id', 'desc')->paginate()->toArray();

        } else {

            $list = $app->search($search)->orderBy('id', 'desc')->paginate()->toArray();
        }
            
            foreach ($list['data'] as $key => $value) {
                
                if ($value['validity_time'] == 0) {

                    $list['data'][$key]['validity_time'] = intval($value['validity_time']);

                } else {
                    
                    $list['data'][$key]['validity_time'] = date('Y-m-d', $value['validity_time'] );
                }
            }

        return $this->successJson('获取成功',  $list);
    }

    public static function checkRole()
    {
        $uid = \Auth::guard('admin')->user()->uid;

        $user = AdminUser::find($uid);

        $appUser = AppUser::where('uid', $uid)->get();

        // if (!$user || !$appUser || $user->type != 0 ) {
        if (!$user || !$appUser ) {
            return '您无权限查看平台应用';
        }

        if ($user->endtime != 0 && $user->endtime < time()) {
            return '您的账号已过期';
        }
        
        foreach ($appUser->toArray() as $k => $v) {
            $ids[] = $v['uniacid'];
        }
        
        $app = UniacidApp::where('creator', $uid)->get();

        if ($app) {

            foreach ($app as $key => $value) {
                
                $ids[] = $value['id'];
            }
        }
        return $ids;
    }

    public function add()
    {
        $app = new UniacidApp();

        $data = $this->fillData(request()->input());

        $id = $app->insertGetId($data); 

        if ($id) {
            
            $up = UniacidApp::where('id', $id)->update(['uniacid'=>$id]);  
            
            if (!$up) {
                \Log::info('平台添加修改uniacid字段失败, id为',$id);
            }
            //更新缓存
//                Cache::put($this->key.':'. $id, $app->find($id));
//                Cache::put($this->key.'_num', $id);
            return $this->successJson('添加成功');

        } else {

            return $this->errorJson('添加失败');
        }
    }

    public function update()
    {

        $id = request()->id;

        $app = new UniacidApp();

        $info = $app->find($id);

        if (!$id || !$info) {
            return $this->errorJson('请选择应用');
        }

        if (request()->input()) {

            $data = $this->fillData(request()->input());
            $data['id'] = $id;
            $data['uniacid'] = $id;

            $app->fill($data);

            $validator = $app->validator($data);

            if ($validator->fails()) {

                return $this->errorJson($validator->messages());

            } else {

                if ($app->where('id', $id)->update($data)) {
                    //更新缓存
                    // Cache::put($this->key . ':' . $id, $app->find($id), $data['validity_time']);

                    return $this->successJson('修改成功');
                } else {

                    return $this->errorJson('修改失败');
                }
            }
        }
    }

    public function getApp()
    {
        $id = request()->id;
        
        $app = new UniacidApp();

        $info = $app->find($id);

        if (!$id || !$info) {
            return $this->errorJson('获取失败');
        }
        return $this->successJson('获取成功', $info);
    }

    //加入回收站 删除
    public function delete()
    {
        $id = request()->id;

        $info = UniacidApp::withTrashed()->find($id);

        if (!$id || !$info) {
            return $this->errorJson('请选择要修改的应用');
        }
        if ($info->deleted_at) {

            //强制删除
            if (!$info->forceDelete()) {
                return $this->errorJson('操作失败');
            }

            // Cache::forget($this->key . ':' . $id);

        } else {

            if (!$info->delete()) {
                return $this->errorJson('操作失败');
            }

            $info->update(['status'=> 0]);

            // Cache::put($this->key . ':' . $id, UniacidApp::find($id));
        }

        return $this->successJson('操作成功');
    }

    //启用禁用或恢复应用
    public function switchStatus()
    {

        $id = request()->id;

        $info = UniacidApp::withTrashed()->find($id);

        if (!$id || !$info) {
            return $this->errorJson('请选择要修改的应用');
        }

        if (request()->status) {
            //修改状态
            $res = UniacidApp::where('id', $id)->update(['status' => $info->status == 1 ? 0 : 1]);
        }

        if (request()->url) {
            //修改应用跳转链接
            $res = UniacidApp::where('id', $id)->update(['url' => filter_var(trim(request()->url), FILTER_VALIDATE_URL)]);
        }

        if ($info->deleted_at) {

            //从回收站中恢复应用
            $res = UniacidApp::withTrashed()->where('id', $id)->restore();
            $info->update(['status'=> 1]);
        }

        if ($res) {
            //更新缓存
            // Cache::put($this->key . ':' . $id, UniacidApp::find($id), $info->validity_time);

            return $this->successJson('操作成功');
        } else {
            return $this->errorJson('操作失败');
        }
    }

    //回收站 列表
    public function recycle()
    {
        $search = request()->search;

        $app = new UniacidApp();

        if ($search) {
            $app = $app->search($search);
        }

        $list = $app
            ->onlyTrashed()
            ->orderBy('id', 'desc')
            ->paginate()
            ->toArray();

        foreach ($list['data'] as $key => $value) {
                
            if ($value['validity_time'] == 0) {

                $list['data'][$key]['validity_time'] = intval($value['validity_time']);

            } else {
                
                $list['data'][$key]['validity_time'] = date('Y-m-d', $value['validity_time'] );
            }
        }

        if ($list) {
            return $this->successJson('获取成功', $list);
        } else {
            return $this->errorJson('获取失败,暂无数据');
        }
    }

    private function fillData($data)
    {
        return [
            'img' => $data['img'] ?  : 'http://www.baidu.com',
            'url' => $data['url'],
            'name' => $data['name'] ?  : 'test',
            'kind' => $data['kind'] ?  : '',
            'type' => $data['type'] ?  : 2,
            'title' => $data['title'] ?  : '',
            'descr' => $data['descr'] ?  : '',
            'status' => $data['status'] ?  : 1,
            'creator' => \Auth::guard('admin')->user()->uid,
            'version' => $data['version'] ?  : 0.00,
            'validity_time' => $data['validity_time'] ?  : 0,
        ];
    }

    public function upload()
    {
        $file = request()->file('file');
        \Log::info('file', $file);

        if (!$file) {
            return $this->errorJson('请传入正确参数');
        }
        if ($file->isValid()) {
            $originalName = $file->getClientOriginalName(); // 文件原名
            \Log::info('originalName', $originalName);
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            \Log::info('realPath', $realPath);

            $ext = $file->getClientOriginalExtension();
            \Log::info('ext', $ext);
            
            // $path = config('filesystems.disks.public')['root'].'/';   //后期存放路径

            $newOriginalName = date('Ymd').md5($originalName . str_random(6)) . '.' . $ext;
            \Log::info('newOriginalName', $newOriginalName);

            $res = \Storage::disk('public')->put($newOriginalName, file_get_contents($realPath));
            \Log::info('res-path', [$res, \Storage::disk('public')]);

            // $proto = explode('/', $_SERVER['SERVER_PROTOCOL'])[0] === 'https' ? 'https://' : 'http://';
            $proto = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';

            return $this->successJson('上传成功', $proto.$_SERVER['HTTP_HOST'].'/storage/app/public/'.$newOriginalName);
        }
    }

    public function temp()
    {
        if (request()->input()) {
            
            $file = request()->file;
            // dd($file);
            if ($file) {

            $first = explode(',', $file);
            $ext = explode(';', explode('/', $first[0])[1])[0];

            //解码
            $content = base64_decode($first[1]); 
            //自定义路径
            // $path = config('filesystems.disks.public')['root'].'/';

            // $extPath = str_replace(substr($path, -7, 1), "\\", $path);
            
            $filename = date('Ymd').uniqid().rand(1, 9999).'.'.$ext;

            $url = $path.$filename;
            
            // UploadedFile::store($url);
            $res = \Storage::disk('public')->put($url, $content);
            // dd($res);
            return $this->successJson('上传成功', $proto.$_SERVER['HTTP_HOST'].'/storage/app/public/'.$filename);

            }
        }
        return View('admin.application.upload');
    }
}