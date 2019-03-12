<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\AppUser;
use app\common\helpers\Cache;

class AppuserController extends BaseController
{
	protected $key = 'application_user';

	public function index()
	{
		$list = AppUser::orderBy('id', 'desc')->get();

		return $this->successJson('获取成功', $list);
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

	public function checkUserRole()
	{
		
	}

	private function fillData($data)
    {
        return [
         		'uniacid' => request()->uniacid,
         		'uid' => request()->uid,
         		'role' => request()->role ? : '',
        ];
    }

}