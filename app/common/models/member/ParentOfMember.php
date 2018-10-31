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
    private $uniacid = 0;
    private $parents = [];

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

    public function delRelation(array $member_ids)
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

        $attr[] = [
            'uniacid'   => $this->uniacid,
            'parent_id' => $parent_id,
            'level'     => $depth,
            'member_id' => $uid,
            'created_at' => time()
        ];

        if (!empty($parents)) {
            foreach ($parents as $key => $val) {
                $attr[] = [
                    'uniacid'   => $this->uniacid,
                    'parent_id' => $val['parent_id'],
                    'level'     => ++$val['level'],
                    'member_id' => $uid,
                    'created_at' => time()
                ];
            }
        }

        $this->CreateData($attr);
    }

    public function delMemberOfRelation($uid)
    {
        $member_ids = $this->getMemberIdByParent($uid);

        if (!$member_ids->isEmpty()) {
            $this->delRelation($member_ids);
        }

        $this->delRelation([$uid]);

    }

    public function hasRelationOfParent($uid, $depth)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('level', $depth)
            ->count();
    }

    public function getParentsOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function getParents($uid)
    {
        $parents = $this->getParentsOfMember($uid);

        if (!is_null($parents)) {
            foreach ($parents as $val) {
                $this->parents[] = $val['parent_id'];
            }
        }
    }
}