<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午4:51
 */

namespace app\platform\modules\user\models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use app\platform\controllers\BaseController;
use app\common\helpers\Cache;
use Illuminate\Support\Facades\Hash;
use app\common\events\UserActionEvent;

class AdminUser extends Authenticatable
{
    use Notifiable;
    public $primaryKey = 'uid';
    protected $table = 'yz_admin_users';
    public $timestamps = true;
    protected $guarded = [''];
    protected $dateFormat = 'U';
    public static $base = '';

    public function __construct()
    {
        self::$base = new BaseController;
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    //用户角色
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'yz_admin_role_user', 'user_id', 'role_id');
    }

    // 判断用户是否具有某个角色
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role); // ?
        }

        return !!$role->intersect($this->roles)->count();
    }

    // 判断用户是否具有某权限
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
            if (!$permission) {
                return false;
            }
        }

        return $this->hasRole($permission->roles);
    }

    // 给用户分配角色
    public function assignRole($role)
    {
        return $this->roles()->save($role);
    }

    //角色整体添加与修改
    public function giveRoleTo(array $RoleId)
    {
        $this->roles()->detach();
        $roles = Role::whereIn('id', $RoleId)->get();
        foreach ($roles as $v) {
            $this->assignRole($v);
        }
        return true;
    }

    /**
     * 保存数据
     * @param $data
     * @param string $user_model
     * @return mixed
     */
    public static function saveData($data, $user_model)
    {
        $verify_res = self::verifyData($data, $user_model);

        if ($verify_res['re_password']) {
            $verify_res['password'] = bcrypt($verify_res['password']);
            unset($verify_res['re_password']);
        }
        $result = $verify_res->save();
        if ($result) {
            self::saveProfile($data, $verify_res);
//            Cache::put('admin_users', $data, 3600);
            echo self::$base->successJson('成功'); exit;
        } else {
            echo self::$base->errorJson('失败'); exit;
        }
    }

    /**
     * 整合数据
     * @param $data
     * @param string $user_model
     * @return AdminUser|string
     */
    public static function verifyData($data, $user_model = '')
    {
        if (request()->path() != "admin/user/change") {
            $data['username'] = trim($data['username']);
            if ($data['application_number'] == 0) {
                $data['application_number'] = '';
            }
            if ($data['endtime'] == 0) {
                $data['endtime'] = '';
            } else {
//                $data['endtime'] = strtotime($data['endtime']);
            }
            $data['remark'] = trim($data['remark']);
        } else {
            $data['old_password'] = trim($data['old_password']);
            if (!Hash::check($data['old_password'], $user_model['password'])) {
                echo self::$base->errorJson('原密码错误'); exit;
            } elseif (Hash::check($data['password'], $user_model['password'])) {
                echo self::$base->errorJson('新密码与原密码一致'); exit;
            }
            unset($data['old_password']);
        }
        if (request()->path() != "admin/user/edit") {
            $data['password'] = trim($data['password']);
            $data['re_password'] = trim($data['re_password']);
        }
        $data['lastvisit'] =time();
        $data['lastip'] = getIp();
        $data['joinip'] = getIp();
        $data['salt'] = self::randNum(8);

        if (!$user_model) {
            $user_model = new self();
        }
        $user_model->fill($data);
        unset($user_model['mobile']);

        return $user_model;
    }

    /**
     * 读取所有数据
     * @return \app\framework\Database\Eloquent\Collection
     */
    public static function getList()
    {
        $users = self::orderBy('uid', 'desc')->get();
        foreach ($users as $item) {
            $item['create_at'] = $item['created_at']->format('Y-m-d');
            if ($item['endtime'] == 0) {
                $item['endtime'] = '永久有效';
            }else {
                if (time() > $item['endtime']) {
                    $item['status'] = 1;
                    self::where('uid', $item['uid'])->update(['status'=>1]);
                }
                $item['endtime'] = date('Y-m-d', $item['endtime']);
            }
        }

        return $users;
    }

    /**
     * 读取单条数据
     * @param $id
     * @return AdminUser
     */
    public static function getData($uid)
    {

        return self::find($uid);

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
     * 检索会员信息
     *
     * @param $parame
     * @return mixed
     */
    public static function searchUsers($parame, $credit = null)
    {
        if (!isset($credit)) {
            $credit = 'credit2';
        }
        $result = self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid();

        if (!empty($parame['search']['mid'])) {
            $result = $result->where('uid', $parame['search']['mid']);
        }
        if (isset($parame['search']['searchtime']) && $parame['search']['searchtime'] == 1) {
            if ($parame['search']['times']['start'] != '请选择' && $parame['search']['times']['end'] != '请选择') {
                $range = [strtotime($parame['search']['times']['start']), strtotime($parame['search']['times']['end'])];
                $result = $result->whereBetween('createtime', $range);
            }
        }

        if (!empty($parame['search']['realname'])) {
            $result = $result->where(function ($w) use ($parame) {
                $w->where('nickname', 'like', '%' . $parame['search']['realname'] . '%')
                    ->orWhere('realname', 'like', '%' . $parame['search']['realname'] . '%')
                    ->orWhere('mobile', 'like', $parame['search']['realname'] . '%');
            });
        }

        if (!empty($parame['search']['groupid']) || !empty($parame['search']['level']) || $parame['search']['isblack'] != ''
            || $parame['search']['isagent'] != ''
        ) {

            $result = $result->whereHas('yzMember', function ($q) use ($parame) {
                if (!empty($parame['search']['groupid'])) {
                    $q = $q->where('group_id', $parame['search']['groupid']);
                }

                if (!empty($parame['search']['level'])) {
                    $q = $q->where('level_id', $parame['search']['level']);
                }

                if ($parame['search']['isblack'] != '') {
                    $q->where('is_black', $parame['search']['isblack']);
                }

                if ($parame['search']['isagent'] != '') {
                    $q->where('is_agent', $parame['search']['isagent']);
                }
            });
        }

        return $result;
    }

    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    private static function randNum($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    public static function saveProfile($data, $user)
    {
        if (request()->path() == "admin/user/create") {
            $data = [
                'mobile' => $data['mobile'],
                'uid' => $user->uid
            ];
            $profile_model = new YzUserProfile;
            $profile_model->fill($data);
            if (!$profile_model->save()) {
                echo self::$base->errorJson('存储相关信息表失败'); exit;
            }
            event(new UserActionEvent(self::class, $user['uid'], 1, '添加了用户' . $user['username']));
        } elseif (request()->path() == "admin/user/edit") {
            $data = [
                'mobile' => $data['mobile'],
            ];
            $profile_model = YzUserProfile::where('uid', $user->uid)->first();
            $profile_model->fill($data);
            if (!$profile_model->save()) {
                echo self::$base->errorJson('存储相关信息表失败'); exit;
            }
            event(new UserActionEvent(AdminUser::class, $user['uid'], 3, '编辑了用户' . $user['username']));
        }
    }
}