<?php
namespace app\backend\modules\user\observers;

use app\backend\modules\user\services\PermissionService;
use app\common\models\user\UniAccountUser;
use app\common\models\user\UserPermission;
use app\common\models\user\UserProfile;
use app\common\models\user\YzPermission;
use app\common\models\user\YzUserRole;
use app\common\observers\BaseObserver;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/17
 * Time: 上午10:56
 */
class UserObserver extends BaseObserver
{
    use MessageTrait;

    public function creating(Model $model)
    {
        $profile = (new UserProfile())->relationValidator($model->widgets['profile']);
        if($profile->fails()){
            $this->error($profile->messages());
            return false;
        }
        //未进行权限校验
    }

    public function created(Model $model) {
        $userProfile = (new UserProfile())->addUserProfile($model->widgets['profile'], $model);
        if (!$userProfile) {
            $this->error("操作员简介写入失败,请重试！！");
            return false;
        }
        $uniAccountUser = (new UniAccountUser())->addOperator($model->id);
        if (!$uniAccountUser) {
            $this->error("操作员写入失败,请重试！！");
            return false;
        }
        $userPermission = (new UserPermission())->addUserPermission($model->id);
        if (!$userPermission) {
            $this->error("操作员权限写入失败，请重试！！");
            return false;
        }
        if ($model->widgets['role_id']) {
            $yzUserRole = (new YzUserRole())->addUserRole($model->id, $model->widgets['role_id']);
            if (!$yzUserRole) {
                $this->error("关联权限写入失败，请重试！！");
                return false;
            }
        }
        $perms = (new PermissionService())->addedToPermission($model->widgets['perms'], YzPermission::TYPE_USER, $model->id);
        $yzPermission = (new YzPermission())->addYzPermission($perms);
        if (!$yzPermission) {
            $this->error("权限写入失败，请重试！！");
            return false;
        }
    }

    public function updating(Model $model) {}

    public function updated(Model $model) {}

    public function saving(Model $model) {}

    public function saved(Model $model) {}


    public function deleted(Model $model) {
        dd('hhahah');
        return false;
    }

    public function restoring(Model $model) {}

    public function restored(Model $model) {}
}