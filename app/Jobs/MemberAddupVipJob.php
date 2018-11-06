<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/11/4
 * Time: 下午3:44
 */

namespace app\Jobs;


use app\common\models\member\ParentOfMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Yunshop\Mryt\models\MrytMemberAddUpVipModel;
use Yunshop\Mryt\models\MrytMemberModel;

class MemberAddupVipJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $uniacid;
    protected $curr_date;

    public    $MrytMemberAddUpVipModel;
    public    $parentMemberModel;

    public function __construct($uid, $uniacid)
    {
        $this->uid = $uid;
        $this->uniacid = $uniacid;
        $this->curr_date = date('Ym', time());


        $this->MrytMemberAddUpVipModel = new MrytMemberAddUpVipModel();
        $this->parentMemberModel = new ParentOfMember();
    }

    public function handle()
    {
        $insert_ids = [];

         //查询uid所有父类id
        $parent = $this->parentMemberModel->getParentOfMember($this->uid);

        if (!$parent->isEmpty()) {
            foreach ($parent as $val) {
                $insert_ids[] = $val->parent_id;
            }

            //mrytVVIP会员
            $mryt_vvip_ids = MrytMemberModel::getMemberInfosWithLevel($insert_ids);

            $exists_parent = $this->MrytMemberAddUpVipModel->QueryCurrentMonthRecord($mryt_vvip_ids, $this->curr_date);

            if (!$exists_parent->isEmpty()) {
                foreach ($exists_parent as $item) {
                    $ExistsIds [] = $item->uid;
                }

                foreach ($mryt_vvip_ids as $id) {
                    if (!in_array($id, $ExistsIds)) {
                        $noExistsIds[] = $id;
                    }
                }

                DB::transaction(function () use ($ExistsIds, $noExistsIds) {
                    $this->UpdateDate($ExistsIds);
                    $this->InsertDate($noExistsIds);
                });
            } else {
                $this->InsertDate($mryt_vvip_ids);
            }
        }
    }

    public function InsertDate($no_exists_ids)
    {
        foreach ($no_exists_ids as $ids) {
            $attr[] = [
                'uniacid' => $this->uniacid,
                'uid'     => $ids,
                'nums'    => 1,
                'curr_date' => $this->curr_date,
                'created_at' => time(),
                'updated_at' => time()
            ];
        }

        $this->MrytMemberAddUpVipModel->CreateData($attr);
    }

    public function UpdateDate($exists_ids, $curr_date)
    {
        $this->MrytMemberAddUpVipModel->UpdateIncrementNums($exists_ids, $curr_date);
    }
}