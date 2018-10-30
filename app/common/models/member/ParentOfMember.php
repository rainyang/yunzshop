<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午5:29
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use Illuminate\Support\Facades\DB;

class ParentOfMember extends BaseModel
{
    public $table = 'yz_member_parent';
    protected $guarded = [];

    public function CreateData($data)
    {
        \Log::debug('----------insert data-----');
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }

    public function getParentOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function getMemberIdByParent($parent_id)
    {
        return self::uniacid()
            ->where('parent_id', $parent_id)
            ->pluck('member_id');
    }

    public function delRelation($member_ids)
    {
        return self::uniacid()
            ->whereIn('member_id', $member_ids)
            ->delete();
    }

    public function addNewParentData($uid, $parent_id)
    {
        $attr = [];
        $depth = 1;
        $parents = $this->getParentOfMember($parent_id);

        if (!empty($parents)) {
            foreach ($parents as $key => $val) {
                $attr[] = [
                    'parent_id' => $val['parent_id'],
                    'level'     => $val['level'],
                    'member_id' => $uid
                ];

                ++$depth;
            }
        }


        $attr[] = [
            'parent_id' => $parent_id,
            'level'     => $depth,
            'member_id' => $uid
        ];

        $this->CreateData($attr);
    }

    public function delMemberOfRelation($uid)
    {
        $member_ids = $this->getMemberIdByParent($uid);

        $this->delRelation($member_ids);
        $this->delRelation($uid);

    }
}