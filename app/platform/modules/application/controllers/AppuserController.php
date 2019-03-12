<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\AppUser;
use app\common\helpers\Cache;
use app\platform\modules\application\models\UniacidApp;
use app\platform\modules\user\models\AdminUser;

class AppuserController extends BaseController
{
	protected $key = 'application_user';
	protected $role = ['owner', 'manager', 'operator', 'founder'];

	public function index()
	{
		$list = AppUser::orderBy('id', 'desc')->paginate()->toArray();

		return $this->successJson('获取成功', $list);
	}

	public function add()
    {
        if (request()->input()) {
            
            $user = new AppUser();
            
            $data = $this->fillData(request()->input());
           
            if (!is_array($data)) {

            	return $this->errorJson($data);
            }

            $user->fill($data);

            $validator =$user->validator();

            if ($validator->fails()) {
            
                return $this->error($validator->messages());
            
            } else {

                if ($this->user->save()) {
                    
                    //更新缓存
                    // Cache::put($this->key.':'.$user->insertGetId(),$user->find($this->user->insertGetId()));

                    // Cache::put($this->key.'_num',$user->insertGetId());

                    return $this->successJson(1, '添加成功');

                } else {

                    return $this->errorJson(0, '添加失败');
                }
            }
        }
		 // return View('admin.appuser.form');
    }

	public function delete()
	{	
		$id = request()->id;
        
        $info = AppUser::find($id);

        if (!$id || !$info) {
            return $this->errorJson(0, '请选择要删除的用户');
        }

        $info->delete();

        // Cache::put($this->key.':'.$id, AppUser::find($id));

        return $this->successJson(1, 'OK');
	}

	public function checkUser($data)
	{
		
		return true;
	}

	private function fillData($data)
    {
    	$checkUser = AdminUser::find($data['uid']); 
		//用户存在且状态有效, 角色为普通用户时可以添加
        // if (!$checkUser || $checkUser->status != 0 || $checkUser->type != 1) {
        if (!$checkUser ) {
        	return 'uid 无效';
        }
        // dd(UniacidApp::chekcApp( intval($data['uniacid'])) );
        //检测平台
		if (! UniacidApp::chekcApp($data['uniacid'])) {
        	return '平台id 无效';
        	// return false;
		}

		if (!in_array($data['role'], $this->role)) {
			return '权限值非法';
			// return false;
		}

        return [
         		'uniacid' => $data['uniacid'],
         		'uid' => $data['uid'],
         		'role' => $data['role'] ? : 'manager',
        ];
    }

}