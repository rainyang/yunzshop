<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午3:50
 */

namespace app\Jobs;


use app\backend\modules\member\models\Member;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class memberParentOfMemberJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;
    private $member_info;
    public  $memberModel;
    public  $childMemberModel;

    public function __construct($uniacid, $member_info)
    {
        $this->uniacid = $uniacid;
        $this->member_info = $member_info;
    }


    public function handle()
    {
        \Log::debug('-----queue uniacid-----', $this->uniacid);
        \Log::debug('-----queue member count-----', count($this->member_info));
        return $this->synRun($this->uniacid);
    }

    public function synRun($uniacid)
    {
        $parentMemberModle = new ParentOfMember();
        $childMemberModel = new ChildrenOfMember();
        $memberModel = new Member();
        $memberModel->_allNodes = collect([]);

        \Log::debug('--------------清空表数据------------');
        //$parentMemberModle->DeletedData();

        $memberInfo = $memberModel->getTreeAllNodes($uniacid);

        if ($memberInfo->isEmpty()) {
            \Log::debug('----is empty-----');
            return;
        }

        foreach ($memberInfo as $item) {
            $memberModel->_allNodes->put($item->member_id, $item);
        }

        \Log::debug('--------queue synRun -----');

        foreach ($this->memberInfo as $key => $val) {
            $attr = [];
            $child_attr = [];

            \Log::debug('--------foreach start------', $val->member_id);
            $data = $memberModel->getNodeParents($uniacid, $val->member_id);

            if (!$data->isEmpty()) {
                \Log::debug('--------insert init------');

                foreach ($data as $k => $v) {
                    if ($k != $val->member_id) {
                        $attr[] = [
                            'uniacid'   => $uniacid,
                            'parent_id'  => $k,
                            'level'     => $v['depth'] + 1,
                            'member_id' => $val->member_id,
                            'created_at' => time()
                        ];

                        $child_attr[] = [
                            'uniacid'   => $uniacid,
                            'child_id'  => $val->member_id,
                            'level'     => $v['depth'] + 1,
                            'member_id' => $k,
                            'created_at' => time()
                        ];
                    } else {
                        file_put_contents(storage_path("logs/" . date('Y-m-d') . "_batchparent.log"), print_r([$val->member_id, $v], 1), FILE_APPEND);
                    }
                }

                $parentMemberModle->createData($attr);
                $childMemberModel->createData($child_attr);
            }
        }
    }
}