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

class OrderCountModel extends BaseModel
{
    public $table = 'yz_order_count';
    public $guarded = [];

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
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