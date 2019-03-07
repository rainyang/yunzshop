<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\application\models\AppUser;

class AppuserController extends BaseController
{
	// protected $user;
	protected $key = 'application_user';

	public function index()
	{
		$list = AppUser::orderBy('id', 'desc')->get();

		return View('admin.appuser.index', ['list'=>$list]);
	}

	public function add()
    {
        if (request()->input()) {
            
            $user = new AppUser();
           
            $data = $this->fillData(request()->input());
            
            $user->fill($data);

            $validator =$user->validator();

            if ($validator->fails()) {
            
                return $this->error($validator->messages());
            
            } else {

                if ($this->user->save()) {
                    
                    //更新缓存
                    \Cache::put($this->key.':'.$user->insertGetId(),$user->find($this->user->insertGetId()));

                    \Cache::put($this->key.'_num',$user->insertGetId());

                    return $this->backMsg(1, '添加成功');

                } else {

                    return $this->backMsg(0, '添加失败');
                }
            }
        }
		return View('admin.appuser.form');
    }

	public function update()
	{
		$id = request()->id;
        
        $user = new AppUser;

        $info = $user->find($id);

        if (!$id || !$info) {
            return $this->backMsg(0, '请选择要修改的应用');
        }

        if (request()->input()) {
           
            $data = $this->fillData(request()->input());
            $data['uniacid'] = $id;
            $data['id'] = $id;

            $user->fill($data);

            $validator = $user->validator($data);

            if ($validator->fails()) {

                return $this->error($validator->messages());
            
            } else {

                if ($user->where('id', $id)->update($data)) {
                    //更新缓存
                    \Cache::put($this->key.':'. $id, $user->find($id));

                    return $this->backMsg(1, '修改成功');
                } else {
                    
                    return $this->backMsg(0, '修改失败');
                }
            }
        }

		return View('admin.appuser.form', ['item'=>$info]);
	}

	public function delete()
	{	
		$id = request()->id;
        
        $info = AppUser::find($id);

        if (!$id || !$info) {
            return $this->backMsg(0, '请选择要修改的用户');
        }

        $info->delete();

        \Cache::put($this->key.':'.$id, AppUser::find($id));

        return $this->backMsg(1, 'OK');
	}

	private function fillData($data)
    {
        return [
        	'name' => request()->name,
            'owner_uid' => request()->owner_uid ? : 0,
            'uniacid' => request()->uniacid ? : 0,
            'app_role_id' => request()->app_role_id ? : 0,
            'app_permission_id' => request()->app_permission_id ? : 0,
            'status' => request()->status ? : 1
        ];
    }

    private function backMsg(int $status, string $msg, mix $data = null)
    {
        return json_encode(array('result'=>$status, 'msg'=>$msg, 'data'=>$data));
    }
}