<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额变动记录表
 *
 * */
class Balance extends BaseModel
{
    public $table = 'yz_balance';

    public $timestamps = false;

    /*
     * 模型管理，关联会员数据表
     *
     * @Author yitian */
    public function member()
    {
        return $this->hasOne('app\common\models\member', 'uid', 'member_id');
    }

    /*
     * 获取分页列表
     *
     * @params int $pageSize
     *
     * @return object
     * @Autho yitian */
    public static function getPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query) {
                return $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
    }



}