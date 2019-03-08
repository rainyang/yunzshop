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

class AdminUser extends BaseModel
{
    public $table = 'yz_admin_user';
    public $timestamps = true;
    protected $guarded = [''];

    /**
     * 保存数据
     * @param string $data
     * @param string $key
     * @param $cache_name
     * @return SystemSetting|bool
     */
    public function saveData($data, $user_model)
    {
        $verify_res = self::verifyData($data, $user_model);
        if ($verify_res['validator']->fails()) {
            echo $this->errorJson($verify_res['validator']->messages());
            exit;
        }
        unset($verify_res['user_model']['repassword']);
        $result = $verify_res['user_model']->save();
        if ($result) {
            Cache::put('admin_user', $data, 3600);
            return $this->successJson('成功', '');
        } else {
            return $this->errorJson('失败', '');
        }
    }

    /**
     * 读取数据
     * @param string $key
     * @param string $cache_name
     * @return SystemSetting
     */
    /**
     * @param $id
     */
    public function getData($id)
    {

       return  self::find($id);

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
     * @param $hotel_data
     * @param string $hotel_model
     * @return array
     */
    public static function verifyData($data, $user_model = '')
    {
        $data['username'] = trim($data['username']);
        if ($data['password']) {
            $data['password'] = trim($data['password']);
            $data['repassword'] = trim($data['repassword']);
        }
        $data['phone'] = trim($data['phone']);
        if ($data['application_number'] == 0) {
            $data['application_number'] = '';
        }
        if ($data['effective_time'] == 0) {
            $data['effective_time'] = '';
        } else {
            $data['effective_time'] = strtotime("+".$data['effective_time']."day");
        }

        $data['remarks'] = trim($data['remarks']);

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
            'repassword' => '确认密码',
            'phone' => '手机号',
            'application_number' => '创建平台数量',
            'effective_time' => '有效期',
            'remarks' => '备注',
        ];
    }

    public function rules()
    {
        $rules = [
            'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u',
            'phone' => 'required|regex:/^1[34578]\d{9}$/',
        ];

        if (request()->path() != "admin/user/edit") {
            $rules['password'] = 'required';
            $rules['repassword'] = 'same:password';
        }
        return $rules;
    }

    public function getList()
    {
        return self::get();
    }
}