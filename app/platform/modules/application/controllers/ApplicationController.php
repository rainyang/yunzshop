<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\UniacidApp;
use app\common\helpers\Cache;
use app\common\helpers\PaginationHelper;
// use Illuminate\Support\Facades\Storage;
use app\common\services\Storage;
use Intervention\Image\File;
use app\common\services\UploadStrategy;
use  Illuminate\Filesystem\FilesystemManager;

class ApplicationController extends BaseController
{
    protected $key = 'application';

    public function index()
    {
        $search = request()->search;
        
        $app = new UniacidApp();

        if ($search) {
            
            $app = $app->search($search);


        } 
        // else {
            $list = $app->orderBy('id', 'desc')->paginate(5)->toArray();

//            $total = Cache::get($this->key.'_num');
//
//            for($i = 1; $i< $total; $i++ ) {
//
//               $list[] = Cache::paginate(request()->input('pagesize',20))->get($this->key.':'.$i);
//            }
        // }

        return $this->successJson('获取成功',  $list);
    }

    public function add()
    {
        $app = new UniacidApp();

        if (request()->input()) {

            $data = $this->fillData(request()->input());

            $app->fill($data);

            $validator = $app->validator();

            if ($validator->fails()) {
            
                // return $this->error($validator->messages());
            
            } else {

                if ($app->save()) {
                    
                    // $id = $app->insertGetId();
                    //更新缓存
                    // Cache::put($this->key.':'. $id, $app->find($id));
                    // Cache::put($this->key.'_num', $id);

                    // return $this->successJson('添加成功');
                    return json_encode(array('result'=>1, 'msg'=>'添加成功', 'data'=>''));
                    
                } else {

                    // return $this->errorJson('添加失败');
                    return json_encode(array('result'=>0, 'msg'=>'添加失败', 'data'=>''));
                }
            }
        }
        // return View('admin.application.form');
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
            $data['uniacid'] = $id;
            $data['id'] = $id;

            $app->fill($data);

            $validator = $app->validator($data);

            if ($validator->fails()) {

                return $this->error($validator->messages());
            
            } else {

                if ($app->where('id', $id)->update($data)) {
                    //更新缓存
                    Cache::put($this->key.':'. $id, $app->find($id), $data['validity_time']);

                    return $this->successJson('修改成功');
                } else {
                    
                    return $this->errorJson('修改失败');
                }
            }
        }
        return $this->successJson('成功获取', $info);
    }

    //加入回收站 删除
    public function delete()
    {
        $id = request()->id;
        
        $info = UniacidApp::withTrashed()->find($id);

        if (!$id || !$info) {
            return $this->errorJson('请选择要修改的应用');
        }
        // dd($info->deleted_at);
        if ($info->deleted_at) {
            
           //强制删除
            if (!$info->forceDelete()) {
                return $this->errorJson('操作失败');            
            }
        
            Cache::forget($this->key.':'.$id);

        } else {

            if(!$info->delete()){
                return $this->errorJson('操作失败');            
            }

            Cache::put($this->key.':'.$id, UniacidApp::find($id));
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
            $res = UniacidApp::where('id', $id)->update(['url' => filter_var(trim(request()->url), FILTER_VALIDATE_URL) ]);
        }

        if ($info->deleted_at) {

            //从回收站中恢复应用
            $res = UniacidApp::withTrashed()->where('id', $id)->restore();
            // dd('2');
        }

        if ($res) {
            //更新缓存
            Cache::put($this->key.':'.$id, UniacidApp::find($id), $info->validity_time);

            return $this->successJson('操作成功');
        } else {
            return $this->errorJson('操作失败');
        }
       
    }

    //回收站 列表
    public function recycle()
    {
        $list = UniacidApp::onlyTrashed()
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->toArray();

        if ($list) {

            return $this->successJson('获取成功', $list);
        } else {
            return $this->errorJson('获取失败,暂无数据');
        }
    }

    private function fillData($data)
    {
        return [
            'img' => request()->img ?  : 'http://www.baidu.com',
            'url' => request()->url,
            'name' => request()->name ?  : 'test',
            'kind' => request()->kind ?  : '',
            'type' => request()->type ?  : 2,
            'title' => request()->title ?  : '',
            'descr' => request()->descr ?  : '',
            'status' => request()->status ?  : '',
            // 'uniacid' => $app->insertGetId() + 1,
            'version' => request()->version ?  : 1.0,
            'validity_time' => request()->validity_time ?  : 0,
        ];
    }

    public function upl()
    {
            $file = request()->file('img');
            //自定义路径
            $path = config('filesystems.disks.public')['root'];
            \Log::info('up_path', $path);
            // dd($path);
            $extPath = str_replace(substr($path, -7, 1), "\\", $path);
            \Log::info('up_extPath', $extPath);

            // dd($path);
            if (!file_exists($extPath)) {
                
                mkdir($extPath);
            }
            $extPath = $extPath.'\\'.date('Ymd');
            // dd($extPath);    
            \Log::info('up_extPath2', $extPath);
            
            //判断文件是否上传成功
            if ($file->isValid()){
                //原文件名
                $originalName = $file->getClientOriginalName();          
            \Log::info('up_originalName', $originalName);

                //扩展名
                $ext = $file->getClientOriginalExtension(); 
            \Log::info('up_ext', $ext);

                //MimeType
                // $type = $file->getClientMimeType();
                //临时绝对路径
                $realPath = $file->getRealPath();
            // dd($realPath);
            \Log::info('up_realPath', $realPath);

                $filename = date('Ymd').uniqid().rand(1,9999).'.'.$ext;
            \Log::info('up_filename', $filename);

                $url = $path.'\\'.$filename;
            \Log::info('up_url', $url);
               
                Storage::put($url, file_get_contents($realPath));

                $res = \Storage::url();
            \Log::info('up_res', $res);

                // return $this->successJson('上传成功', asset('public/'.$filename));
                return $this->successJson('上传成功', asset($res.'app/public/'.$filename));
            }
    }
}