<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午3:50
 */

namespace app\Jobs;


use app\backend\modules\member\models\Member;
use app\common\models\member\ChildenOfMember;
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
        $this->member_info = $member_info->toArray();
    }

    public function handle()
    {
        \Log::debug('-----queque uniacid-----', $this->uniacid);
        return $this->synRun($this->uniacid, $this->member_info);
    }

    public function synRun($uniacid, $memberInfo)
    {
        $memberModel = new Member();
        $childMemberModel = new ChildenOfMember();

       /* \Log::debug('--------queue member_model -----', get_class($this->memberModel));
        \Log::debug('--------queue childMemberModel -----', get_class($this->childMemberModel));*/
        \Log::debug('--------queue cccccc -----');
        foreach ($memberInfo as $key => $val) {
            \Log::debug('--------queue 22222-----');
            $data = $memberModel->getDescendants($uniacid, 65)->toArray();
            \Log::debug('--------queue 3333-----');

            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    $attr[] = [
                        'uniacid'   => $uniacid,
                        'child_id'  => $k,
                        'level'     => $v['depth'] + 1,
                        'member_id' => $val['uid']
                    ];
                }

                $childMemberModel->createData($attr);
            }
        }

    }
}