<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/7/31
 * Time: 10:07
 */

namespace app\common\models\statistic;


use app\common\models\BaseModel;
use app\common\models\Member;

class MemberRelationOrderStatisticsModel extends BaseModel
{
    public $table = 'yz_member_relation_order_statistics';
    protected $fillable = [
        'uniacid','member_id',
        'first_order_quantity', 'first_order_amount',
        'second_order_quantity', 'second_order_amount',
        'third_order_amount', 'third_order_quantity',
        'first_scened_order_quantity', 'first_scened_order_amount',
        'first_scened_third_order_quantity', 'first_scened_third_order_amount',
        'team_order_quantity','team_order_amount',
    ];

    public function hasOneMember()
    {
        return $this->hasOne(Member::class,'uid', 'member_id');
    }

    public static function getMember($search)
    {
        $model = self::uniacid()->with('hasOneMember');

        if (!empty($search['member_id'])) {
            $model->whereHas('hasOneMember', function ($q) use($search) {
                $q->where('uid', $search['member_id']);
            });
        }

        if (!empty($search['member_info'])) {
            $model->whereHas('hasOneMember', function ($q) use($search) {
                $q->where('nickname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('realname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('mobile', 'like' , '%' . $search['member_info'] . '%');
            });
        }
        return $model;
    }
}