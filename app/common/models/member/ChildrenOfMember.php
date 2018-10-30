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

class ChildrenOfMember extends BaseModel
{
    public $table = 'yz_member_children';
    protected $guarded = [];

    public function CreateData($data)
    {
        \Log::debug('----------insert data-----');
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }

    public function getMemberByDepth($uid, $depth)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('level', $depth)
            ->orderBy('level')
            ->get();
    }

    public function getChildOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function addNewChildData(ParentOfMember $parentObj, $uid, $parent_id)
    {
        $parents = $parentObj->getParentOfMember($parent_id);
        $parents_ids = [];
        $attr        = [];

        if (!empty($parents)) {
            foreach ($parents as $val) {
                $parents_ids[] = $val['parent_id'];
            }
        }

        $parents_ids[] = $parent_id;

        $item = $this->countSubChildOfMember($parents_ids);

        foreach ($item as $rows) {
            $attr[] = [
                'child_id' => $uid,
                'level'    => ++$rows['user_count'],
                'member_id' => $rows['member_id']
            ];
        }

        $this->CreateData($attr);
    }

    public function countSubChildOfMember($uid)
    {
        return self::uniacid()
            ->select(DB::raw('count(1) as user_count, member_id'))
            ->whereIn('member_id', $uid)
            ->groupBy('member_id')
            ->get();
    }

    public function delRelationOfParentByMemberId($parent_id, $uid)
    {
        return self::uniacid()
            ->where('member_id', $parent_id)
            ->whereAnd('child_id', $uid)
            ->delete();
    }

    public function delRelation($uid)
    {
        return self::uniacid()
            ->whereIn('member_id', $uid)
            ->delete();
    }

    public function delMemberOfRelation(ParentOfMember $parentObj, $uid)
    {
        $parents = $parentObj->getParentOfMember($uid);
        $childs  = $this->getChildOfMember($uid);

        foreach ($childs as $val) {
            $this->delRelation($val['member_id']);
        }

        foreach ($parents as $val) {
            $this->delRelationOfParentByMemberId($val['member_id'], $uid);
        }
    }
}