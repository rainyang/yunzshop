<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 上午9:32
 */

namespace app\backend\modules\member\models;

use app\backend\models\BackendModel;

class MemberRelation extends BackendModel
{
    public $table = 'yz_member_relation';

    public $timestamps = false;

    /**
     * 可以批量赋值的属性
     *
     * @var array
     */
    public $fillable = ['uniacid', 'status', 'become', 'become_order', 'become_child', 'become_ordercount',
        'become_moneycount', 'become_goods_id', 'become_info', 'become_check'];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    public $guarded = [];


    public static function getSetInfo()
    {
        return self::uniacid();
    }


    public function memberHasOneGoods(){

    }
}