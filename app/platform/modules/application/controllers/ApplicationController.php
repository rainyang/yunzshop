<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\UniacidApp;
use app\common\helpers\Cache;
use app\common\helpers\PaginationHelper;

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
            
                return $this->error($validator->messages());
            
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
        }
        // return View('admin.application.form');
    }

    public function update()
    {
        $id = request()->id;
        
        $app = new UniacidApp();
        
        $info = $app->find($id);

        if (!$id || !$info) {
            return $this->errorJson('请选择要修改的应用');
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
        return $this->successJson('成功获取', ['item'=>$info]);
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
            $info->forceDelete();           
        
            Cache::forget($this->key.':'.$id);

        } else {

            $info->delete();

            Cache::put($this->key.':'.$id, UniacidApp::find($id));
        }

        return $this->successJson('OK');
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

        return $this->successJson('获取成功', $list);
    }

    private function fillData($data)
    {
        return [
            'img' => request()->img ?  : 'http://www.baidu.com',
            'url' => request()->url ?  : 'http://www.jd.com',
            'name' => request()->name ?  : 'test',
            'kind' => request()->kind ?  : '',
            'type' => request()->type ?  : 2,
            'title' => request()->title ?  : '',
            'descr' => request()->descr ?  : '',
            'status' => request()->status ?  : '',
            // 'uniacid' => $app->insertGetId() + 1,
            'version' => request()->version ?  : 1.0,
            'validity_time' => request()->validity_time ?  : time()+(3600*24*30),
        ];
    }

    // private function backMsg(int $status, string $msg, mix $data = null)
    // {
        // return json_encode(array('result'=>$status, 'msg'=>$msg, 'data'=>$data));
    // }

}