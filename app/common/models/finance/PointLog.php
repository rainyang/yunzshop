<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/10
 * Time: 下午5:47
 */

namespace app\common\models\finance;


use app\backend\modules\member\models\Member;
use app\common\models\BaseModel;

class PointLog extends BaseModel
{
    public $table = 'yz_point_log';
    protected $guarded = [''];
    //搜索
    protected $search_fields = ['id'];

    public static function getPointLogList($search)
    {
        $list = PointLog::lists($search);
        return $list;
    }

    public function scopeLists($query, $search)
    {
        $query->search($search);
        $builder = $query->with([
            'hasOneMember'
        ]);
        return $builder;
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->uniacid();
        $query->orderBy('id', 'desc');
        if ($search['realname'] || $search['level_id'] || $search['group_id']) {
            $query = $query->whereHas('hasOneMember', function($member)use($search) {
                if ($search['realname']) {
                    $member = $member->select('uid', 'nickname','realname','mobile','avatar')
                        ->where('realname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('mobile', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('nickname', 'like', '%' . $search['realname'] . '%');
                }
                if ($search['level_id']) {
                    $member = $member->whereHas('yzMember', function ($level)use($search) {
                        $level->where('level_id', $search['level_id']);
                    });
                }
                if ($search['group_id']) {
                    $member = $member->whereHas('yzMember', function ($group)use($search) {
                        $group->where('group_id', $search['group_id']);
                    });
                }

            });
        }
        if ($search['searchtime']) {
            $query = $query->whereBetween('updated_at', [strtotime($search['time_range']['start']),strtotime($search['time_range']['end'])]);
        }
        return $query;
    }
}