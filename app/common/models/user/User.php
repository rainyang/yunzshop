<?php
/**
 * Created by PhpStorm.
 * User: yitian
 * Date: 02/03/2017
 * Time: 18:19
 */

namespace app\common\models\user;


use app\backend\modules\user\observers\UserObserver;
use app\common\models\BaseModel;
use Illuminate\Validation\Rule;

class User extends BaseModel
{
    public $table = 'users';

    public $timestamps = false;

    public $fillable = [];

    public $widgets =[];

    public $attributes = [
        'groupid' => 0 ,
        'type' => 0,
        'remark' => '',
        'endtime' => 0
    ];
    protected $guarded = [''];

    public function uniAccounts()
    {
        return $this->hasMany('app\common\models\user\UniAccountUser', 'uid', 'uid');
    }

    /*
     *  One to one, each operator corresponds to an operator
     **/
    public function userProfile()
    {
        return $this->hasOne('app\common\models\user\UserProfile', 'uid', 'uid');
    }

    /*
     *  One to one, account each operator corresponds to an operator
     **/
    public function uniAccount()
    {
        return $this->belongsTo('app\common\models\user\UniAccountUser', 'uid', 'uid');
    }

    /*
     *  One to one, one operator has only one character
     **/
    public function userRole()
    {
        return $this->hasOne('app\common\models\user\YzUserRole', 'user_id', 'uid');
    }

    /*
     *  One to many, one operator has multiple operating privileges
     **/
    public function permissions()
    {
        return $this->hasMany('app\common\models\user\YzPermission', 'item_id', 'uid')
            ->where('type', '=', YzPermission::TYPE_USER);
    }


    public static function getPageList($pageSize = 20)
    {
        return self::whereHas('uniAccount', function($query){
            return $query->uniacid();
        })
        ->with(['userProfile' => function($profile) {
            return $profile->select('uid', 'realname', 'mobile');
        }])
        ->with(['userRole' => function($userRole) {
            return $userRole->select('user_id', 'role_id')
                ->with(['role' => function ($role) {
                    return $role->select('id', 'name')->uniacid();
            }]);
        }])
        ->paginate($pageSize);

    }

    protected static function checkUserName($userName)
    {
        $user = static::select('uid')->where('username', '=', $userName)->first();
        //var_dump(empty($user));
        return empty($user) ? '' : '1';
    }

    /**
     * 数据库获取用户权限
     * @return mixed
     */
    public static function getUserPermissionsCache()
    {
        $key = 'user.permissions.'.\YunShop::app()->uid;
        $list = \Cache::get($key);
        if($list === null){
            $list =  static::select(['uid'])
                ->where(['uid' => \YunShop::app()->uid])
                ->where('type','!=', '1')
                ->with([
                    'userRoles' => function ($query) {
                        return $query->select(['user_id','role_id'])
                            ->with(['permissions']);
                    },
                    'permissions'
                ])
                ->get();

            \Cache::put($key,$list,3600);
        }

        return $list;
    }

    /**
     * 获取并组合用户权限
     * @return array
     */
    public static function getAllPermissions()
    {
        $userPermissions = self::getUserPermissionsCache()->toArray();
        $permissions = [];
        if($userPermissions) {
            foreach ($userPermissions as $v) {
                if ($v['permissions']) {
                    foreach ($v['permissions'] as $v1) {
                        $permissions[] = $v1['permission'];
                    }
                }
                if ($v['user_roles']) {
                    foreach ($v['user_roles'] as $v2) {
                        if ($v2['permissions']) {
                            foreach ($v2['permissions'] as $v3) {
                                !in_array($v3['permission'], $permissions) && $permissions[] = $v3['permission'];
                            }
                        }
                    }
                }
            }
        }

        return $permissions;
    }

    /**
    * 定义字段名
    *
    * @return array */
    public  function atributeNames() {
        return [
            'username'=> "操作员用户名",
            'password' => "操作员密码"
        ];
    }
    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'username' => ['required',Rule::unique($this->table)->ignore($this->id)],
            'password' => 'required'
        ];
    }

    /**
     * 在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();


        //注册观察者
        static::observe(new UserObserver());
    }

}
