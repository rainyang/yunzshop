<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/24
 * Time: 上午6:32
 */

namespace app\Jobs;


use app\backend\modules\member\models\Member;
use app\common\models\member\ChildrenOfMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class memberChildOfMemberJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;
    private $member_info;
    public  $memberModel;
    public  $childMemberModel;

    public $timeout = 3000;

    public function __construct($uniacid)
    {
        $this->uniacid = $uniacid;
    }

    public function handle()
    {
        \Log::debug('-----queue uniacid-----', $this->uniacid);

        return $this->synRun($this->uniacid);
    }

    public function synRun($uniacid)
    {
        $childMemberModel = new ChildrenOfMember();
        $memberModel = new Member();
        $memberModel->_allNodes = collect([]);

        $memberInfo = $memberModel->getTreeAllNodes($uniacid);

        if ($memberInfo->isEmpty()) {
            \Log::debug('----is empty-----');
            return;
        }


        foreach ($memberInfo as $item) {
            $memberModel->_allNodes->put($item->member_id, $item);
        }

        \Log::debug('--------queue synRun -----');

        foreach ($memberInfo as $key => $val) {
            $attr = [];

            \Log::debug('--------foreach start------', $val->member_id);
            $data = $memberModel->getDescendants($uniacid, $val->member_id);
            \Log::debug('--------foreach data------', $data->count());

            if (!$data->isEmpty()) {
                \Log::debug('--------insert init------');
                $data = $data->toArray();
                foreach ($data as $k => $v) {
                    $attr[] = [
                        'uniacid'   => $uniacid,
                        'child_id'  => $k,
                        'level'     => $v['depth'] + 1,
                        'member_id' => $val->member_id,
                        'created_at' => time()
                    ];
                }

                $childMemberModel->createData($attr);
            }
        }

    }
}