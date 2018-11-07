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
    private $uniacid = 0;
    private $childrens = [];

    public function __construct(array $attributes = [])
    {
        $this->uniacid = \YunShop::app()->uniacid;

        parent::__construct($attributes);
    }

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
            ->orderBy('level')
            ->get();
    }

    public function addNewChildData(ParentOfMember $parentObj, $uid, $parent_id)
    {
        $parents = $parentObj->getParentOfMember($parent_id);
        $parents_ids = [];
        $attr = [];

        $parents_ids[] = $parent_id;

        if (!empty($parents)) {
            foreach ($parents as $val) {
                $parents_ids[$val['level']] = $val['parent_id'];
            }
        }
//dd($parents_ids);
        $parent_total = count($parents_ids);

        foreach ($parents_ids as $key => $ids) {
            $attr[] = [
                'uniacid' => $this->uniacid,
                'child_id' => $uid,
                'level' => ++$key,
                'member_id' => $ids,
                'created_at' => time()
            ];

           // dd($attr);
        }
//dd($attr);
        /*$item = $this->countSubChildOfMember($parents_ids);




        //$parents_ids = array_flip($parents_ids);

        //统计不为0的子级
        if (!empty($item)) {
            $parent_total = count($parents_ids);

            foreach ($item as $key => $rows) {
                if (in_array($rows['member_id'], $parents_ids)) {
                    $exists[] = $rows['member_id'];

                    $attr[] = [
                        'uniacid' => $this->uniacid,
                        'child_id' => $uid,
                        'level' => $parent_total - $key,
                        'member_id' => $rows['member_id'],
                        'created_at' => time()
                    ];
                }
            }
        }

        //统计为空的子级
        foreach ($parents_ids as $key => $ids) {
            if (!in_array($ids, $exists)) {
                $attr[] = [
                    'uniacid' => $this->uniacid,
                    'child_id' => $uid,
                    'level' => 1,
                    'member_id' => $ids,
                    'created_at' => time()
                ];
            }
        }*/

        //ksort($attr);
        $this->CreateData($attr);
    }

    public function countSubChildOfMember(array $uid)
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
            ->where('child_id', $uid)
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
        $childs = $this->getChildOfMember($uid);

        //删除重新分配节点本身在子表中原父级的记录
        if (!$parents->isEmpty()) {
            foreach ($parents as $val) {
                $this->delRelationOfParentByMemberId($val['parent_id'], $val['member_id']);
            }
        }

        //删除重新分配节点的子级在子表中原父级的记录
        if (!$childs->isEmpty()) {
            foreach ($childs as $val) {
                foreach ($parents as $rows) {
                    $this->delRelationOfParentByMemberId($rows['parent_id'], $val['child_id']);
                }
            }
        }

        //可优化
        //删除子节点本身
        if (!$childs->isEmpty()) {
            foreach ($childs as $val) {
                $this->delRelation($val['member_id']);
            }
        }
    }

    public function getChildrensOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function getChildrens($uid)
    {
        $childrens = $this->getChildOfMember($uid);

        if (!is_null($childrens)) {
            foreach ($childrens as $val) {
                $this->childrens[] = $val['child_id'];
            }
        }
    }
}