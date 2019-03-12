<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\UniacidApp;
use app\common\helpers\Cache;
use app\common\helpers\PaginationHelper;
// use Illuminate\Support\Facades\Storage;
use app\common\services\Storage;
use Illuminate\Http\UploadedFile;

class ApplicationController extends BaseController
{
    protected $key = 'application';

    public function index()
    {
        // if (request()->ajax()) {
            
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
        // }    
    }

    public function add()
    {
        // if (request()->ajax()) {

            $app = new UniacidApp();
            // dd(request());
            $data = $this->fillData(request()->input());

            $app->fill($data);

            $validator = $app->validator();

            if ($validator->fails()) {

                return $this->errorJson($validator->messages());
            
            } else {

                if ($app->save()) {
                    
                    // $id = $app->insertGetId();
                    //更新缓存
                    // Cache::put($this->key.':'. $id, $app->find($id));
                    // Cache::put($this->key.'_num', $id);

                    return $this->successJson('添加成功');

                } else {

                    return $this->errorJson('添加失败');
                }
            }
        // }
    }

    public function update()
    {
        // if (request()->ajax()) {

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

                    return $this->errorJson($validator->messages());

                } else {

                    if ($app->where('id', $id)->update($data)) {
                        //更新缓存
                        Cache::put($this->key . ':' . $id, $app->find($id), $data['validity_time']);

                        return $this->successJson('修改成功');
                    } else {

                        return $this->errorJson('修改失败');
                    }
                }
            }
        // }
    }

    //加入回收站 删除
    public function delete()
    {
        // if (request()->ajax()) {

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

                Cache::forget($this->key . ':' . $id);

            } else {

                if (!$info->delete()) {
                    return $this->errorJson('操作失败');
                }

                Cache::put($this->key . ':' . $id, UniacidApp::find($id));
            }

            return $this->successJson('操作成功');
        // }
    }

    //启用禁用或恢复应用
    public function switchStatus()
    {
        // if (request()->ajax()) {

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
                // dd('2');
            }

            if ($res) {
                //更新缓存
                Cache::put($this->key . ':' . $id, UniacidApp::find($id), $info->validity_time);

                return $this->successJson('操作成功');
            } else {
                return $this->errorJson('操作失败');
            }
        // }
    }

    //回收站 列表
    public function recycle()
    {
        // if (request()->ajax()) {

            $list = UniacidApp::onlyTrashed()
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->toArray();

            if ($list) {
                return $this->successJson('获取成功', $list);
            } else {
                return $this->errorJson('获取失败,暂无数据');
            }
        // }
    }

    private function fillData($data)
    {
        // dd($data);
        return [
            'img' => $data['img'] ?  : 'http://www.baidu.com',
            'url' => $data['url'],
            'name' => $data['name'] ?  : 'test',
            'kind' => $data['kind'] ?  : '',
            'type' => $data['type'] ?  : 2,
            'title' => $data['title'] ?  : '',
            'descr' => $data['descr'] ?  : '',
            'status' => $data['status'] ?  : 1,
            // 'uniacid' => $app->insertGetId() + 1,
            'version' => $data['version'] ?  : 0.00,
            'validity_time' => $data['validity_time'] ?  : 0,
        ];
    }

    public function upl()
    {
            header('Access-Control-Allow-Origin:*');

            $file = request()->file('file');

            if (!file_exists('up.txt')) {
                
                touch('up.txt');

                chmod('./up.txt', 0777);
            }

            file_put_contents( './up.txt', $file);
            // $file = $_POST['file'];
            \Log::info('file_content', $file);

            $first = explode(',', $file);

            $ext = explode(';', explode('/', $first[0])[1])[0];
            \Log::info('up_ext', $ext);

            //解码
            $content = base64_decode($first[1]); 
            //自定义路径
            $path = config('filesystems.disks.public')['root'].'/';
            \Log::info('up_path', $path);

            // $extPath = str_replace(substr($path, -7, 1), "\\", $path);
            // \Log::info('up_extPath', $extPath);
            
            if (!file_exists($path) || !is_dir($path)) {
                
                // mkdir($path);
                \Log::info('upload_dir_not_exists');
                return false;
            }
            
            chmod($path, 0777);

            $ch = opendir($path);

            $filename = date('Ymd').uniqid().rand(1, 9999).'.'.$ext;
                \Log::info('up_filename', $filename);

            $url = $path.$filename;
                \Log::info('up_url', $url);
            
            // UploadedFile::store($url);
            $res = Storage::put($url, $content);

            // $res = \Storage::url();
                \Log::info('up_res', $res);
            
            closedir($ch);

            return $this->successJson('上传成功', asset($res.'app/public/'.$filename));
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

            // $res = \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));
            $res = \Storage::disk('public')->put($newOriginalName, file_get_contents($realPath));
            \Log::info('res-path', [$res, \Storage::disk('public')]);

            $proto = stripos($_SERVER['SERVER_PROTOCOL'],'https') ? 'https://' : 'http://';

            // return $this->successJson('上传成功', \Storage::disk('public')->url('app/public/'.$newOriginalName));
            return $this->successJson('上传成功', $proto.$_SERVER['HTTP_HOST'].'/storage/app/public/'.$newOriginalName);
        }
    }

    public function temp()
    {
        dd();
        // return View('admin.application.upload');
    }
}