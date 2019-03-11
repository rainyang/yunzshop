<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/8
 * Time: 14:12
 */

namespace app\platform\modules\user\models;


use app\common\models\BaseModel;
use app\common\helpers\Cache;
use Illuminate\Support\Facades\Hash;

class AdminUser extends BaseModel
{
    public $table = 'yz_admin_user';
    public $timestamps = true;
    protected $guarded = [''];

    /**
     * 保存数据
     * @param $data
     * @param string $user_model
     * @return mixed
     */
    public function saveData($data, $user_model)
    {
        $verify_res = self::verifyData($data, $user_model);
        if ($verify_res['validator']->fails()) {
            return $this->errorJson($verify_res['validator']->messages());
        }
        if ($verify_res['user_model']['re_password']) {
            $verify_res['user_model']['password'] = Hash::make($verify_res['user_model']['password']);
            unset($verify_res['user_model']['re_password']);
        }
        $result = $verify_res['user_model']->save();
        if ($result) {
            Cache::put('admin_user', $data, 3600);
            return $this->successJson('成功', '');
        } else {
            return $this->errorJson('失败', '');
        }
    }

    /**
     * 读取单条数据
     * @param $id
     * @return AdminUser
     */
    public static function getData($id)
    {

        return self::find($id);

//        if (!Cache::has($cache_name)) {
//            $result = self::getKeyList($key);
//            Cache::put($cache_name, $result, 3600);
//        } else {
//            $result = \Cache::get($cache_name);
//        }
//        if ($result) {
//            $result = unserialize($result);
//        }

//        return $result;
    }

    /**
     * 验证数据
     * @param $data
     * @param string $user_model
     * @return array
     */
    public static function verifyData($data, $user_model = '')
    {
        if (request()->path() != "admin/user/change") {
            $data['username'] = trim($data['username']);
            $data['phone'] = trim($data['phone']);
            if ($data['application_number'] == 0) {
                $data['application_number'] = '';
            }
            if ($data['effective_time'] == 0) {
                $data['effective_time'] = '';
            } else {
                $data['effective_time'] = strtotime($data['effective_time']);
            }
            $data['remarks'] = trim($data['remarks']);
        } else {
            $data['old_password'] = trim($data['old_password']);
            if (!Hash::check($data['old_password'], $user_model['password'])) {
                $array = ([
                    'result' => 0,
                    'msg' => '原密码错误',
                    'data' => ''
                ]);
                echo json_encode($array);
                exit;
            } elseif (Hash::check($data['password'], $user_model['password'])) {
                $array = ([
                    'result' => 0,
                    'msg' => '新密码与原密码一致',
                    'data' => ''
                ]);
                echo json_encode($array);
                exit;
            }
            unset($data['old_password']);
        }
        if (request()->path() != "admin/user/edit") {
            $data['password'] = trim($data['password']);
            $data['re_password'] = trim($data['re_password']);
        }

        if (!$user_model) {
            $user_model = new self();
        }
        $user_model->fill($data);
        $validator = $user_model->validator();
        return [
            'validator' => $validator,
            'user_model' => $user_model
        ];
    }

    public function atributeNames()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            're_password' => '确认密码',
            'phone' => '手机号',
            'application_number' => '创建平台数量',
            'effective_time' => '有效期',
            'remarks' => '备注',
        ];
    }

    public function rules()
    {
        $rules = [];
        if (request()->path() != "admin/user/change") {
            $rules = [
                'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u',
                'phone' => 'required|regex:/^1[34578]\d{9}$/',
            ];
        }

        if (request()->path() != "admin/user/edit") {
            $rules['password'] = 'required';
            $rules['re_password'] = 'same:password';
        }
        return $rules;
    }

    /**
     * 读取所有数据
     * @return \app\framework\Database\Eloquent\Collection
     */
    public static function getList()
    {
        $users = self::get();
        foreach ($users as $item) {
            $item['create_at'] = $item['created_at']->format('Y-m-d');
            if ($item['effective_time'] == 0) {
                $item['effective_time'] = '永久有效';
            }else {
                if (time() > $item['effective_time']) {
                    $item['status'] = 1;
                    AdminUser::where('id', $item['id'])->update(['status'=>1]);
                }
                $item['effective_time'] = date('Y-m-d', $item['effective_time']);
            }
        }

        return $users;
    }
}