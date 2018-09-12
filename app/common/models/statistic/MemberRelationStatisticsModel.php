<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/7/30
 * Time: 13:55
 */

namespace app\common\models\statistic;


use app\common\models\BaseModel;
use app\common\models\Member;

class MemberRelationStatisticsModel extends BaseModel
{
    public $table = 'yz_member_relation_statistics';

    protected $fillable=['uniacid','member_id','first_total','second_total','third_total','team_total'];

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