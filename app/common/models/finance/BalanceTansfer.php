<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 下午2:25
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额转让记录
 *
 * */
class BalanceTansfer extends BaseModel
{
    public $table = 'yz_balance_tansfer';

    public $timestamps = false;

    protected $guarded = [''];

    /*
     * 关联会员数据表，一对一
     * */
    public function transferorInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'transferor');
    }

    /*
     * 关联会员数据表，一对一
     * */
    public function recipientInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'recipient');
    }

    /*
     * 获取余额转让记录分页列表，后台使用
     *
     * @return objece */
    public static function getTansferPageList($pageSize)
    {
        return self::uniacid()
            ->with(['transferorInfo' => function($transferorInfo) {
                return $transferorInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
            }])
            ->with(['recipientInfo' => function($recipientInfo) {
                return $recipientInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
            }])
            ->orderBy('created_at')->paginate($pageSize);
    }

}
